<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveProductCategoryRequest;
use App\Models\ProductCategory;
use App\Support\CatalogTypes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ProductCategoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $definition = CatalogTypes::resolve($request);
        $data = $request->validate(['kind' => ['nullable', Rule::in(['category', 'brand', 'size'])]]);

        return response()->json(['data' => ProductCategory::with('image')->where('type', $definition['key'])->whereIn('kind', CatalogTypes::kinds($definition))->when(isset($data['kind']), fn ($query) => $query->where('kind', $data['kind']))->orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function store(SaveProductCategoryRequest $request): JsonResponse
    {
        return $this->save($request, new ProductCategory);
    }

    public function update(SaveProductCategoryRequest $request, ProductCategory $category): JsonResponse
    {
        return $this->save($request, $category);
    }

    private function save(SaveProductCategoryRequest $request, ProductCategory $category): JsonResponse
    {
        $definition = CatalogTypes::resolve($request, $category->exists ? $category : null);
        $creating = ! $category->exists;
        $saved = DB::transaction(function () use ($request, $category, $definition): ProductCategory {
            $nodes = ProductCategory::where('type', $definition['key'])->orderBy('id')->lockForUpdate()->get()->keyBy('id');
            $data = $request->validated();
            if ($category->exists && $category->kind !== $data['kind']) {
                throw ValidationException::withMessages(['kind' => 'Không được thay đổi loại danh mục đã tạo.']);
            }
            if ($data['kind'] !== 'category' && ! empty($data['parent_id'])) {
                throw ValidationException::withMessages(['parent_id' => 'Hãng và kích thước không có cấp cha.']);
            }
            $category->fill($data);
            $nodes->put($category->id ?? 0, $category);
            foreach ($nodes as $node) {
                $seen = [];
                $depth = 1;
                $cursor = $node;
                while ($cursor->parent_id !== null) {
                    if (isset($seen[$cursor->parent_id]) || ++$depth > $definition['category_depth']) {
                        throw ValidationException::withMessages(['parent_id' => 'Danh mục tối đa '.$definition['category_depth'].' cấp và không được tạo vòng lặp.']);
                    }
                    $seen[$cursor->parent_id] = true;
                    $cursor = $nodes->get($cursor->parent_id);
                    if (! $cursor) {
                        throw ValidationException::withMessages(['parent_id' => 'Danh mục cha không tồn tại.']);
                    }
                }
            }
            $category->save();

            return $category->load('image');
        });

        return response()->json(['data' => $saved], $creating ? 201 : 200);
    }

    public function destroy(ProductCategory $category): Response
    {
        CatalogTypes::resolve(request(), $category);
        DB::transaction(function () use ($category): void {
            ProductCategory::orderBy('id')->lockForUpdate()->get();
            $used = ProductCategory::where('parent_id', $category->id)->exists()
                || DB::table('products')->where('category_id', $category->id)->orWhere('brand_id', $category->id)->exists()
                || DB::table('product_size')->where('product_category_id', $category->id)->exists();
            if ($used) {
                throw ValidationException::withMessages(['category' => 'Không thể xóa: còn danh mục con hoặc sản phẩm đang sử dụng (kể cả thùng rác).']);
            }
            $category->delete();
        });

        return response()->noContent();
    }
}
