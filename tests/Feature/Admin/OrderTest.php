<?php

namespace Tests\Feature\Admin;

use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_orders(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/orders');

        $response->assertOk();
    }

    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create(['status' => 'pending']);

        $response = $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'confirmed']);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', ['id' => $order->id, 'status' => 'confirmed']);
    }

    public function test_order_status_update_fails_with_invalid_status(): void
    {
        $admin = User::factory()->admin()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($admin)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'flying']);

        $response->assertSessionHasErrors('status');
    }

    public function test_non_admin_cannot_update_order_status(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create();

        $response = $this->actingAs($user)
            ->patch("/admin/orders/{$order->id}/status", ['status' => 'confirmed']);

        $response->assertForbidden();
    }
}
