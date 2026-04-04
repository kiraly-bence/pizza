<?php

namespace Tests\Feature\Admin;

use App\Enums\OrderStatus;
use App\Models\Category;
use App\Models\Ingredient;
use App\Models\Label;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertOk();
    }

    public function test_non_admin_cannot_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');

        $response->assertForbidden();
    }

    public function test_guest_cannot_view_dashboard(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect();
    }

    public function test_dashboard_shows_correct_order_count(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->count(5)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(5, $stats['orders']);
    }

    public function test_dashboard_shows_correct_pending_count(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->count(3)->create(['status' => 'pending']);
        Order::factory()->count(2)->create(['status' => 'delivered']);

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(3, $stats['pending']);
    }

    public function test_dashboard_revenue_excludes_cancelled_orders(): void
    {
        $admin = User::factory()->admin()->create();
        Order::factory()->create(['total' => 3000, 'status' => 'delivered']);
        Order::factory()->create(['total' => 2000, 'status' => 'cancelled']);

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(3000, $stats['revenue']);
    }

    public function test_dashboard_shows_correct_user_count(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->count(4)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(5, $stats['users']); // 4 + admin
    }

    public function test_dashboard_shows_correct_product_count(): void
    {
        $admin = User::factory()->admin()->create();
        Product::factory()->count(7)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(7, $stats['products']);
    }

    public function test_dashboard_shows_correct_category_count(): void
    {
        $admin = User::factory()->admin()->create();
        Category::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(3, $stats['categories']);
    }

    public function test_dashboard_shows_correct_ingredient_count(): void
    {
        $admin = User::factory()->admin()->create();
        Ingredient::factory()->count(10)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(10, $stats['ingredients']);
    }

    public function test_dashboard_shows_correct_label_count(): void
    {
        $admin = User::factory()->admin()->create();
        Label::factory()->count(4)->create();

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(4, $stats['labels']);
    }

    public function test_dashboard_revenue_sums_all_non_cancelled_statuses(): void
    {
        $admin = User::factory()->admin()->create();

        foreach (['pending', 'confirmed', 'preparing', 'delivering', 'delivered'] as $status) {
            Order::factory()->create(['total' => 1000, 'status' => $status]);
        }
        Order::factory()->create(['total' => 9999, 'status' => 'cancelled']);

        $response = $this->actingAs($admin)->get('/admin');

        $stats = $response->original->getData()['page']['props']['stats'];
        $this->assertEquals(5000, $stats['revenue']);
    }
}
