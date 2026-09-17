<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return ['name' => fake()->words(3, true), 'slug' => fake()->unique()->slug(), 'code' => fake()->unique()->bothify('SP-#####'), 'regular_price' => 100000, 'availability' => 'in_stock', 'schema_mode' => 'auto', 'is_active' => false, 'is_featured' => false, 'is_new' => false, 'is_bestseller' => false, 'noindex' => false, 'sort_order' => 0];
    }
}
