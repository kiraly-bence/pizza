<?php

namespace Tests\Feature\Admin;

use App\Models\Label;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LabelTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_labels(): void
    {
        $admin = User::factory()->admin()->create();
        Label::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/labels');

        $response->assertOk();
    }

    public function test_admin_can_create_label(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/labels', [
            'name' => 'Vegán',
            'type' => 'primary',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('labels', ['name' => 'Vegán']);
    }

    public function test_admin_can_update_label(): void
    {
        $admin = User::factory()->admin()->create();
        $label = Label::factory()->create(['name' => 'Régi']);

        $response = $this->actingAs($admin)
            ->patch("/admin/labels/{$label->id}", ['name' => 'Új', 'type' => 'secondary']);

        $response->assertRedirect();
        $this->assertDatabaseHas('labels', ['id' => $label->id, 'name' => 'Új']);
    }

    public function test_admin_can_delete_label(): void
    {
        $admin = User::factory()->admin()->create();
        $label = Label::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/labels/{$label->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('labels', ['id' => $label->id]);
    }

    public function test_label_creation_fails_without_name(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/labels', ['type' => 'badge']);

        $response->assertSessionHasErrors('name');
    }
}
