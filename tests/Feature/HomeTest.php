<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_is_accessible_to_guests(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_home_page_is_accessible_to_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
    }

    public function test_home_page_includes_categories(): void
    {
        Category::factory()->count(2)->create();

        $response = $this->get('/');

        $response->assertOk();
        $data = $response->original->getData()['page']['props']['categories'];
        $this->assertCount(2, $data);
    }

    public function test_home_page_only_includes_available_products(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'is_available' => true]);
        Product::factory()->create(['category_id' => $category->id, 'is_available' => false]);

        $response = $this->get('/');

        $data     = $response->original->getData()['page']['props']['categories'];
        $products = $data[0]['products'];
        $this->assertCount(1, $products);
    }

    public function test_home_page_prefixes_product_image_with_storage_path(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create([
            'category_id' => $category->id,
            'image'       => 'products/pizza.jpg',
        ]);

        $response = $this->get('/');

        $data    = $response->original->getData()['page']['props']['categories'];
        $product = $data[0]['products'][0];
        $this->assertEquals('/storage/products/pizza.jpg', $product['image']);
    }

    public function test_home_page_leaves_null_image_as_null(): void
    {
        $category = Category::factory()->create();
        Product::factory()->create(['category_id' => $category->id, 'image' => null]);

        $response = $this->get('/');

        $data    = $response->original->getData()['page']['props']['categories'];
        $product = $data[0]['products'][0];
        $this->assertNull($product['image']);
    }

    public function test_home_page_includes_product_ingredients(): void
    {
        $category   = Category::factory()->create();
        $product    = Product::factory()->create(['category_id' => $category->id]);
        $ingredient = Ingredient::factory()->create(['name' => 'Sajt']);
        $product->ingredients()->attach($ingredient);

        $response = $this->get('/');

        $data       = $response->original->getData()['page']['props']['categories'];
        $ingredient = $data[0]['products'][0]['ingredients'][0];
        $this->assertEquals('Sajt', $ingredient['name']);
    }

    public function test_home_page_includes_product_labels(): void
    {
        $category = Category::factory()->create();
        $product  = Product::factory()->create(['category_id' => $category->id]);
        $label    = Label::factory()->create(['name' => 'Vegán']);
        $product->labels()->attach($label);

        $response = $this->get('/');

        $data  = $response->original->getData()['page']['props']['categories'];
        $label = $data[0]['products'][0]['labels'][0];
        $this->assertEquals('Vegán', $label['name']);
    }

    public function test_categories_are_ordered_by_sort_order(): void
    {
        Category::factory()->create(['name' => 'C', 'sort_order' => 3]);
        Category::factory()->create(['name' => 'A', 'sort_order' => 1]);
        Category::factory()->create(['name' => 'B', 'sort_order' => 2]);

        $response = $this->get('/');

        $data  = $response->original->getData()['page']['props']['categories'];
        $names = collect($data)->pluck('name')->toArray();
        $this->assertEquals(['A', 'B', 'C'], $names);
    }
}
