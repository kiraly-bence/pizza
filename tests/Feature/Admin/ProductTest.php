<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    private function productPayload(int $categoryId, array $overrides = []): array
    {
        return array_merge([
            'category_id'  => $categoryId,
            'name'         => 'Teszt Pizza',
            'description'  => 'Finom pizza',
            'price'        => 2490,
            'sort_order'   => 0,
            'is_available' => true,
            'ingredients'  => [],
            'labels'       => [],
        ], $overrides);
    }

    public function test_admin_can_view_products(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/products');

        $response->assertOk();
    }

    public function test_admin_can_create_product_without_image(): void
    {
        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->post('/admin/products', $this->productPayload($category->id));

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['name' => 'Teszt Pizza']);
    }

    public function test_admin_can_create_product_with_image(): void
    {
        Storage::fake('public');

        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->post('/admin/products', array_merge(
            $this->productPayload($category->id),
            ['image' => UploadedFile::fake()->image('pizza.jpg')]
        ));

        $response->assertRedirect();
        $product = Product::first();
        $this->assertNotNull($product->image);
        Storage::disk('public')->assertExists($product->image);
    }

    public function test_admin_can_create_product_with_ingredients_and_labels(): void
    {
        $admin      = User::factory()->admin()->create();
        $category   = Category::factory()->create();
        $ingredient = Ingredient::factory()->create();
        $label      = Label::factory()->create();

        $this->actingAs($admin)->post('/admin/products', $this->productPayload($category->id, [
            'ingredients' => [$ingredient->id],
            'labels'      => [$label->id],
        ]));

        $product = Product::first();
        $this->assertTrue($product->ingredients->contains($ingredient));
        $this->assertTrue($product->labels->contains($label));
    }

    public function test_admin_can_update_product(): void
    {
        $admin    = User::factory()->admin()->create();
        $product  = Product::factory()->create(['name' => 'Régi Név']);
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/products/{$product->id}", $this->productPayload($category->id, ['name' => 'Új Név']));

        $response->assertRedirect();
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Új Név']);
    }

    public function test_admin_can_delete_product(): void
    {
        Storage::fake('public');

        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create(['image' => 'products/test.jpg']);
        Storage::disk('public')->put('products/test.jpg', 'fake');

        $response = $this->actingAs($admin)->delete("/admin/products/{$product->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
        Storage::disk('public')->assertMissing('products/test.jpg');
    }

    public function test_product_creation_fails_without_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/products', []);

        $response->assertSessionHasErrors(['category_id', 'name', 'price']);
    }

    public function test_updating_product_with_new_image_deletes_old_image(): void
    {
        Storage::fake('public');

        $admin   = User::factory()->admin()->create();
        $product = Product::factory()->create(['image' => 'products/old.jpg']);
        Storage::disk('public')->put('products/old.jpg', 'fake');
        $category = Category::factory()->create();

        $this->actingAs($admin)->patch("/admin/products/{$product->id}", array_merge(
            $this->productPayload($category->id),
            ['image' => UploadedFile::fake()->image('new.jpg')]
        ));

        Storage::disk('public')->assertMissing('products/old.jpg');
    }
}
