<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_has_many_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->products);
        $this->assertInstanceOf(Product::class, $category->products->first());
    }

    public function test_category_products_are_ordered_by_sort_order(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'sort_order' => 3, 'name' => 'C']);
        Product::factory()->create(['category_id' => $category->id, 'sort_order' => 1, 'name' => 'A']);
        Product::factory()->create(['category_id' => $category->id, 'sort_order' => 2, 'name' => 'B']);

        $names = $category->fresh()->products->pluck('name')->toArray();

        $this->assertEquals(['A', 'B', 'C'], $names);
    }

    public function test_category_with_no_products_returns_empty_collection(): void
    {
        $category = Category::factory()->create();

        $this->assertCount(0, $category->products);
    }
}
