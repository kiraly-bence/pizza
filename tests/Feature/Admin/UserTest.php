<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_users(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/users');

        $response->assertOk();
    }

    public function test_admin_can_promote_user_to_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/role", ['role' => 'admin']);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $user->id, 'role' => 'admin']);
    }

    public function test_admin_can_demote_other_admin(): void
    {
        $admin      = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$otherAdmin->id}/role", ['role' => 'user']);

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $otherAdmin->id, 'role' => 'user']);
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->patch("/admin/users/{$admin->id}/role", ['role' => 'user']);

        $this->assertDatabaseHas('users', ['id' => $admin->id, 'role' => 'admin']);
    }

    public function test_admin_can_ban_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/ban");

        $response->assertRedirect();
        $this->assertNotNull($user->fresh()->banned_at);
    }

    public function test_admin_cannot_ban_themselves(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch("/admin/users/{$admin->id}/ban");

        $this->assertNull($admin->fresh()->banned_at);
    }

    public function test_admin_cannot_ban_another_admin(): void
    {
        $admin      = User::factory()->admin()->create();
        $otherAdmin = User::factory()->admin()->create();

        $this->actingAs($admin)->patch("/admin/users/{$otherAdmin->id}/ban");

        $this->assertNull($otherAdmin->fresh()->banned_at);
    }

    public function test_admin_can_unban_a_user(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->banned()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/unban");

        $response->assertRedirect();
        $this->assertNull($user->fresh()->banned_at);
    }

    public function test_non_admin_cannot_ban_users(): void
    {
        $actor = User::factory()->create();
        $user  = User::factory()->create();

        $response = $this->actingAs($actor)->patch("/admin/users/{$user->id}/ban");

        $response->assertForbidden();
        $this->assertNull($user->fresh()->banned_at);
    }

    public function test_role_update_fails_with_invalid_role(): void
    {
        $admin = User::factory()->admin()->create();
        $user  = User::factory()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/users/{$user->id}/role", ['role' => 'superuser']);

        $response->assertSessionHasErrors('role');
    }
}
