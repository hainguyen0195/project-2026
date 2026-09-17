<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductMedia;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductModuleTest extends TestCase
{
    use RefreshDatabase;

    private function login(array $permissions = ['products.view', 'products.manage'], bool $changePassword = false): User
    {
        $this->app['auth']->forgetGuards();
        $role = Role::factory()->create(['permissions' => $permissions]);
        $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true, 'must_change_password' => $changePassword]);
        $this->actingAs($user, 'web')->withSession(['cms_auth_version' => 0]);

        return $user;
    }

    private function payload(array $overrides = []): array
    {
        return [...[
            'name' => 'Sản phẩm kiểm thử', 'slug' => 'san-pham-kiem-thu', 'code' => 'SP-001',
            'category_id' => null, 'brand_id' => null, 'size_ids' => [], 'main_image_id' => null, 'image_alt' => 'Ảnh chính',
            'regular_price' => '150000.50', 'sale_price' => '120000.25', 'availability' => 'in_stock',
            'description' => '<p>Mô tả</p>', 'content' => '<h2>Nội dung</h2><p><strong>Chi tiết</strong></p>', 'specifications' => '<table><tr><td>Kích thước</td><td>30 cm</td></tr></table>',
            'seo_title' => 'SEO sản phẩm', 'seo_description' => 'Mô tả SEO', 'seo_keywords' => 'comi, sản phẩm', 'canonical_url' => 'https://example.com/san-pham',
            'noindex' => false, 'schema_mode' => 'auto', 'is_active' => true, 'is_featured' => true, 'is_new' => true, 'is_bestseller' => false, 'sort_order' => 1, 'images' => [],
        ], ...$overrides];
    }

    public function test_quick_status_updates_only_selected_flag_and_respects_permissions(): void
    {
        $this->login();
        $product = Product::factory()->create(['name' => 'Unchanged name', 'is_active' => false, 'is_featured' => false, 'is_new' => false, 'is_bestseller' => false]);
        $endpoint = '/api/v1/cms/products/'.$product->id.'/status';
        foreach (['is_active', 'is_featured', 'is_new', 'is_bestseller'] as $field) {
            $this->patchJson($endpoint, ['field' => $field, 'value' => true, 'name' => 'Ignored name'])
                ->assertOk()->assertJsonPath('data.'.$field, true)->assertJsonPath('data.name', 'Unchanged name');
            $this->patchJson($endpoint, ['field' => $field, 'value' => false])->assertOk()->assertJsonPath('data.'.$field, false);
        }
        $this->patchJson($endpoint, ['field' => 'regular_price', 'value' => true])->assertUnprocessable()->assertJsonValidationErrors('field');
        $this->patchJson($endpoint, ['field' => 'is_active', 'value' => 'invalid'])->assertUnprocessable()->assertJsonValidationErrors('value');
        $this->patchJson($endpoint, [])->assertUnprocessable()->assertJsonValidationErrors(['field', 'value']);
        $this->assertFalse($product->refresh()->is_active);
        $this->assertFalse($product->is_featured);
        $this->assertFalse($product->is_new);
        $this->assertFalse($product->is_bestseller);
        $this->login(['products.view']);
        $this->patchJson($endpoint, ['field' => 'is_active', 'value' => true])->assertForbidden();
        $this->login();
        $product->delete();
        $this->patchJson($endpoint, ['field' => 'is_active', 'value' => true])->assertNotFound();
    }

    public function test_localized_slugs_are_saved_validated_and_updated(): void
    {
        $this->login();
        foreach (['products', 'product-categories'] as $resource) {
            $payload = $resource === 'products' ? $this->payload() : ['kind' => 'category', 'name' => 'Danh mục', 'slug' => 'danh-muc', 'is_active' => true, 'is_featured' => false, 'sort_order' => 0];
            $payload['translations'] = ['en' => ['name' => 'English Name', 'slug' => 'Custom English Slug']];
            $endpoint = '/api/v1/cms/'.$resource;
            $created = $this->postJson($endpoint, $payload)->assertCreated()->assertJsonPath('data.slug', $payload['slug'])->assertJsonPath('data.translations.en.slug', 'custom-english-slug');
            $id = $created->json('data.id');
            $this->putJson($endpoint.'/'.$id, $payload)->assertOk()->assertJsonPath('data.translations.en.slug', 'custom-english-slug');
            $duplicate = [...$payload, 'slug' => 'another-vietnamese-slug'];
            if ($resource === 'products') {
                $duplicate['code'] = 'SP-002';
                $copy = $this->postJson($endpoint.'/'.$id.'/duplicate')->assertCreated();
                $this->assertNotSame('custom-english-slug', $copy->json('data.translations.en.slug'));
            }
            $this->postJson($endpoint, $duplicate)->assertUnprocessable()->assertJsonValidationErrors('translations.en.slug');
            $payload['translations']['en']['slug'] = '';
            $this->putJson($endpoint.'/'.$id, $payload)->assertOk()->assertJsonPath('data.translations.en.slug', 'english-name');
            $payload['translations']['en']['slug'] = str_repeat('a', 256);
            $this->putJson($endpoint.'/'.$id, $payload)->assertUnprocessable()->assertJsonValidationErrors('translations.en.slug');
            $payload['translations']['en'] = ['name' => '', 'slug' => ''];
            $this->putJson($endpoint.'/'.$id, $payload)->assertOk()->assertJsonPath('data.translations.en.slug', null);
        }
    }

    public function test_ai_seo_is_saved_in_both_languages_and_can_be_updated_copied_and_cleared(): void
    {
        $this->login();
        $content = ['vi' => ['summary' => 'Tóm tắt sản phẩm', 'audience' => 'Gia đình', 'use_cases' => "Trang trí\nQuà tặng", 'alternate_names' => "Tên khác\nTên khác\nTên thứ hai", 'faqs' => [['question' => 'Bảo quản thế nào?', 'answer' => 'Lau bằng khăn mềm.']], 'sources' => [['title' => 'Nhà sản xuất', 'url' => 'https://example.com/manual']]], 'en' => ['summary' => 'Product summary', 'faqs' => [], 'sources' => []]];
        $response = $this->postJson('/api/v1/cms/products', $this->payload(['ai_seo' => $content]))->assertCreated()
            ->assertJsonPath('data.ai_seo.vi.faqs.0.answer', 'Lau bằng khăn mềm.')
            ->assertJsonPath('data.resolved_schema.alternateName', ['Tên khác', 'Tên thứ hai']);
        $id = $response->json('data.id');
        $this->getJson('/api/v1/cms/products/'.$id)->assertOk()->assertJsonPath('data.ai_seo.en.summary', 'Product summary');
        $content['vi']['summary'] = 'Đã cập nhật';
        $this->putJson('/api/v1/cms/products/'.$id, $this->payload(['ai_seo' => $content]))->assertOk()->assertJsonPath('data.ai_seo.vi.summary', 'Đã cập nhật');
        $this->postJson('/api/v1/cms/products/'.$id.'/duplicate')->assertCreated()->assertJsonPath('data.ai_seo.vi.summary', 'Đã cập nhật');
        $this->putJson('/api/v1/cms/products/'.$id, $this->payload())->assertOk()->assertJsonPath('data.ai_seo.vi.summary', 'Đã cập nhật');
        $this->putJson('/api/v1/cms/products/'.$id, $this->payload(['ai_seo' => null]))->assertOk()->assertJsonPath('data.ai_seo', null)->assertJsonMissingPath('data.resolved_schema.alternateName');
    }

    public function test_ai_seo_rejects_invalid_nested_data_and_unsafe_source_urls(): void
    {
        $this->login();
        foreach ([
            ['value' => ['fr' => []], 'field' => 'ai_seo'],
            ['value' => ['vi' => ['unknown' => 'x']], 'field' => 'ai_seo.vi'],
            ['value' => ['vi' => ['summary' => str_repeat('x', 1501)]], 'field' => 'ai_seo.vi.summary'],
            ['value' => ['vi' => ['faqs' => [['question' => 'Câu hỏi', 'answer' => '']]]], 'field' => 'ai_seo.vi.faqs.0.answer'],
            ['value' => ['vi' => ['faqs' => array_fill(0, 21, ['question' => 'Q', 'answer' => 'A'])]], 'field' => 'ai_seo.vi.faqs'],
            ['value' => ['en' => ['sources' => [['title' => 'Nguồn', 'url' => 'javascript:alert(1)']]]], 'field' => 'ai_seo.en.sources.0.url'],
            ['value' => ['vi' => ['sources' => array_fill(0, 11, ['title' => 'Nguồn', 'url' => 'https://example.com'])]], 'field' => 'ai_seo.vi.sources'],
        ] as $invalid) {
            $this->postJson('/api/v1/cms/products', $this->payload(['ai_seo' => $invalid['value']]))->assertUnprocessable()->assertJsonValidationErrors($invalid['field']);
        }
        $this->assertDatabaseCount('products', 0);
    }

    public function test_ai_seo_respects_product_permissions_and_schema_modes(): void
    {
        $product = Product::factory()->create(['ai_seo' => ['vi' => ['summary' => 'Nội dung', 'alternate_names' => 'Tên khác']]]);
        $this->login(['products.view']);
        $this->getJson('/api/v1/cms/products/'.$product->id)->assertOk()->assertJsonPath('data.ai_seo.vi.summary', 'Nội dung');
        $this->putJson('/api/v1/cms/products/'.$product->id, $this->payload(['ai_seo' => null]))->assertForbidden();
        $this->login();
        $this->putJson('/api/v1/cms/products/'.$product->id, $this->payload(['schema_mode' => 'disabled']))->assertOk()->assertJsonPath('data.resolved_schema', null);
        $schema = ['@context' => 'https://schema.org', '@type' => 'Product', 'name' => 'Custom'];
        $this->putJson('/api/v1/cms/products/'.$product->id, $this->payload(['schema_mode' => 'custom', 'schema_data' => $schema]))->assertOk()->assertJsonPath('data.resolved_schema', $schema);
    }

    private function categoryPayload(array $overrides = []): array
    {
        return [...['kind' => 'category', 'name' => 'Danh mục', 'slug' => 'danh-muc', 'parent_id' => null, 'is_active' => true, 'is_featured' => false, 'sort_order' => 0], ...$overrides];
    }

    public function test_product_routes_require_login_and_permissions(): void
    {
        $product = Product::factory()->create();
        $this->getJson('/api/v1/cms/products')->assertUnauthorized();
        $this->postJson('/api/v1/cms/products', $this->payload())->assertUnauthorized();
        $this->login([]);
        $this->getJson('/api/v1/cms/products')->assertForbidden();
        $this->getJson('/api/v1/cms/products/'.$product->id)->assertForbidden();
        $this->getJson('/api/v1/cms/product-categories')->assertForbidden();
        $this->login(['products.view']);
        $this->getJson('/api/v1/cms/products')->assertOk();
        $this->getJson('/api/v1/cms/products/'.$product->id)->assertOk();
        $this->postJson('/api/v1/cms/products', $this->payload())->assertForbidden();
        $this->putJson('/api/v1/cms/products/'.$product->id, $this->payload())->assertForbidden();
        $this->deleteJson('/api/v1/cms/products/'.$product->id)->assertForbidden();
        $this->postJson('/api/v1/cms/products/'.$product->id.'/duplicate')->assertForbidden();
        $this->postJson('/api/v1/cms/products/'.$product->id.'/restore')->assertForbidden();
        $this->postJson('/api/v1/cms/product-media')->assertForbidden();
        $category = ProductCategory::factory()->create();
        $this->postJson('/api/v1/cms/product-categories', $this->categoryPayload())->assertForbidden();
        $this->putJson('/api/v1/cms/product-categories/'.$category->id, $this->categoryPayload())->assertForbidden();
        $this->deleteJson('/api/v1/cms/product-categories/'.$category->id)->assertForbidden();
        $this->login(['products.view', 'products.manage'], true);
        $this->postJson('/api/v1/cms/products', $this->payload())->assertStatus(423);
    }

    public function test_full_product_round_trip_includes_images_sizes_html_seo_and_schema(): void
    {
        $this->login();
        Storage::fake('local');
        $category = ProductCategory::factory()->create();
        $brand = ProductCategory::factory()->create(['kind' => 'brand']);
        $size = ProductCategory::factory()->create(['kind' => 'size']);
        $media = $this->postJson('/api/v1/cms/product-media', ['image' => UploadedFile::fake()->image('image.png', 200, 150)])->assertCreated()->json('data.id');
        $data = $this->payload(['slug' => '', 'code' => ' sp-001 ', 'category_id' => $category->id, 'brand_id' => $brand->id, 'size_ids' => [$size->id], 'main_image_id' => $media,
            'images' => [['media_id' => $media, 'alt' => 'Chi tiết', 'caption' => 'Ảnh album', 'is_active' => true]],
            'translations' => ['en' => ['name' => 'English product', 'content' => '<p>English content</p>']]]);
        $response = $this->postJson('/api/v1/cms/products', $data)->assertCreated()->assertJsonPath('data.code', 'SP-001')->assertJsonPath('data.slug', 'san-pham-kiem-thu')->assertJsonPath('data.regular_price', '150000.50')->assertJsonPath('data.sale_price', '120000.25')->assertJsonPath('data.size_ids.0', $size->id)->assertJsonPath('data.resolved_schema.@type', 'Product')->assertJsonPath('data.resolved_schema.offers.price', '120000.25')->assertJsonPath('data.images.0.alt', 'Chi tiết');
        $id = $response->json('data.id');
        $this->getJson('/api/v1/cms/products/'.$id)->assertOk()->assertJsonPath('data.translations.en.name', 'English product')->assertJsonPath('data.main_image.id', $media)->assertJsonPath('data.seo_title', 'SEO sản phẩm');
        $this->putJson('/api/v1/cms/products/'.$id, [...$data, 'name' => 'Tên đã sửa', 'size_ids' => [], 'images' => [], 'main_image_id' => null, 'sale_price' => null])->assertOk()->assertJsonPath('data.name', 'Tên đã sửa')->assertJsonCount(0, 'data.images')->assertJsonCount(0, 'data.size_ids')->assertJsonPath('data.sale_price', null);
        $this->assertDatabaseHas('products', ['id' => $id, 'name' => 'Tên đã sửa']);
    }

    public function test_category_tree_rejects_cycles_depth_five_and_deep_subtree_moves(): void
    {
        $this->login();
        $parent = null;
        $ids = [];
        for ($level = 1; $level <= 4; $level++) {
            $parent = $this->postJson('/api/v1/cms/product-categories', $this->categoryPayload(['slug' => 'level-'.$level, 'parent_id' => $parent]))->assertCreated()->json('data.id');
            $ids[] = $parent;
        }
        $this->postJson('/api/v1/cms/product-categories', $this->categoryPayload(['parent_id' => $parent]))->assertUnprocessable()->assertJsonValidationErrors('parent_id');
        $this->putJson('/api/v1/cms/product-categories/'.$ids[0], $this->categoryPayload(['slug' => 'level-1', 'parent_id' => $ids[3]]))->assertUnprocessable();
        $this->putJson('/api/v1/cms/product-categories/'.$ids[0], $this->categoryPayload(['slug' => 'level-1', 'parent_id' => $ids[0]]))->assertUnprocessable();
        $other = ProductCategory::factory()->create();
        $this->putJson('/api/v1/cms/product-categories/'.$ids[0], $this->categoryPayload(['slug' => 'level-1', 'parent_id' => $other->id]))->assertUnprocessable();
        $this->putJson('/api/v1/cms/product-categories/'.$ids[3], $this->categoryPayload(['slug' => 'level-4', 'parent_id' => null]))->assertOk();
    }

    public function test_category_kind_and_deletion_cannot_break_relationships(): void
    {
        $this->login();
        $category = ProductCategory::factory()->create();
        $child = ProductCategory::factory()->create(['parent_id' => $category->id]);
        $this->deleteJson('/api/v1/cms/product-categories/'.$category->id)->assertUnprocessable();
        $this->putJson('/api/v1/cms/product-categories/'.$category->id, $this->categoryPayload(['kind' => 'brand']))->assertUnprocessable();
        $this->postJson('/api/v1/cms/product-categories', $this->categoryPayload(['kind' => 'brand', 'parent_id' => $category->id]))->assertUnprocessable();
        $product = Product::factory()->create(['category_id' => $child->id]);
        $product->delete();
        $this->deleteJson('/api/v1/cms/product-categories/'.$child->id)->assertUnprocessable();
        $unused = ProductCategory::factory()->create();
        $this->deleteJson('/api/v1/cms/product-categories/'.$unused->id)->assertNoContent();
    }

    public function test_product_validation_rejects_duplicates_invalid_prices_wrong_types_and_invalid_media(): void
    {
        $this->login();
        $product = Product::factory()->create(['slug' => 'existing', 'code' => 'EXISTING']);
        $brand = ProductCategory::factory()->create(['kind' => 'brand']);
        $data = $this->payload(['slug' => 'existing', 'code' => 'existing', 'regular_price' => '-1', 'sale_price' => 5, 'category_id' => $brand->id, 'size_ids' => [$brand->id], 'main_image_id' => 999999]);
        $this->postJson('/api/v1/cms/products', $data)->assertUnprocessable()->assertJsonValidationErrors(['slug', 'code', 'regular_price', 'sale_price', 'category_id', 'size_ids.0', 'main_image_id']);
        $this->postJson('/api/v1/cms/products', $this->payload(['canonical_url' => 'javascript:alert(1)']))->assertUnprocessable()->assertJsonValidationErrors('canonical_url');
        $this->postJson('/api/v1/cms/products', $this->payload(['regular_price' => '1.001']))->assertUnprocessable();
        $this->postJson('/api/v1/cms/products', $this->payload(['sale_price' => '200000']))->assertUnprocessable();
        $this->postJson('/api/v1/cms/products', $this->payload(['sale_price' => '0']))->assertCreated()->assertJsonPath('data.resolved_schema.offers.price', '0.00');
        $this->assertSame(2, Product::count());
    }

    public function test_rich_html_is_sanitized_in_all_languages(): void
    {
        $this->login();
        $unsafe = '<p onclick="alert(1)">Safe <strong>bold</strong><script>alert(1)</script><img src="x" onerror="alert(2)"><a href="javascript:alert(3)">link</a><iframe src="https://evil.example"></iframe></p>';
        $response = $this->postJson('/api/v1/cms/products', $this->payload(['content' => $unsafe, 'description' => $unsafe, 'specifications' => $unsafe, 'translations' => ['en' => ['content' => $unsafe]]]))->assertCreated();
        foreach (['content', 'description', 'specifications', 'translations.en.content'] as $field) {
            $clean = $response->json('data.'.$field);
            $this->assertStringContainsString('<strong>bold</strong>', $clean);
            foreach (['<script', 'onclick', 'onerror', 'javascript:', '<iframe'] as $forbidden) {
                $this->assertStringNotContainsString($forbidden, $clean);
            }
        }
    }

    public function test_schema_requires_product_object_and_supports_auto_custom_disabled(): void
    {
        $this->login();
        $this->postJson('/api/v1/cms/products', $this->payload(['schema_mode' => 'custom', 'schema_data' => ['@type' => 'Article']]))->assertUnprocessable()->assertJsonValidationErrors('schema_data');
        $schema = ['@context' => 'https://schema.org', '@type' => 'Product', 'name' => 'Custom'];
        $response = $this->postJson('/api/v1/cms/products', $this->payload(['schema_mode' => 'custom', 'schema_data' => $schema]))->assertCreated()->assertJsonPath('data.resolved_schema', $schema);
        $this->putJson('/api/v1/cms/products/'.$response->json('data.id'), $this->payload(['schema_mode' => 'disabled']))->assertOk()->assertJsonPath('data.resolved_schema', null)->assertJsonPath('data.schema_data', null);
    }

    public function test_image_upload_is_reencoded_and_svg_fake_images_and_large_images_are_rejected(): void
    {
        Storage::fake('local');
        $this->login();
        $response = $this->postJson('/api/v1/cms/product-media', ['image' => UploadedFile::fake()->image('photo.png', 120, 80)])->assertCreated()->assertJsonPath('data.mime', 'image/webp')->assertJsonPath('data.width', 120)->assertJsonMissingPath('data.path');
        $media = ProductMedia::findOrFail($response->json('data.id'));
        Storage::disk('local')->assertExists($media->path);
        $this->assertSame('image/webp', getimagesizefromstring(Storage::disk('local')->get($media->path))['mime']);
        $this->get('/api/v1/catalog/media/'.$media->id)->assertOk()->assertHeader('X-Content-Type-Options', 'nosniff');
        $this->postJson('/api/v1/cms/product-media', ['image' => UploadedFile::fake()->createWithContent('fake.jpg', '<?php echo 1;')])->assertUnprocessable();
        $this->postJson('/api/v1/cms/product-media', ['image' => UploadedFile::fake()->createWithContent('bad.svg', '<svg xmlns="http://www.w3.org/2000/svg"><script>alert(1)</script></svg>')])->assertUnprocessable();
        $this->postJson('/api/v1/cms/product-media', ['image' => UploadedFile::fake()->image('big.png', 5100, 2)])->assertUnprocessable();
        $this->postJson('/api/v1/cms/product-media', ['image' => UploadedFile::fake()->image('big.png')->size(8193)])->assertUnprocessable();
        $this->assertSame(1, ProductMedia::count());
    }

    public function test_soft_delete_restore_duplicate_and_descendant_filter_are_persistent(): void
    {
        $this->login();
        $parent = ProductCategory::factory()->create();
        $child = ProductCategory::factory()->create(['parent_id' => $parent->id]);
        $product = Product::factory()->create(['category_id' => $child->id, 'name' => 'Find me', 'is_active' => true]);
        Product::factory()->count(16)->create();
        $this->getJson('/api/v1/cms/products?category_id='.$parent->id.'&search=Find')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $product->id);
        $this->getJson('/api/v1/cms/products?page=2')->assertOk()->assertJsonPath('meta.total', 17)->assertJsonCount(2, 'data');
        $copy = $this->postJson('/api/v1/cms/products/'.$product->id.'/duplicate')->assertCreated()->assertJsonPath('data.is_active', false)->json('data.id');
        $this->assertNotSame($product->code, Product::findOrFail($copy)->code);
        $this->deleteJson('/api/v1/cms/products/'.$product->id)->assertNoContent();
        $this->getJson('/api/v1/cms/products/'.$product->id)->assertNotFound();
        $this->getJson('/api/v1/cms/products?status=trash')->assertOk()->assertJsonCount(1, 'data');
        $this->postJson('/api/v1/cms/products/'.$product->id.'/restore')->assertOk();
        $this->getJson('/api/v1/cms/products/'.$product->id)->assertOk();
    }

    public function test_album_order_metadata_and_removal_are_persisted(): void
    {
        $this->login();
        Storage::fake('local');
        $ids = [];
        for ($index = 0; $index < 2; $index++) {
            $ids[] = $this->postJson('/api/v1/cms/product-media', ['image' => UploadedFile::fake()->image('photo.png')])->assertCreated()->json('data.id');
        }
        $data = $this->payload(['images' => [['media_id' => $ids[1], 'alt' => 'Second', 'caption' => 'Caption', 'is_active' => false], ['media_id' => $ids[0], 'alt' => 'First', 'caption' => '', 'is_active' => true]]]);
        $response = $this->postJson('/api/v1/cms/products', $data)->assertCreated()->assertJsonPath('data.images.0.media_id', $ids[1])->assertJsonPath('data.images.0.is_active', false)->assertJsonCount(1, 'data.resolved_schema.image');
        $id = $response->json('data.id');
        $this->putJson('/api/v1/cms/products/'.$id, [...$data, 'images' => array_reverse($data['images'])])->assertOk()->assertJsonPath('data.images.0.media_id', $ids[0]);
        $this->putJson('/api/v1/cms/products/'.$id, [...$data, 'images' => [$data['images'][0], $data['images'][0]]])->assertUnprocessable();
    }
}
