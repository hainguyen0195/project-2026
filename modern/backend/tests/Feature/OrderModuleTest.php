<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderModuleTest extends TestCase
{
    use RefreshDatabase;

    private function login(array $permissions): void
    {
        $this->app['auth']->forgetGuards();
        $role = Role::factory()->create(['permissions' => $permissions]);
        $user = User::factory()->create(['role_id' => $role->id, 'is_active' => true, 'must_change_password' => false]);
        $this->actingAs($user, 'web')->withSession(['cms_auth_version' => 0]);
    }

    private function payload(): array
    {
        return ['customer_name' => 'Nguyễn An', 'phone' => '0901234567', 'address' => 'Hồ Chí Minh', 'payment_method' => 'cod', 'shipping_fee' => 30000, 'discount' => 10000, 'items' => [['name' => 'Sản phẩm', 'sku' => 'SP01', 'quantity' => 2, 'unit_price' => 100000]]];
    }

    public function test_creation_calculates_totals_and_ignores_client_status_and_total(): void
    {
        $this->login(['orders.view', 'orders.manage']);
        $this->getJson('/api/v1/cms/order-config')->assertOk()->assertJsonPath('data.statuses.pending.vi', 'Chờ xác nhận');
        $response = $this->postJson('/api/v1/cms/orders', [...$this->payload(), 'total' => 1, 'status' => 'completed', 'payment_status' => 'paid']);
        $response->assertCreated()->assertJsonPath('data.subtotal', 200000)->assertJsonPath('data.total', 220000)->assertJsonPath('data.status', 'pending')->assertJsonPath('data.payment_status', 'unpaid')->assertJsonCount(1, 'data.history');
        $this->getJson('/api/v1/cms/orders/'.$response->json('data.id'))->assertOk()->assertJsonPath('data.items.0.sku', 'SP01');
        $this->postJson('/api/v1/cms/orders', [...$this->payload(), 'discount' => 300000])->assertUnprocessable()->assertJsonValidationErrors('discount');
        $this->postJson('/api/v1/cms/orders', [...$this->payload(), 'items' => []])->assertUnprocessable();
        $this->postJson('/api/v1/cms/orders', [...$this->payload(), 'payment_method' => 'invalid'])->assertUnprocessable();
        $this->assertDatabaseCount('orders', 1);
    }

    public function test_transitions_history_and_stale_writes(): void
    {
        $this->login(['orders.view', 'orders.manage']);
        $order = Order::factory()->create();
        $url = '/api/v1/cms/orders/'.$order->id;
        $data = ['status' => 'confirmed', 'payment_status' => 'paid', 'version' => 1, 'internal_note' => 'Đã gọi'];
        $this->putJson($url, $data)->assertOk()->assertJsonPath('data.version', 2)->assertJsonCount(1, 'data.history');
        $this->putJson($url, $data)->assertConflict();
        $this->putJson($url, [...$data, 'status' => 'completed', 'version' => 2])->assertUnprocessable();
        $this->putJson($url, [...$data, 'status' => 'shipping', 'version' => 2])->assertOk();
        $this->putJson($url, [...$data, 'status' => 'completed', 'version' => 3])->assertOk();
        $this->putJson($url, [...$data, 'status' => 'pending', 'version' => 4])->assertUnprocessable();
        $this->assertSame(4, $order->fresh()->version);
    }

    public function test_filters_pagination_and_permissions(): void
    {
        $this->getJson('/api/v1/cms/orders')->assertUnauthorized();
        $this->login(['orders.view']);
        Order::factory()->count(22)->create();
        $target = Order::factory()->create(['customer_name' => 'Unique customer', 'status' => 'completed']);
        $this->getJson('/api/v1/cms/orders')->assertOk()->assertJsonCount(20, 'data')->assertJsonPath('total', 23);
        $this->getJson('/api/v1/cms/orders?page=2')->assertOk()->assertJsonCount(3, 'data');
        $this->getJson('/api/v1/cms/orders?status=completed&search=Unique')->assertOk()->assertJsonPath('data.0.id', $target->id)->assertJsonPath('total', 1);
        $this->getJson('/api/v1/cms/orders?to='.now()->format('Y-m-d'))->assertOk();
        $this->getJson('/api/v1/cms/orders?from=2026-09-15&to=2026-09-01')->assertUnprocessable();
        $this->postJson('/api/v1/cms/orders', $this->payload())->assertForbidden();
        $this->putJson('/api/v1/cms/orders/'.$target->id, [])->assertForbidden();
        $this->login(['products.view']);
        $this->getJson('/api/v1/cms/orders')->assertForbidden();
        $this->getJson('/api/v1/cms/order-config')->assertForbidden();
        $this->getJson('/api/v1/cms/orders/'.$target->id)->assertForbidden();
    }
}
