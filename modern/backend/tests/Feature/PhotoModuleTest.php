<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PhotoModuleTest extends TestCase
{
    use RefreshDatabase;

    private function login(array $permissions): void
    {
        $this->app['auth']->forgetGuards();
        $role = Role::factory()->create(['permissions' => $permissions]);
        $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true, 'must_change_password' => false]);
        $this->actingAs($user, 'web')->withSession(['cms_auth_version' => 0]);
    }

    public function test_media_types_upload_singletons_and_scoped_lists(): void
    {
        Storage::fake('local');
        $this->login(['media.view', 'media.manage']);
        $this->getJson('/api/v1/cms/photo-types')->assertOk()->assertJsonCount(9, 'data');
        $image = $this->postJson('/api/v1/cms/photo-media', ['image' => UploadedFile::fake()->image('logo.png')])->assertCreated()->json('data.id');
        $data = ['image_id' => $image, 'is_active' => true, 'sort_order' => 1, 'translations' => ['vi' => ['alt' => 'Ảnh'], 'en' => ['alt' => 'Image']]];
        $first = $this->postJson('/api/v1/cms/photos/logo', $data)->assertOk()->json('data.id');
        $this->postJson('/api/v1/cms/photos/logo', $data)->assertOk()->assertJsonPath('data.id', $first);
        $this->getJson('/api/v1/cms/photos/logo')->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.translations.en.alt', 'Image');
        $this->deleteJson('/api/v1/cms/photos/logo/'.$first)->assertUnprocessable();
        $this->putJson('/api/v1/cms/photos/social/'.$first, $data)->assertNotFound();
        $created = $this->postJson('/api/v1/cms/photos/social', $data)->assertOk()->json('data.id');
        $this->postJson('/api/v1/cms/photos/social', $data)->assertOk();
        $this->getJson('/api/v1/cms/photos/social')->assertOk()->assertJsonCount(2, 'data');
        $this->putJson('/api/v1/cms/photos/social/'.$created, [...$data, 'is_active' => false])->assertOk()->assertJsonPath('data.is_active', false);
        $this->deleteJson('/api/v1/cms/photos/social/'.$created)->assertOk();
        $this->getJson('/api/v1/cms/photos/social')->assertOk()->assertJsonCount(1, 'data');
        $this->postJson('/api/v1/cms/photos/social', [...$data, 'link' => 'javascript:alert(1)'])->assertUnprocessable();
        $this->postJson('/api/v1/cms/photos/unknown', $data)->assertNotFound();
        $this->postJson('/api/v1/cms/photo-media', ['image' => UploadedFile::fake()->create('bad.svg', 1, 'image/svg+xml')])->assertUnprocessable();
    }

    public function test_permissions_are_enforced(): void
    {
        $this->getJson('/api/v1/cms/photo-types')->assertUnauthorized();
        $this->login(['media.view']);
        $this->getJson('/api/v1/cms/photo-types')->assertOk();
        $this->postJson('/api/v1/cms/photos/logo', [])->assertForbidden();
        $this->postJson('/api/v1/cms/photo-media', [])->assertForbidden();
        $this->login(['products.view']);
        $this->getJson('/api/v1/cms/photos/logo')->assertForbidden();
    }
}
