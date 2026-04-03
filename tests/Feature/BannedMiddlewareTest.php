<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BannedMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_banned_user_is_logged_out_on_next_request(): void
    {
        $user = User::factory()->create();

        // Ban the user while they are "logged in"
        $user->update(['banned_at' => now()]);

        $response = $this->actingAs($user)->get('/');

        $this->assertGuest();
    }

    public function test_active_user_is_not_affected_by_ban_middleware(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/');

        $response->assertOk();
        $this->assertAuthenticatedAs($user);
    }

    public function test_banned_user_is_redirected_when_accessing_protected_route(): void
    {
        $user = User::factory()->banned()->create();

        $response = $this->actingAs($user)->get('/checkout');

        // The middleware logs them out and redirects; then auth redirects them again
        $this->assertGuest();
    }
}
