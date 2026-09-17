<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    private function login(array $permissions): void
    {
        $this->app['auth']->forgetGuards();
        $role = Role::factory()->create(['permissions' => $permissions]);
        $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true, 'must_change_password' => false]);
        $this->actingAs($user, 'web')->withSession(['cms_auth_version' => 0]);
    }

    public function test_settings_persist_bilingual_information_and_are_singleton(): void
    {
        $this->login(['general.view', 'general.manage']);
        $this->getJson('/api/v1/cms/general-settings')->assertOk()->assertJsonPath('data.options.lang_default', 'vi');
        $data = ['translations' => ['vi' => ['name' => 'Công ty', 'address' => 'Việt Nam'], 'en' => ['name' => 'Company']], 'options' => ['lang_default' => 'vi', 'email' => 'test@example.com', 'website' => 'https://example.com'], 'headjs' => '<script>example()</script>'];
        $this->putJson('/api/v1/cms/general-settings', $data)->assertOk()->assertJsonPath('data.translations.en.name', 'Company');
        $data['translations']['vi']['name'] = 'Cập nhật';
        $this->putJson('/api/v1/cms/general-settings', $data)->assertOk();
        $this->getJson('/api/v1/cms/general-settings')->assertOk()->assertJsonPath('data.translations.vi.name', 'Cập nhật')->assertJsonPath('data.headjs', '<script>example()</script>');
        $this->assertSame(1, SiteSetting::count());
    }

    public function test_permissions_and_validation_are_enforced(): void
    {
        $this->getJson('/api/v1/cms/general-settings')->assertUnauthorized();
        $this->login(['general.view']);
        $this->getJson('/api/v1/cms/general-settings')->assertOk();
        $this->putJson('/api/v1/cms/general-settings', [])->assertForbidden();
        $this->login(['settings.view']);
        $this->getJson('/api/v1/cms/general-settings')->assertForbidden();
        $this->login(['general.view', 'general.manage']);
        $this->putJson('/api/v1/cms/general-settings', ['translations' => ['vi' => ['name' => '']], 'options' => ['lang_default' => 'fr', 'email' => 'invalid', 'website' => 'javascript:alert(1)']])->assertUnprocessable()->assertJsonValidationErrors(['translations.vi.name', 'options.lang_default', 'options.email', 'options.website']);
        $this->putJson('/api/v1/cms/general-settings', ['translations' => ['vi' => ['name' => 'Example']], 'options' => ['lang_default' => 'vi', 'password_host' => 'secret']])->assertUnprocessable()->assertJsonValidationErrors('options');

        $this->assertSame(0, SiteSetting::count());
    }

    public function test_branding_shares_only_company_names_with_authenticated_users(): void
    {
        $this->getJson('/api/v1/cms/branding')->assertUnauthorized();
        SiteSetting::create(['key' => 'general', 'data' => ['translations' => ['vi' => ['name' => 'Cris', 'address' => 'Private'], 'en' => ['name' => 'Cris Company']], 'headjs' => 'private-code']]);
        $this->login(['products.view']);
        $this->getJson('/api/v1/cms/branding')->assertOk()->assertExactJson(['data' => ['vi' => 'Cris', 'en' => 'Cris Company']]);
        $this->getJson('/api/v1/cms/general-settings')->assertForbidden();
    }
}
