<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductCategoryFactory extends Factory
{
    public function definition(): array
    {
        return ['kind' => 'category', 'name' => fake()->words(3, true), 'slug' => fake()->unique()->slug(), 'is_active' => true, 'is_featured' => false, 'sort_order' => 0];
    }
}
