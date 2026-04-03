<?php

namespace Tests\Feature\Admin;

use App\Models\Ingredient;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IngredientTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_ingredients(): void
    {
        $admin = User::factory()->admin()->create();
        Ingredient::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/ingredients');

        $response->assertOk();
    }

    public function test_admin_can_create_ingredient(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/ingredients', ['name' => 'Mozzarella']);

        $response->assertRedirect();
        $this->assertDatabaseHas('ingredients', ['name' => 'Mozzarella']);
    }

    public function test_admin_can_update_ingredient(): void
    {
        $admin      = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create(['name' => 'Sajt']);

        $response = $this->actingAs($admin)
            ->patch("/admin/ingredients/{$ingredient->id}", ['name' => 'Mozzarella']);

        $response->assertRedirect();
        $this->assertDatabaseHas('ingredients', ['id' => $ingredient->id, 'name' => 'Mozzarella']);
    }

    public function test_admin_can_delete_ingredient(): void
    {
        $admin      = User::factory()->admin()->create();
        $ingredient = Ingredient::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/ingredients/{$ingredient->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('ingredients', ['id' => $ingredient->id]);
    }

    public function test_ingredient_name_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Ingredient::factory()->create(['name' => 'Sajt']);

        $response = $this->actingAs($admin)->post('/admin/ingredients', ['name' => 'Sajt']);

        $response->assertSessionHasErrors('name');
    }

    public function test_ingredient_creation_fails_without_name(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/ingredients', []);

        $response->assertSessionHasErrors('name');
    }
}
