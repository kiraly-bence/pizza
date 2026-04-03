<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function orderPayload(array $products, array $overrides = []): array
    {
        return array_merge([
            'payment_method'   => 'cash',
            'zip'              => '1000',
            'city'             => 'Budapest',
            'street'           => 'Fő utca 1.',
            'note'             => null,
            'delivery_message' => null,
            'save_address'     => false,
            'items'            => collect($products)->map(fn($p) => ['id' => $p->id, 'quantity' => 1])->toArray(),
        ], $overrides);
    }

    public function test_guest_cannot_access_checkout(): void
    {
        $response = $this->get('/checkout');

        $response->assertRedirect('/');
    }

    public function test_authenticated_user_can_view_checkout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/checkout');

        $response->assertOk();
    }

    public function test_user_can_place_an_order(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $response = $this->actingAs($user)->post('/orders', $this->orderPayload([$product]));

        $response->assertRedirect('/');
        $this->assertDatabaseHas('orders', ['user_id' => $user->id]);
    }

    public function test_order_totals_are_calculated_correctly(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $this->actingAs($user)->post('/orders', $this->orderPayload([$product]));

        $order = Order::first();
        $this->assertEquals(2000, $order->subtotal);
        $this->assertEquals(OrderService::DELIVERY_FEE, $order->delivery_fee);
        $this->assertEquals(OrderService::SERVICE_FEE, $order->service_fee);
        $this->assertEquals(2000 + OrderService::DELIVERY_FEE + OrderService::SERVICE_FEE, $order->total);
    }

    public function test_order_items_are_saved(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $this->actingAs($user)->post('/orders', $this->orderPayload([$product]));

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'price'      => 2000,
            'quantity'   => 1,
        ]);
    }

    public function test_order_with_valid_coupon_applies_discount(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);
        $coupon  = Coupon::factory()->create(['code' => 'SAVE10', 'discount_type' => 'percentage', 'discount_value' => 10]);

        $this->actingAs($user)->post('/orders', $this->orderPayload([$product], ['coupon_code' => 'SAVE10']));

        $order = Order::first();
        $this->assertEquals(200, $order->discount_amount); // 10% of 2000
        $this->assertEquals(2000 + OrderService::DELIVERY_FEE + OrderService::SERVICE_FEE - 200, $order->total);
        $this->assertEquals($coupon->id, $order->coupon_id);
    }

    public function test_coupon_usage_is_recorded_after_order(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);
        $coupon  = Coupon::factory()->create(['code' => 'SAVE10']);

        $this->actingAs($user)->post('/orders', $this->orderPayload([$product], ['coupon_code' => 'SAVE10']));

        $this->assertDatabaseHas('coupon_usages', [
            'coupon_id' => $coupon->id,
            'user_id'   => $user->id,
        ]);
    }

    public function test_invalid_coupon_code_is_silently_ignored(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $this->actingAs($user)->post('/orders', $this->orderPayload([$product], ['coupon_code' => 'FAKE']));

        $order = Order::first();
        $this->assertEquals(0, $order->discount_amount);
        $this->assertNull($order->coupon_id);
    }

    public function test_save_address_persists_address_to_user(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $this->actingAs($user)->post('/orders', $this->orderPayload([$product], [
            'zip'          => '2000',
            'city'         => 'Szentendre',
            'street'       => 'Kossuth u. 5.',
            'save_address' => true,
        ]));

        $this->assertDatabaseHas('users', [
            'id'   => $user->id,
            'zip'  => '2000',
            'city' => 'Szentendre',
        ]);
    }

    public function test_order_fails_without_address(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $response = $this->actingAs($user)->post('/orders', [
            'payment_method' => 'cash',
            'items'          => [['id' => $product->id, 'quantity' => 1]],
        ]);

        $response->assertSessionHasErrors(['zip', 'city', 'street']);
    }

    public function test_order_fails_without_items(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/orders', [
            'payment_method' => 'cash',
            'zip'            => '1000',
            'city'           => 'Budapest',
            'street'         => 'Fő u. 1.',
            'items'          => [],
        ]);

        $response->assertSessionHasErrors('items');
    }

    public function test_order_fails_with_invalid_payment_method(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 2000]);

        $response = $this->actingAs($user)->post('/orders', $this->orderPayload([$product], ['payment_method' => 'bitcoin']));

        $response->assertSessionHasErrors('payment_method');
    }

    public function test_guest_cannot_place_order(): void
    {
        $product = Product::factory()->create(['price' => 2000]);

        $response = $this->post('/orders', $this->orderPayload([$product]));

        $response->assertRedirect('/');
        $this->assertDatabaseEmpty('orders');
    }

    public function test_order_total_does_not_go_below_zero_with_large_discount(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['price' => 100]);
        $coupon  = Coupon::factory()->fixed(99999)->create(['code' => 'HUGE']);

        $this->actingAs($user)->post('/orders', $this->orderPayload([$product], ['coupon_code' => 'HUGE']));

        $order = Order::first();
        $this->assertEquals(0, $order->total);
    }
}
