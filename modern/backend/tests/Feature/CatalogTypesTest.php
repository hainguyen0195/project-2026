<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CatalogTypesTest extends TestCase
{
    use RefreshDatabase;

    private function login(array $permissions = ['products.view', 'products.manage']): void
    {
        $this->app['auth']->forgetGuards();
        $role = Role::factory()->create(['permissions' => $permissions]);
        $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true, 'must_change_password' => false]);
        $this->actingAs($user, 'web')->withSession(['cms_auth_version' => 0]);
    }

    private function payload(string $type = 'san-pham'): array
    {
        return ['type' => $type, 'name' => 'Example', 'slug' => 'example', 'code' => 'EXAMPLE', 'regular_price' => '1000', 'sale_price' => null, 'availability' => 'in_stock', 'size_ids' => [], 'images' => [], 'noindex' => false, 'schema_mode' => 'auto', 'is_active' => true, 'is_featured' => false, 'is_new' => false, 'is_bestseller' => false, 'sort_order' => 0, 'translations' => ['en' => ['name' => 'English example', 'slug' => 'english-example']]];
    }

    public function test_types_reuse_the_template_and_scope_lists_and_identifiers(): void
    {
        $this->login();
        $this->getJson('/api/v1/cms/product-types')->assertOk()->assertJsonPath('data.0.key', 'san-pham')->assertJsonPath('data.1.features.pricing', false)->assertJsonPath('data.1.category_depth', 1);
        $first = $this->postJson('/api/v1/cms/products', $this->payload())->assertCreated();
        $second = $this->postJson('/api/v1/cms/products', $this->payload('thu-vien-anh'))->assertCreated()->assertJsonPath('data.type', 'thu-vien-anh')->assertJsonPath('data.schema_mode', 'disabled')->assertJsonPath('data.resolved_schema', null)->assertJsonPath('data.regular_price', '0.00');
        $this->getJson('/api/v1/cms/products')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $first->json('data.id'));
        $this->getJson('/api/v1/cms/products?type=thu-vien-anh')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $second->json('data.id'));
        $this->postJson('/api/v1/cms/products', $this->payload())->assertUnprocessable()->assertJsonValidationErrors(['slug', 'code', 'translations.en.slug']);
        $this->getJson('/api/v1/cms/products?type=invalid')->assertUnprocessable();
        $gallery = $this->payload('thu-vien-anh');
        $gallery['slug'] = 'no-code';
        $gallery['code'] = '';
        $gallery['translations'] = [];
        $this->postJson('/api/v1/cms/products', $gallery)->assertCreated();
    }

    public function test_status_filters_follow_type_configuration(): void
    {
        $this->login();
        foreach (['is_active', 'is_featured', 'is_new', 'is_bestseller'] as $field) {
            $flags = array_fill_keys(['is_active', 'is_featured', 'is_new', 'is_bestseller'], false);
            $product = Product::factory()->create([...$flags, $field => true]);
            $this->getJson('/api/v1/cms/products?status='.$field)->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $product->id);
            $product->delete();
            $this->getJson('/api/v1/cms/products?status='.$field)->assertOk()->assertJsonCount(0, 'data');
        }
        Product::factory()->create(['type' => 'san-pham', 'is_featured' => true]);
        $gallery = Product::factory()->create(['type' => 'thu-vien-anh', 'is_featured' => true]);
        $this->getJson('/api/v1/cms/products?type=thu-vien-anh&status=is_featured')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $gallery->id);
        $this->getJson('/api/v1/cms/products?type=thu-vien-anh&status=is_bestseller')->assertUnprocessable()->assertJsonValidationErrors('status');
        $this->getJson('/api/v1/cms/products?status=unknown')->assertUnprocessable()->assertJsonValidationErrors('status');
    }

    public function test_wrong_type_cannot_read_update_delete_duplicate_restore_or_toggle(): void
    {
        $this->login();
        $product = Product::factory()->create();
        $path = '/api/v1/cms/products/'.$product->id;
        $this->getJson($path.'?type=thu-vien-anh')->assertNotFound();
        $this->putJson($path, $this->payload('thu-vien-anh'))->assertNotFound();
        $this->deleteJson($path.'?type=thu-vien-anh')->assertNotFound();
        $this->postJson($path.'/duplicate?type=thu-vien-anh')->assertNotFound();
        $this->patchJson($path.'/status?type=thu-vien-anh', ['field' => 'is_active', 'value' => true])->assertNotFound();
        $product->delete();
        $this->postJson($path.'/restore?type=thu-vien-anh')->assertNotFound();
        $this->postJson($path.'/restore')->assertOk();
    }

    public function test_category_relationships_and_depth_are_scoped_to_type(): void
    {
        $this->login();
        $category = ProductCategory::factory()->create();
        $payload = ['type' => 'thu-vien-anh', 'kind' => 'category', 'name' => 'Gallery', 'slug' => $category->slug, 'is_active' => true, 'is_featured' => false, 'sort_order' => 0];
        $created = $this->postJson('/api/v1/cms/product-categories', $payload)->assertCreated();
        $this->getJson('/api/v1/cms/product-categories?type=thu-vien-anh')->assertOk()->assertJsonCount(1, 'data');
        $this->postJson('/api/v1/cms/product-categories', [...$payload, 'slug' => 'child', 'parent_id' => $category->id])->assertUnprocessable()->assertJsonValidationErrors('parent_id');
        $this->postJson('/api/v1/cms/product-categories', [...$payload, 'slug' => 'child', 'parent_id' => $created->json('data.id')])->assertUnprocessable()->assertJsonValidationErrors('parent_id');
        $this->postJson('/api/v1/cms/product-categories', [...$payload, 'kind' => 'brand'])->assertUnprocessable()->assertJsonValidationErrors('kind');
        $this->postJson('/api/v1/cms/products', [...$this->payload('thu-vien-anh'), 'category_id' => $category->id])->assertUnprocessable()->assertJsonValidationErrors('category_id');
        $brand = ProductCategory::factory()->create(['kind' => 'brand']);
        $size = ProductCategory::factory()->create(['kind' => 'size']);
        $this->postJson('/api/v1/cms/products', [...$this->payload('thu-vien-anh'), 'brand_id' => $brand->id, 'size_ids' => [$size->id]])->assertUnprocessable()->assertJsonValidationErrors(['brand_id', 'size_ids.0']);
        $this->deleteJson('/api/v1/cms/product-categories/'.$category->id.'?type=thu-vien-anh')->assertNotFound();
        $this->putJson('/api/v1/cms/product-categories/'.$category->id, $payload)->assertNotFound();
    }

    public function test_feature_flags_and_permissions_are_enforced_by_backend(): void
    {
        $this->login();
        config(['catalog.types.thu-vien-anh.features.copy' => false]);
        $product = Product::factory()->create(['type' => 'thu-vien-anh', 'content' => 'Keep content', 'regular_price' => 123]);
        $path = '/api/v1/cms/products/'.$product->id;
        $this->patchJson($path.'/status?type=thu-vien-anh', ['field' => 'is_bestseller', 'value' => true])->assertUnprocessable();
        $this->postJson($path.'/duplicate?type=thu-vien-anh')->assertForbidden();
        $this->putJson($path, $this->payload('thu-vien-anh'))->assertOk()->assertJsonPath('data.regular_price', '123.00');
        $this->login(['products.view']);
        $this->postJson('/api/v1/cms/products', $this->payload('thu-vien-anh'))->assertForbidden();
        $this->getJson('/api/v1/cms/product-types')->assertOk();
    }

    public function test_news_reuses_catalog_with_separate_configuration_and_permissions(): void
    {
        Storage::fake('local');
        $this->login(['content.view', 'content.manage']);
        $this->getJson('/api/v1/cms/news-types')->assertOk()->assertJsonPath('data.0.key', 'tin-tuc')->assertJsonPath('data.0.module', 'news')->assertJsonPath('data.0.features.pricing', false);
        $this->getJson('/api/v1/cms/products')->assertForbidden();
        $category = $this->postJson('/api/v1/cms/news-categories', ['name' => 'News category', 'slug' => 'news-category', 'kind' => 'category', 'is_active' => true, 'is_featured' => false, 'sort_order' => 0])->assertCreated()->json('data.id');
        $payload = [...$this->payload('tin-tuc'), 'code' => '', 'category_id' => $category, 'description' => 'Summary', 'content' => '<p>Article body</p>', 'seo_title' => 'News SEO'];
        $created = $this->postJson('/api/v1/cms/news', $payload)->assertCreated()->assertJsonPath('data.type', 'tin-tuc')->assertJsonPath('data.regular_price', '0.00')->assertJsonPath('data.schema_mode', 'disabled')->assertJsonPath('data.translations.en.slug', 'english-example');
        $path = '/api/v1/cms/news/'.$created->json('data.id');
        $this->getJson('/api/v1/cms/news')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson($path)->assertOk()->assertJsonPath('data.seo_title', 'News SEO');
        $this->putJson($path, [...$payload, 'name' => 'Updated article'])->assertOk()->assertJsonPath('data.name', 'Updated article');
        $this->patchJson($path.'/status', ['field' => 'is_featured', 'value' => true])->assertOk();
        $this->getJson('/api/v1/cms/news?status=is_featured')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/cms/news?status=is_bestseller')->assertUnprocessable();
        $this->postJson($path.'/duplicate')->assertCreated();
        $this->deleteJson($path)->assertNoContent();
        $this->postJson($path.'/restore')->assertOk();
        $this->post('/api/v1/cms/news-media', ['image' => UploadedFile::fake()->image('article.jpg', 40, 40)], ['Accept' => 'application/json'])->assertCreated();
        $this->login(['products.view', 'products.manage', 'content.view', 'content.manage']);
        $this->getJson('/api/v1/cms/products')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/cms/products?type=tin-tuc')->assertUnprocessable();
        $this->getJson('/api/v1/cms/news?type=san-pham')->assertUnprocessable();
        $this->getJson('/api/v1/cms/products/'.$created->json('data.id'))->assertNotFound();
        $this->login(['content.view']);
        $this->getJson('/api/v1/cms/news')->assertOk();
        $this->putJson($path, $payload)->assertForbidden();
        $this->deleteJson($path)->assertForbidden();
    }

    public function test_additional_news_types_are_configurable_and_isolated(): void
    {
        config(['news.types.su-kien' => [
            'label' => ['vi' => 'Sự kiện', 'en' => 'Events'],
            'singular' => ['vi' => 'sự kiện', 'en' => 'event'],
            'category_depth' => 0,
            'statuses' => ['is_active' => ['vi' => 'Hiển thị', 'en' => 'Visible']],
        ]]);
        $this->login(['content.view', 'content.manage']);
        $this->getJson('/api/v1/cms/news-types')->assertOk()->assertJsonCount(2, 'data')->assertJsonPath('data.1.key', 'su-kien')->assertJsonPath('data.1.features.images', true);
        $this->postJson('/api/v1/cms/news', $this->payload('su-kien'))->assertCreated();
        $this->getJson('/api/v1/cms/news')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/cms/news?type=su-kien')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/cms/news?type=su-kien&status=is_featured')->assertUnprocessable();
        $this->login(['products.view', 'products.manage']);
        $this->getJson('/api/v1/cms/news-types')->assertForbidden();
        $this->postJson('/api/v1/cms/news', $this->payload())->assertForbidden();
    }

    public function test_static_pages_have_one_record_per_type_and_reuse_content_permissions(): void
    {
        $this->login(['content.view', 'content.manage']);
        $this->getJson('/api/v1/cms/static-types')->assertOk()->assertJsonPath('data.0.key', 'gioi-thieu')->assertJsonPath('data.0.singleton', true)->assertJsonPath('data.0.features.copy', false)->assertJsonPath('data.0.category_depth', 0);
        $this->getJson('/api/v1/cms/news-types')->assertOk()->assertJsonCount(1, 'data');
        $payload = [...$this->payload('gioi-thieu'), 'name' => 'Giới thiệu', 'slug' => 'gioi-thieu', 'code' => '', 'content' => '<p>Về chúng tôi</p>'];
        $created = $this->postJson('/api/v1/cms/news', $payload)->assertCreated()->assertJsonPath('data.content', '<p>Về chúng tôi</p>');
        $path = '/api/v1/cms/news/'.$created->json('data.id').'?type=gioi-thieu';
        $this->postJson('/api/v1/cms/news', [...$payload, 'slug' => 'another-page', 'translations' => []])->assertUnprocessable()->assertJsonValidationErrors('type');
        $this->putJson($path, [...$payload, 'content' => '<p>Updated</p>'])->assertOk()->assertJsonPath('data.content', '<p>Updated</p>');
        $this->postJson('/api/v1/cms/news/'.$created->json('data.id').'/duplicate?type=gioi-thieu')->assertForbidden();
        $this->deleteJson($path)->assertForbidden();
        $this->patchJson('/api/v1/cms/news/'.$created->json('data.id').'/status?type=gioi-thieu', ['field' => 'is_active', 'value' => false])->assertOk();
        $this->getJson('/api/v1/cms/news?type=gioi-thieu')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/cms/news')->assertOk()->assertJsonCount(0, 'data');
        $this->login(['content.view']);
        $this->getJson($path)->assertOk();
        $this->putJson($path, $payload)->assertForbidden();
        $this->login(['products.view', 'products.manage']);
        $this->getJson('/api/v1/cms/static-types')->assertForbidden();
    }

    public function test_seo_pages_save_bilingual_metadata_and_image_independently(): void
    {
        Storage::fake('local');
        $this->login(['content.view', 'content.manage']);
        $this->getJson('/api/v1/cms/seopage-types')->assertOk()->assertJsonCount(6, 'data')->assertJsonPath('data.0.key', 'seo-trang-chu')->assertJsonPath('data.0.kind', 'seopage')->assertJsonPath('data.0.singleton', true)->assertJsonPath('data.0.features.content', false);
        $this->getJson('/api/v1/cms/static-types')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/v1/cms/news-types')->assertOk()->assertJsonCount(1, 'data');
        $image = $this->post('/api/v1/cms/news-media', ['image' => UploadedFile::fake()->image('seo.jpg', 300, 200)], ['Accept' => 'application/json'])->assertCreated()->json('data.id');
        $payload = [...$this->payload('seo-trang-chu'), 'code' => '', 'seo_title' => 'Trang chủ', 'seo_description' => 'Mô tả trang chủ', 'seo_keywords' => 'trang chủ', 'main_image_id' => $image, 'image_alt' => 'Ảnh SEO', 'translations' => ['en' => ['seo_title' => 'Home', 'seo_description' => 'Home description', 'seo_keywords' => 'home']]];
        $created = $this->postJson('/api/v1/cms/news', $payload)->assertCreated()->assertJsonPath('data.seo_title', 'Trang chủ')->assertJsonPath('data.translations.en.seo_title', 'Home')->assertJsonPath('data.main_image.id', $image)->assertJsonPath('data.schema_mode', 'disabled');
        $path = '/api/v1/cms/news/'.$created->json('data.id').'?type=seo-trang-chu';
        $this->putJson($path, [...$payload, 'seo_title' => 'Cập nhật SEO'])->assertOk()->assertJsonPath('data.seo_title', 'Cập nhật SEO');
        $this->getJson('/api/v1/cms/news?type=seo-trang-chu')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.translations.en.seo_description', 'Home description');
        $this->getJson('/api/v1/cms/news?type=seo-san-pham')->assertOk()->assertJsonCount(0, 'data');
        $this->getJson('/api/v1/cms/news?type=gioi-thieu')->assertOk()->assertJsonCount(0, 'data');
        $this->postJson('/api/v1/cms/news', [...$payload, 'slug' => 'second'])->assertUnprocessable()->assertJsonValidationErrors('type');
        $this->deleteJson($path)->assertForbidden();
        $this->postJson('/api/v1/cms/news/'.$created->json('data.id').'/duplicate?type=seo-trang-chu')->assertForbidden();
        $this->login(['content.view']);
        $this->getJson($path)->assertOk();
        $this->putJson($path, $payload)->assertForbidden();
        $this->login(['products.view', 'products.manage']);
        $this->getJson('/api/v1/cms/seopage-types')->assertForbidden();
    }
}
