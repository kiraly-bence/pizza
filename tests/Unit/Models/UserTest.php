<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    public function test_is_admin_returns_true_for_admin_role(): void
    {
        $user = new User(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
    }

    public function test_is_admin_returns_false_for_user_role(): void
    {
        $user = new User(['role' => 'user']);

        $this->assertFalse($user->isAdmin());
    }

    public function test_is_banned_returns_true_when_banned_at_is_set(): void
    {
        $user = User::factory()->banned()->create();

        $this->assertTrue($user->isBanned());
    }

    public function test_is_banned_returns_false_when_banned_at_is_null(): void
    {
        $user = User::factory()->create();

        $this->assertFalse($user->isBanned());
    }
}
