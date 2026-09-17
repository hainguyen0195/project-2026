<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CmsAccessTest extends TestCase
{
    use RefreshDatabase;

    private function account(bool $admin = true, array $attributes = []): User
    {
        $role = Role::factory()->create(['is_system' => $admin]);

        return User::factory()->create([...['role_id' => $role->id, 'is_active' => true, 'must_change_password' => false, 'password' => 'SecurePassword!123'], ...$attributes]);
    }

    private function sessionAs(User $user): static
    {
        return $this->actingAs($user, 'web')->withSession(['cms_auth_version' => $user->auth_version]);
    }

    public function test_login_normalizes_email_and_returns_only_safe_profile(): void
    {
        $user = $this->account();
        $this->postJson('/api/v1/auth/login', ['email' => strtoupper($user->email), 'password' => 'SecurePassword!123'])
            ->assertOk()->assertJsonPath('data.id', $user->id)->assertJsonMissingPath('data.password')->assertJsonMissingPath('data.auth_version')->assertSessionHas('cms_auth_version', 0);
        $this->getJson('/api/v1/auth/me')->assertOk();
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_password_change_accepts_eight_characters_but_rejects_seven(): void
    {
        $user = $this->account(true, ['must_change_password' => true]);
        $this->sessionAs($user)->putJson('/api/v1/auth/password', [
            'current_password' => 'SecurePassword!123',
            'password' => 'Abcd!12',
            'password_confirmation' => 'Abcd!12',
        ])->assertUnprocessable()->assertJsonValidationErrors('password');

        $this->putJson('/api/v1/auth/password', [
            'current_password' => 'SecurePassword!123',
            'password' => 'Abcd!123',
            'password_confirmation' => 'Abcd!123',
        ])->assertOk()->assertJsonPath('data.must_change_password', false);

        $this->assertTrue(Hash::check('Abcd!123', $user->fresh()->password));
    }

    public function test_account_creation_and_reset_accept_eight_character_passwords(): void
    {
        $admin = $this->account();
        $data = [
            'name' => 'New admin', 'email' => 'eight@example.com',
            'role_id' => $admin->role_id, 'is_active' => true, 'password' => 'Abcd!12',
        ];
        $this->sessionAs($admin)->postJson('/api/v1/cms/users', $data)
            ->assertUnprocessable()->assertJsonValidationErrors('password');

        $response = $this->postJson('/api/v1/cms/users', [...$data, 'password' => 'Abcd!123'])->assertCreated();
        $created = User::findOrFail($response->json('data.id'));
        $this->assertTrue(Hash::check('Abcd!123', $created->password));

        $this->putJson('/api/v1/cms/users/'.$created->id, $data)
            ->assertUnprocessable()->assertJsonValidationErrors('password');
        $this->putJson('/api/v1/cms/users/'.$created->id, [...$data, 'password' => 'Xyzw!456'])
            ->assertOk()->assertJsonPath('data.must_change_password', true);
        $this->assertTrue(Hash::check('Xyzw!456', $created->fresh()->password));
    }

    public function test_invalid_disabled_and_unassigned_users_cannot_login(): void
    {
        $user = $this->account(false, ['is_active' => false]);
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'SecurePassword!123'])->assertUnprocessable();
        $user->is_active = true;
        $user->role_id = null;
        $user->save();
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'SecurePassword!123'])->assertUnprocessable();
        $this->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'wrong'])->assertUnprocessable();
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/v1/auth/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])->assertUnprocessable();
        }
        $this->postJson('/api/v1/auth/login', ['email' => 'nobody@example.com', 'password' => 'wrong'])->assertTooManyRequests();
    }

    public function test_guests_cannot_read_or_mutate_accounts_and_roles(): void
    {
        $this->getJson('/api/v1/cms/users')->assertUnauthorized();
        $this->postJson('/api/v1/cms/users', [])->assertUnauthorized();
        $this->getJson('/api/v1/cms/roles')->assertUnauthorized();
    }

    public function test_staff_cannot_manage_access_but_can_only_view_assigned_modules(): void
    {
        $user = $this->account(false);
        $this->sessionAs($user)->getJson('/api/v1/cms/users')->assertForbidden();
        $this->postJson('/api/v1/cms/roles', [])->assertForbidden();
        $this->putJson('/api/v1/cms/users/'.$user->id, [])->assertForbidden();
        $this->putJson('/api/v1/cms/roles/'.$user->role_id, [])->assertForbidden();
        $this->getJson('/api/v1/cms/access/products')->assertOk();
        $this->getJson('/api/v1/cms/access/orders')->assertForbidden();
        $this->getJson('/api/v1/cms/access/access')->assertForbidden();
        $this->getJson('/api/v1/cms/access/unknown')->assertNotFound();
    }

    public function test_first_login_requires_strong_password_change_and_revokes_old_version(): void
    {
        $user = $this->account(true, ['must_change_password' => true]);
        $this->sessionAs($user)->getJson('/api/v1/cms/users')->assertStatus(423);
        $this->putJson('/api/v1/auth/password', ['current_password' => 'SecurePassword!123', 'password' => 'weak', 'password_confirmation' => 'weak'])->assertUnprocessable();
        $this->putJson('/api/v1/auth/password', ['current_password' => 'wrong', 'password' => 'NewSecurePass!456', 'password_confirmation' => 'NewSecurePass!456'])->assertUnprocessable();
        $this->putJson('/api/v1/auth/password', ['current_password' => 'SecurePassword!123', 'password' => 'NewSecurePass!456', 'password_confirmation' => 'NewSecurePass!456'])->assertOk()->assertJsonPath('data.must_change_password', false)->assertSessionHas('cms_auth_version', 1);
        $this->assertTrue(Hash::check('NewSecurePass!456', $user->fresh()->password));
        $this->sessionAs($user->fresh())->getJson('/api/v1/cms/users')->assertOk();
        $this->withSession(['cms_auth_version' => 0])->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_admin_creates_accounts_and_enforces_validation(): void
    {
        $admin = $this->account();
        $role = Role::factory()->create();
        $data = ['name' => 'Nhân viên', 'email' => 'STAFF@example.com', 'role_id' => $role->id, 'is_active' => true, 'password' => 'TemporaryPass!123'];
        $this->sessionAs($admin)->postJson('/api/v1/cms/users', $data)->assertCreated()->assertJsonPath('data.email', 'staff@example.com')->assertJsonPath('data.must_change_password', true)->assertJsonMissingPath('data.password');
        $this->assertTrue(Hash::check($data['password'], User::where('email', 'staff@example.com')->firstOrFail()->password));
        $this->postJson('/api/v1/cms/users', $data)->assertUnprocessable()->assertJsonValidationErrors('email');
        $this->postJson('/api/v1/cms/users', [...$data, 'email' => 'new@example.com', 'password' => 'short', 'is_system' => true])->assertUnprocessable()->assertJsonValidationErrors(['password', 'is_system']);
        $this->getJson('/api/v1/cms/users?search=STAFF')->assertOk()->assertJsonCount(1, 'data');
    }

    public function test_roles_cannot_escalate_to_system_admin_and_manage_requires_view(): void
    {
        $admin = $this->account();
        $this->sessionAs($admin)->postJson('/api/v1/cms/roles', ['name' => 'Invalid', 'permissions' => ['access.manage']])->assertUnprocessable();
        $this->postJson('/api/v1/cms/roles', ['name' => 'Invalid', 'permissions' => [], 'is_system' => true])->assertUnprocessable();
        $this->postJson('/api/v1/cms/roles', ['name' => 'Invalid', 'permissions' => ['products.manage']])->assertUnprocessable();
        $this->putJson('/api/v1/cms/roles/'.$admin->role_id, ['name' => 'Changed', 'permissions' => []])->assertForbidden();
        $response = $this->postJson('/api/v1/cms/roles', ['name' => 'Kho hàng', 'permissions' => ['products.view', 'products.manage']])->assertCreated();
        $this->putJson('/api/v1/cms/roles/'.$response->json('data.id'), ['name' => 'Kho hàng', 'permissions' => ['products.view']])->assertOk()->assertJsonPath('data.permissions', ['products.view']);
    }

    public function test_self_disable_demotion_and_admin_reset_are_blocked(): void
    {
        $admin = $this->account();
        $role = Role::factory()->create();
        $data = ['name' => $admin->name, 'email' => $admin->email, 'role_id' => $admin->role_id, 'is_active' => true];
        $this->sessionAs($admin)->putJson('/api/v1/cms/users/'.$admin->id, [...$data, 'is_active' => false])->assertUnprocessable();
        $this->putJson('/api/v1/cms/users/'.$admin->id, [...$data, 'role_id' => $role->id])->assertUnprocessable();
        $this->putJson('/api/v1/cms/users/'.$admin->id, [...$data, 'password' => 'AnotherStrong!123'])->assertUnprocessable();
        $this->putJson('/api/v1/cms/users/'.$admin->id, [...$data, 'name' => 'New name'])->assertOk();
    }

    public function test_disabling_and_resetting_another_account_revokes_sessions(): void
    {
        $admin = $this->account();
        $staff = $this->account(false);
        $data = ['name' => $staff->name, 'email' => $staff->email, 'role_id' => $staff->role_id, 'is_active' => false];
        $this->sessionAs($admin)->putJson('/api/v1/cms/users/'.$staff->id, $data)->assertOk();
        $this->assertSame(1, $staff->fresh()->auth_version);
        $this->sessionAs($staff->fresh())->getJson('/api/v1/auth/me')->assertUnauthorized();
        $this->sessionAs($admin)->putJson('/api/v1/cms/users/'.$staff->id, [...$data, 'is_active' => true, 'password' => 'ResetPassword!123'])->assertOk()->assertJsonPath('data.must_change_password', true);
        $this->actingAs($staff->fresh(), 'web')->withSession(['cms_auth_version' => 0])->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_logout_invalidates_session(): void
    {
        $user = $this->account();
        $this->sessionAs($user)->postJson('/api/v1/auth/logout')->assertNoContent()->assertSessionMissing('cms_auth_version');
        $this->getJson('/api/v1/auth/me')->assertUnauthorized();
    }

    public function test_seeder_is_idempotent_and_admin_command_does_not_overwrite(): void
    {
        $this->seed(RoleSeeder::class);
        $this->seed(RoleSeeder::class);
        $this->assertSame(4, Role::count());
        $this->artisan('cms:create-admin', ['email' => 'admin@example.com'])->assertSuccessful();
        $user = User::where('email', 'admin@example.com')->firstOrFail();
        $this->assertTrue($user->isSystemAdmin());
        $this->assertTrue($user->must_change_password);
        $this->artisan('cms:create-admin', ['email' => 'admin@example.com'])->assertFailed();
        $this->assertSame($user->password, $user->fresh()->password);
    }
}
