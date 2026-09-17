<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ApiHealthTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_health_returns_service_status_when_database_is_available(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertOk()->assertExactJson([
            'data' => ['service' => 'comi-api', 'status' => 'ok'],
        ])->assertHeader('Cache-Control', 'no-store, private');
    }

    public function test_health_returns_503_without_internal_details_when_database_is_unavailable(): void
    {
        config(['database.default' => 'unconfigured']);

        $response = $this->getJson('/api/v1/health');

        $response->assertServiceUnavailable()->assertExactJson([
            'data' => ['service' => 'comi-api', 'status' => 'unavailable'],
        ]);
    }

    public function test_me_returns_json_401_without_authentication_or_accept_header(): void
    {
        $response = $this->get('/api/v1/me');

        $response->assertUnauthorized()->assertJsonStructure(['message']);
    }

    public function test_me_only_returns_the_authenticated_users_public_profile_fields(): void
    {
        $user = User::factory()->create(['role_id' => Role::factory()->create()->id, 'is_active' => true]);

        $response = $this->actingAs($user, 'web')->withSession(['cms_auth_version' => 0])->getJson('/api/v1/me');

        $response->assertOk()->assertExactJson([
            'data' => ['id' => $user->id, 'name' => $user->name, 'email' => $user->email],
        ]);
    }

    public function test_unknown_api_route_returns_json_404(): void
    {
        $response = $this->get('/api/v1/missing');

        $response->assertNotFound()->assertJsonStructure(['message']);
    }

    public function test_cors_allows_the_configured_frontend_with_credentials(): void
    {
        config(['cors.allowed_origins' => ['http://127.0.0.1:3001']]);

        $response = $this->withHeaders([
            'Origin' => 'http://127.0.0.1:3001',
            'Access-Control-Request-Method' => 'GET',
        ])->options('/api/v1/me');

        $response->assertNoContent()
            ->assertHeader('Access-Control-Allow-Origin', 'http://127.0.0.1:3001')
            ->assertHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function test_cors_does_not_grant_access_to_an_unlisted_origin(): void
    {
        config(['cors.allowed_origins' => ['http://127.0.0.1:3001', 'http://127.0.0.1:3000']]);

        $response = $this->withHeaders([
            'Origin' => 'https://untrusted.example',
            'Access-Control-Request-Method' => 'GET',
        ])->options('/api/v1/me');

        $response->assertHeaderMissing('Access-Control-Allow-Origin');
    }
}
