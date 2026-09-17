<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductMedia;
use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomepageTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_is_public_and_has_empty_defaults(): void
    {
        $this->getJson('/api/v1/website/home?locale=vi')->assertOk()
            ->assertJsonPath('data.company.name', '')
            ->assertJsonPath('data.about', null)
            ->assertJsonPath('data.products', [])
            ->assertJsonPath('data.news', [])
            ->assertJsonPath('data.seo.noindex', false);
        $this->getJson('/api/v1/website/home?locale=fr')->assertUnprocessable();
    }

    public function test_public_settings_are_whitelisted_and_media_is_visible_and_sorted(): void
    {
        SiteSetting::create(['key' => 'general', 'data' => ['translations' => ['vi' => ['name' => 'Công ty', 'address' => 'Hồ Chí Minh'], 'en' => ['name' => 'Company']], 'options' => ['lang_default' => 'vi', 'email' => 'hello@example.com', 'password_host' => 'SECRET_PASSWORD', 'coords_iframe' => 'PRIVATE_IFRAME'], 'headjs' => 'PRIVATE_SCRIPT']]);
        $image = ProductMedia::create(['path' => 'private/image.webp', 'original_name' => 'logo.webp', 'mime' => 'image/webp', 'width' => 200, 'height' => 100, 'bytes' => 100]);
        $photo = ['image_id' => $image->id, 'is_active' => true, 'sort_order' => 2, 'translations' => ['vi' => ['name' => 'Second']]];
        SiteSetting::create(['key' => 'photo:logo:single', 'data' => $photo]);
        SiteSetting::create(['key' => 'photo:slide:second', 'data' => $photo]);
        SiteSetting::create(['key' => 'photo:slide:first', 'data' => [...$photo, 'sort_order' => 1, 'link' => 'javascript:alert(1)', 'translations' => ['vi' => ['name' => 'First']]]]);
        SiteSetting::create(['key' => 'photo:slide:hidden', 'data' => [...$photo, 'is_active' => false, 'translations' => ['vi' => ['name' => 'HIDDEN_SLIDE']]]]);
        $response = $this->getJson('/api/v1/website/home?locale=en')->assertOk()
            ->assertJsonPath('data.company.name', 'Company')
            ->assertJsonPath('data.company.address', 'Hồ Chí Minh')
            ->assertJsonPath('data.company.email', 'hello@example.com')
            ->assertJsonCount(2, 'data.media.slide')
            ->assertJsonPath('data.media.slide.0.name', 'First')
            ->assertJsonPath('data.media.slide.0.link', '')
            ->assertJsonPath('data.media.logo.0.image.width', 200);
        foreach (['SECRET_PASSWORD', 'PRIVATE_IFRAME', 'PRIVATE_SCRIPT', 'private/image.webp', 'HIDDEN_SLIDE'] as $secret) {
            $response->assertDontSee($secret);
        }
        SiteSetting::where('key', 'photo:logo:single')->update(['data' => [...$photo, 'is_active' => false]]);
        $this->getJson('/api/v1/website/home')->assertOk()->assertJsonMissingPath('data.media.logo');
    }

    public function test_only_published_content_is_returned_and_deleted_records_are_excluded(): void
    {
        Product::factory()->create(['name' => 'HIDDEN_PRODUCT']);
        Product::factory()->create(['name' => 'UNRELATED_LIBRARY', 'type' => 'thu-vien-anh', 'is_active' => true]);
        $deleted = Product::factory()->create(['name' => 'DELETED_PRODUCT', 'is_active' => true]);
        $deleted->delete();
        Product::factory()->create(['name' => 'Visible', 'is_active' => true, 'is_featured' => true, 'translations' => ['en' => ['name' => 'English product']], 'ai_seo' => ['internal' => 'PRIVATE_AI'], 'schema_data' => ['secret' => 'PRIVATE_SCHEMA']]);
        Product::factory()->create(['name' => 'Public news', 'type' => 'tin-tuc', 'is_active' => true]);
        Product::factory()->create(['name' => 'DRAFT_NEWS', 'type' => 'tin-tuc']);
        $about = Product::factory()->create(['name' => 'About', 'type' => 'gioi-thieu', 'is_active' => true, 'content' => '<p>Safe text</p><script>alert(1)</script><img src="https://example.com/image.jpg" onerror="alert(1)">']);
        $response = $this->getJson('/api/v1/website/home?locale=en')->assertOk()
            ->assertJsonCount(1, 'data.products')->assertJsonCount(1, 'data.news')
            ->assertJsonPath('data.products.0.name', 'English product')
            ->assertJsonPath('data.news.0.name', 'Public news');
        foreach (['HIDDEN_PRODUCT', 'UNRELATED_LIBRARY', 'DELETED_PRODUCT', 'DRAFT_NEWS', 'PRIVATE_AI', 'PRIVATE_SCHEMA'] as $secret) {
            $response->assertDontSee($secret);
        }
        $this->assertStringNotContainsString('<script', $response->json('data.about.content'));
        $this->assertStringNotContainsString('onerror', $response->json('data.about.content'));
        $about->update(['is_active' => false]);
        $this->getJson('/api/v1/website/home')->assertJsonPath('data.about', null);
    }

    public function test_homepage_seo_uses_its_dedicated_config_not_product_publication_flag(): void
    {
        $seo = Product::factory()->create(['type' => 'seo-trang-chu', 'seo_title' => 'SEO trang chủ', 'seo_description' => 'Mô tả', 'seo_keywords' => 'từ khóa', 'canonical_url' => 'https://example.com/', 'noindex' => true, 'translations' => ['en' => ['seo_title' => 'Home SEO']]]);
        $this->getJson('/api/v1/website/home?locale=en')->assertOk()
            ->assertJsonPath('data.seo.title', 'Home SEO')
            ->assertJsonPath('data.seo.description', 'Mô tả')
            ->assertJsonPath('data.seo.canonical', 'https://example.com/')
            ->assertJsonPath('data.seo.noindex', true);
        $seo->delete();
        $this->getJson('/api/v1/website/home')->assertJsonPath('data.seo.title', '');
    }

    public function test_homepage_sections_respect_configured_type_and_limit(): void
    {
        config(['homepage.product_type' => 'thu-vien-anh', 'homepage.product_limit' => 2]);
        Product::factory()->count(3)->create(['type' => 'thu-vien-anh', 'is_active' => true]);
        Product::factory()->create(['type' => 'san-pham', 'is_active' => true, 'name' => 'OTHER_TYPE']);
        $this->getJson('/api/v1/website/home')->assertOk()->assertJsonCount(2, 'data.products')->assertDontSee('OTHER_TYPE');
    }
}
