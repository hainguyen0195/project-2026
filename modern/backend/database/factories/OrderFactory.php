<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => 'DH-'.fake()->unique()->numerify('########'),
            'customer_name' => fake()->name(),
            'phone' => '0901234567',
            'address' => fake()->address(),
            'items' => [['name' => 'Sản phẩm', 'sku' => '', 'quantity' => 2, 'unit_price' => 100000]],
            'subtotal' => 200000,
            'total' => 200000,
            'status' => 'pending',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'history' => [],
        ];
    }
}
