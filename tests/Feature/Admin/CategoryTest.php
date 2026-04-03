<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_categories(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/categories');

        $response->assertOk();
    }

    public function test_admin_can_create_category(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/categories', [
            'name'       => 'Pizzák',
            'sort_order' => 1,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['name' => 'Pizzák']);
    }

    public function test_admin_can_update_category(): void
    {
        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create(['name' => 'Régi']);

        $response = $this->actingAs($admin)
            ->patch("/admin/categories/{$category->id}", [
                'name'       => 'Új',
                'sort_order' => 2,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Új']);
    }

    public function test_admin_can_delete_category(): void
    {
        $admin    = User::factory()->admin()->create();
        $category = Category::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/categories/{$category->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_category_creation_fails_without_name(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/categories', ['sort_order' => 0]);

        $response->assertSessionHasErrors('name');
    }

    public function test_non_admin_cannot_create_category(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/admin/categories', [
            'name'       => 'Pizzák',
            'sort_order' => 1,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('categories', ['name' => 'Pizzák']);
    }
}
