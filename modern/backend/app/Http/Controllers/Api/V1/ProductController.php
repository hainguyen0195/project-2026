<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Support\CatalogTypes;
use App\Support\ProductHtml;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductController extends Controller
{
    private const RELATIONS = ['category', 'brand', 'mainImage', 'sizes', 'images'];

    public function types(): JsonResponse
    {
        $kind = request()->is('api/v1/cms/seopage-types') ? 'seopage' : (request()->is('api/v1/cms/static-types') ? 'static' : (CatalogTypes::module(request()) === 'news' ? 'news' : 'catalog'));

        return response()->json(['data' => array_values(array_filter(CatalogTypes::all(), fn (array $definition): bool => $definition['kind'] === $kind))]);
    }

    public function index(Request $request): AnonymousResourceCollection
    {
        $definition = CatalogTypes::resolve($request);
        $filters = $request->validate(['search' => ['nullable', 'string', 'max:150'], 'category_id' => ['nullable', 'integer', 'exists:product_categories,id'], 'status' => ['nullable', Rule::in(['all', 'published', 'draft', 'trash', ...array_keys($definition['statuses'])])], 'page' => ['nullable', 'integer', 'min:1'], 'per_page' => ['nullable', 'integer', 'min:1', 'max:50']]);
        $query = Product::with(self::RELATIONS)->where('type', $definition['key']);
        $status = $filters['status'] ?? 'all';
        if ($status === 'trash') {
            $query->onlyTrashed();
        } elseif (in_array($status, ['published', 'draft'], true)) {
            $query->where('is_active', $status === 'published');
        } elseif (array_key_exists($status, $definition['statuses'])) {
            $query->where($status, true);
        }
        if (! empty($filters['search'])) {
            $query->where(fn ($nested) => $nested->where('name', 'like', '%'.$filters['search'].'%')->orWhere('code', 'like', '%'.$filters['search'].'%'));
        }
        if (! empty($filters['category_id'])) {
            $ids = [(int) $filters['category_id']];
            $nodes = ProductCategory::where('type', $definition['key'])->where('kind', 'category')->get(['id', 'parent_id']);
            for ($level = 0; $level < 4; $level++) {
                $ids = array_values(array_unique([...$ids, ...$nodes->whereIn('parent_id', $ids)->pluck('id')->all()]));
            }
            $query->whereIn('category_id', $ids);
        }

        return ProductResource::collection($query->orderBy('sort_order')->orderByDesc('id')->paginate($filters['per_page'] ?? 15));
    }

    public function show(Product $product): ProductResource
    {
        CatalogTypes::resolve(request(), $product);

        return new ProductResource($product->refresh()->load(self::RELATIONS));
    }

    public function store(SaveProductRequest $request, ProductHtml $html): ProductResource
    {
        $definition = CatalogTypes::resolve($request);
        if ($definition['singleton']) {
            return Cache::store('file')->lock('static-page:'.$definition['key'], 30)->block(5, function () use ($request, $html, $definition): ProductResource {
                if (Product::withTrashed()->where('type', $definition['key'])->exists()) {
                    throw ValidationException::withMessages(['type' => 'Trang tĩnh này đã tồn tại. Vui lòng tải lại để chỉnh sửa.']);
                }

                return $this->save($request, new Product, $html);
            });
        }

        return $this->save($request, new Product, $html);
    }

    public function update(SaveProductRequest $request, Product $product, ProductHtml $html): ProductResource
    {
        return $this->save($request, $product, $html);
    }

    private function save(SaveProductRequest $request, Product $product, ProductHtml $html): ProductResource
    {
        $definition = CatalogTypes::resolve($request, $product->exists ? $product : null);
        $data = $request->validated();
        foreach (['description', 'content', 'specifications'] as $field) {
            $data[$field] = $html->clean($data[$field] ?? '');
            if (isset($data['translations']['en'][$field])) {
                $data['translations']['en'][$field] = $html->clean($data['translations']['en'][$field]);
            }
        }
        if ($data['schema_mode'] !== 'custom') {
            $data['schema_data'] = null;
        }
        $data = CatalogTypes::productData($data, $definition, ! $product->exists);
        if (isset($data['translations']['en']) && is_array($data['translations']['en'])) {
            $data['translations']['en'] = array_replace($product->translations['en'] ?? [], $data['translations']['en']);
        }
        DB::transaction(function () use ($product, $data): void {
            if ($product->exists) {
                Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            }
            $product->fill(Arr::except($data, ['size_ids', 'images']))->save();
            if (array_key_exists('size_ids', $data)) {
                $product->sizes()->sync($data['size_ids']);
            }
            $images = [];
            foreach ($data['images'] ?? [] as $position => $image) {
                $images[$image['media_id']] = ['alt' => $image['alt'] ?? '', 'caption' => $image['caption'] ?? '', 'sort_order' => $position, 'is_active' => $image['is_active']];
            }
            if (array_key_exists('images', $data)) {
                $product->images()->sync($images);
            }
        });

        return new ProductResource($product->refresh()->load(self::RELATIONS));
    }

    public function updateStatus(Request $request, Product $product): ProductResource
    {
        $definition = CatalogTypes::resolve($request, $product);
        $data = $request->validate([
            'field' => ['required', Rule::in(array_keys($definition['statuses']))],
            'value' => ['required', 'boolean'],
        ]);
        DB::transaction(function () use ($product, $data): void {
            $current = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $current->update([$data['field'] => $data['value']]);
        });

        return new ProductResource($product->refresh()->load(self::RELATIONS));
    }

    public function destroy(Product $product): Response
    {
        $definition = CatalogTypes::resolve(request(), $product);
        abort_if($definition['singleton'], 403, 'Trang tĩnh chỉ được chỉnh sửa hoặc ẩn hiển thị.');
        $product->delete();

        return response()->noContent();
    }

    public function restore(int $id): ProductResource
    {
        $product = Product::onlyTrashed()->findOrFail($id);
        CatalogTypes::resolve(request(), $product);
        $product->restore();

        return new ProductResource($product->load(self::RELATIONS));
    }

    public function duplicate(Product $product): ProductResource
    {
        $definition = CatalogTypes::resolve(request(), $product);
        abort_unless($definition['features']['copy'], 403);
        $copy = DB::transaction(function () use ($product): Product {
            $copy = $product->replicate();
            $suffix = '-'.strtolower(Str::random(10));
            $copy->name = mb_substr($product->name, 0, 240).' (Bản sao)';
            $copy->slug = mb_substr($product->slug, 0, 240).$suffix;
            $translations = $copy->translations;
            if (! empty($translations['en']['slug'])) {
                $translations['en']['slug'] = mb_substr($translations['en']['slug'], 0, 240).$suffix;
                $copy->translations = $translations;
            }
            $copy->code = mb_substr($product->code, 0, 85).strtoupper($suffix);
            $copy->is_active = false;
            $copy->canonical_url = null;
            $copy->schema_mode = 'auto';
            $copy->schema_data = null;
            $copy->save();
            $copy->sizes()->sync($product->sizes->modelKeys());
            foreach ($product->images as $image) {
                $copy->images()->attach($image->id, ['alt' => $image->pivot->alt, 'caption' => $image->pivot->caption, 'sort_order' => $image->pivot->sort_order, 'is_active' => $image->pivot->is_active]);
            }

            return $copy;
        });

        return new ProductResource($copy->load(self::RELATIONS));
    }
}
