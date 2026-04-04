<?php

namespace Tests\Unit\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_order_belongs_to_user(): void
    {
        $user  = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $order->user);
        $this->assertEquals($user->id, $order->user->id);
    }

    public function test_order_has_many_items(): void
    {
        $order = Order::factory()->create();
        OrderItem::factory()->count(2)->create(['order_id' => $order->id]);

        $this->assertCount(2, $order->items);
        $this->assertInstanceOf(OrderItem::class, $order->items->first());
    }

    public function test_order_belongs_to_coupon(): void
    {
        $coupon = Coupon::factory()->create();
        $order  = Order::factory()->create(['coupon_id' => $coupon->id]);

        $this->assertInstanceOf(Coupon::class, $order->coupon);
        $this->assertEquals($coupon->id, $order->coupon->id);
    }

    public function test_order_coupon_is_null_when_no_coupon(): void
    {
        $order = Order::factory()->create(['coupon_id' => null]);

        $this->assertNull($order->coupon);
    }

    public function test_order_status_is_cast_to_enum(): void
    {
        $order = Order::factory()->create(['status' => 'pending']);

        $this->assertInstanceOf(OrderStatus::class, $order->status);
        $this->assertSame(OrderStatus::Pending, $order->status);
    }

    public function test_order_payment_method_is_cast_to_enum(): void
    {
        $order = Order::factory()->create(['payment_method' => 'cash']);

        $this->assertInstanceOf(PaymentMethod::class, $order->payment_method);
        $this->assertSame(PaymentMethod::Cash, $order->payment_method);
    }

    public function test_order_monetary_fields_are_cast_to_integers(): void
    {
        $order = Order::factory()->create([
            'subtotal'        => 2000,
            'delivery_fee'    => 990,
            'service_fee'     => 199,
            'discount_amount' => 0,
            'total'           => 3189,
        ]);

        $this->assertIsInt($order->subtotal);
        $this->assertIsInt($order->delivery_fee);
        $this->assertIsInt($order->service_fee);
        $this->assertIsInt($order->discount_amount);
        $this->assertIsInt($order->total);
    }
}
