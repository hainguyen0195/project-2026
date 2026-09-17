<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ProductMedia;
use App\Models\SiteSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhotoController extends Controller
{
    public function types(): JsonResponse
    {
        return response()->json(['data' => collect(config('photo.types'))->map(fn (array $definition, string $key): array => ['key' => $key, ...$definition])->values()]);
    }

    private function definition(string $type): array
    {
        $definition = config('photo.types')[$type] ?? null;
        abort_unless($definition, 404);

        return $definition;
    }

    private function payload(SiteSetting $photo): array
    {
        return ['id' => $photo->id, ...$photo->data, 'image' => ProductMedia::find($photo->data['image_id'] ?? null)];
    }

    public function index(string $type): JsonResponse
    {
        $this->definition($type);
        $items = SiteSetting::where('key', 'like', 'photo:'.$type.':%')->get()->sortBy(fn (SiteSetting $item): int => $item->data['sort_order'] ?? 0)->values();

        return response()->json(['data' => $items->map(fn (SiteSetting $item): array => $this->payload($item))]);
    }

    public function save(Request $request, string $type, ?SiteSetting $photo = null): JsonResponse
    {
        $definition = $this->definition($type);
        if ($photo) {
            abort_unless(str_starts_with($photo->key, 'photo:'.$type.':'), 404);
        }
        $fields = array_values(array_diff($definition['fields'], ['link']));
        $rules = [
            'image_id' => ['required', 'integer', 'exists:product_media,id'],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
            'translations' => ['sometimes', 'array:vi,en'],
            'translations.*' => ['array:'.implode(',', [...$fields, 'alt'])],
            'translations.*.alt' => ['nullable', 'string', 'max:255'],
        ];
        foreach ($fields as $field) {
            $rules['translations.*.'.$field] = ['nullable', 'string', 'max:5000'];
        }
        if (in_array('link', $definition['fields'], true)) {
            $rules['link'] = ['nullable', 'url:http,https', 'max:2048'];
        }
        $data = $request->validate($rules);
        if ($photo) {
            $photo->update(['data' => $data]);
        } elseif ($definition['singleton']) {
            $photo = SiteSetting::updateOrCreate(['key' => 'photo:'.$type.':single'], ['data' => $data]);
        } else {
            $photo = SiteSetting::create(['key' => 'photo:'.$type.':'.Str::uuid(), 'data' => $data]);
        }

        return response()->json(['data' => $this->payload($photo)]);
    }

    public function destroy(string $type, SiteSetting $photo): JsonResponse
    {
        $definition = $this->definition($type);
        abort_if($definition['singleton'], 422, 'Ảnh đơn chỉ được cập nhật hoặc ẩn.');
        abort_unless(str_starts_with($photo->key, 'photo:'.$type.':'), 404);
        $photo->delete();

        return response()->json(['deleted' => true]);
    }
}
