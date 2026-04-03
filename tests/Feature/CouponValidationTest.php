<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_valid_coupon_returns_valid_true(): void
    {
        $user   = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'PIZZA10']);

        $response = $this->actingAs($user)
            ->postJson('/coupon/validate', ['code' => 'PIZZA10']);

        $response->assertOk()
            ->assertJson([
                'valid'          => true,
                'discount_type'  => 'percentage',
                'discount_value' => 10,
            ]);
    }

    public function test_invalid_code_returns_valid_false(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->postJson('/coupon/validate', ['code' => 'NOTREAL']);

        $response->assertOk()->assertJson(['valid' => false]);
    }

    public function test_expired_coupon_returns_valid_false(): void
    {
        $user   = User::factory()->create();
        $coupon = Coupon::factory()->expired()->create(['code' => 'EXPIRED']);

        $response = $this->actingAs($user)
            ->postJson('/coupon/validate', ['code' => 'EXPIRED']);

        $response->assertOk()->assertJson(['valid' => false]);
    }

    public function test_inactive_coupon_returns_valid_false(): void
    {
        $user   = User::factory()->create();
        $coupon = Coupon::factory()->inactive()->create(['code' => 'INACTIVE']);

        $response = $this->actingAs($user)
            ->postJson('/coupon/validate', ['code' => 'INACTIVE']);

        $response->assertOk()->assertJson(['valid' => false]);
    }

    public function test_coupon_exhausted_for_user_returns_valid_false(): void
    {
        $user   = User::factory()->create();
        $coupon = Coupon::factory()->maxUsesPerUser(1)->create(['code' => 'ONCE']);
        $order  = Order::factory()->create(['user_id' => $user->id]);

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id'   => $user->id,
            'order_id'  => $order->id,
        ]);

        $response = $this->actingAs($user)
            ->postJson('/coupon/validate', ['code' => 'ONCE']);

        $response->assertOk()->assertJson(['valid' => false]);
    }

    public function test_same_coupon_is_valid_for_different_user(): void
    {
        $userA  = User::factory()->create();
        $userB  = User::factory()->create();
        $coupon = Coupon::factory()->maxUsesPerUser(1)->create(['code' => 'ONCE']);
        $order  = Order::factory()->create(['user_id' => $userA->id]);

        CouponUsage::create([
            'coupon_id' => $coupon->id,
            'user_id'   => $userA->id,
            'order_id'  => $order->id,
        ]);

        $response = $this->actingAs($userB)
            ->postJson('/coupon/validate', ['code' => 'ONCE']);

        $response->assertOk()->assertJson(['valid' => true]);
    }

    public function test_code_matching_is_case_insensitive(): void
    {
        $user   = User::factory()->create();
        $coupon = Coupon::factory()->create(['code' => 'PIZZA10']);

        $response = $this->actingAs($user)
            ->postJson('/coupon/validate', ['code' => 'pizza10']);

        $response->assertOk()->assertJson(['valid' => true]);
    }

    public function test_unauthenticated_user_cannot_validate_coupon(): void
    {
        $response = $this->postJson('/coupon/validate', ['code' => 'TEST']);

        $response->assertUnauthorized();
    }
}
