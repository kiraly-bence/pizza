<?php

namespace Tests\Unit\Models;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_is_valid_for_user_returns_false_when_inactive(): void
    {
        $coupon = Coupon::factory()->inactive()->create();

        $this->assertFalse($coupon->isValidForUser(1));
    }

    public function test_is_valid_for_user_returns_false_when_expired(): void
    {
        $coupon = Coupon::factory()->expired()->create();

        $this->assertFalse($coupon->isValidForUser(1));
    }

    public function test_is_valid_for_user_returns_true_when_active_and_not_expired(): void
    {
        $coupon = Coupon::factory()->create(['expires_at' => now()->addDay()]);

        $this->assertTrue($coupon->isValidForUser(1));
    }

    public function test_is_valid_for_user_returns_false_when_user_exhausted_max_uses(): void
    {
        $coupon = Coupon::factory()->maxUsesPerUser(1)->create();
        $userA  = User::factory()->create();
        $order  = Order::factory()->create(['user_id' => $userA->id]);

        CouponUsage::create(['coupon_id' => $coupon->id, 'user_id' => $userA->id, 'order_id' => $order->id]);

        $this->assertFalse($coupon->isValidForUser($userA->id));
    }

    public function test_is_valid_for_user_allows_different_users_independently(): void
    {
        $coupon = Coupon::factory()->maxUsesPerUser(1)->create();
        $userA  = User::factory()->create();
        $userB  = User::factory()->create();
        $order  = Order::factory()->create(['user_id' => $userA->id]);

        CouponUsage::create(['coupon_id' => $coupon->id, 'user_id' => $userA->id, 'order_id' => $order->id]);

        $this->assertTrue($coupon->isValidForUser($userB->id));
    }

    public function test_calculate_discount_for_percentage(): void
    {
        $coupon = new Coupon(['discount_type' => 'percentage', 'discount_value' => 10]);

        $this->assertEquals(300, $coupon->calculateDiscount(3000));
    }

    public function test_calculate_discount_for_fixed(): void
    {
        $coupon = new Coupon(['discount_type' => 'fixed', 'discount_value' => 500]);

        $this->assertEquals(500, $coupon->calculateDiscount(3000));
    }

    public function test_calculate_discount_rounds_percentage_correctly(): void
    {
        $coupon = new Coupon(['discount_type' => 'percentage', 'discount_value' => 10]);

        $this->assertEquals(100, $coupon->calculateDiscount(1001));
    }
}
