<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_belongs_to_category(): void
    {
        $category = Category::factory()->create();
        $product  = Product::factory()->create(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $product->category);
        $this->assertEquals($category->id, $product->category->id);
    }

    public function test_product_belongs_to_many_ingredients(): void
    {
        $product    = Product::factory()->create();
        $ingredient = Ingredient::factory()->create();
        $product->ingredients()->attach($ingredient);

        $this->assertCount(1, $product->ingredients);
        $this->assertInstanceOf(Ingredient::class, $product->ingredients->first());
    }

    public function test_product_belongs_to_many_labels(): void
    {
        $product = Product::factory()->create();
        $label   = Label::factory()->create();
        $product->labels()->attach($label);

        $this->assertCount(1, $product->labels);
        $this->assertInstanceOf(Label::class, $product->labels->first());
    }

    public function test_product_price_is_cast_to_integer(): void
    {
        $product = Product::factory()->create(['price' => 2490]);

        $this->assertIsInt($product->price);
        $this->assertEquals(2490, $product->price);
    }

    public function test_product_sale_price_is_cast_to_integer_when_set(): void
    {
        $product = Product::factory()->create(['price' => 2490, 'sale_price' => 1990]);

        $this->assertIsInt($product->sale_price);
        $this->assertEquals(1990, $product->sale_price);
    }

    public function test_product_sale_price_is_null_by_default(): void
    {
        $product = Product::factory()->create();

        $this->assertNull($product->sale_price);
    }

    public function test_product_is_available_is_cast_to_boolean(): void
    {
        $product = Product::factory()->create(['is_available' => true]);

        $this->assertIsBool($product->is_available);
        $this->assertTrue($product->is_available);
    }
}
