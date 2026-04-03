<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        return [
            'category_id'  => Category::factory(),
            'name'         => fake()->unique()->words(2, true),
            'description'  => fake()->sentence(),
            'image'        => null,
            'price'        => fake()->numberBetween(800, 4000),
            'sort_order'   => 0,
            'is_available' => true,
        ];
    }
}
