<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MyOrdersTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_my_orders(): void
    {
        $response = $this->get('/rendeleseim');

        $response->assertRedirect('/');
    }

    public function test_user_can_view_their_orders(): void
    {
        $user = User::factory()->create();
        Order::factory()->count(2)->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get('/rendeleseim');

        $response->assertOk();
    }

    public function test_user_only_sees_their_own_orders(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        Order::factory()->create(['user_id' => $userA->id]);
        Order::factory()->create(['user_id' => $userB->id]);

        $response = $this->actingAs($userA)->get('/rendeleseim');

        $response->assertOk();
        $data = $response->original->getData()['page']['props']['orders'];
        $this->assertCount(1, $data);
    }
}
