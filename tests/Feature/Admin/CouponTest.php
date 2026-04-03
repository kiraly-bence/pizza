<?php

namespace Tests\Feature\Admin;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CouponTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_coupons(): void
    {
        $admin = User::factory()->admin()->create();
        Coupon::factory()->count(3)->create();

        $response = $this->actingAs($admin)->get('/admin/coupons');

        $response->assertOk();
    }

    public function test_admin_can_create_percentage_coupon(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/coupons', [
            'code'              => 'SAVE10',
            'discount_type'     => 'percentage',
            'discount_value'    => 10,
            'max_uses_per_user' => null,
            'expires_at'        => null,
            'is_active'         => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('coupons', ['code' => 'SAVE10', 'discount_type' => 'percentage']);
    }

    public function test_admin_can_create_fixed_coupon(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/coupons', [
            'code'           => 'FLAT500',
            'discount_type'  => 'fixed',
            'discount_value' => 500,
            'is_active'      => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('coupons', ['code' => 'FLAT500', 'discount_type' => 'fixed']);
    }

    public function test_admin_can_create_coupon_with_expiry(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/coupons', [
            'code'           => 'XMAS',
            'discount_type'  => 'percentage',
            'discount_value' => 20,
            'expires_at'     => now()->addDays(30)->format('Y-m-d\TH:i'),
            'is_active'      => true,
        ]);

        $this->assertDatabaseHas('coupons', ['code' => 'XMAS']);
        $this->assertNotNull(Coupon::where('code', 'XMAS')->first()->expires_at);
    }

    public function test_admin_can_create_coupon_with_max_uses_per_user(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->post('/admin/coupons', [
            'code'              => 'ONCE',
            'discount_type'     => 'percentage',
            'discount_value'    => 5,
            'max_uses_per_user' => 1,
            'is_active'         => true,
        ]);

        $this->assertDatabaseHas('coupons', ['code' => 'ONCE', 'max_uses_per_user' => 1]);
    }

    public function test_admin_can_update_coupon(): void
    {
        $admin  = User::factory()->admin()->create();
        $coupon = Coupon::factory()->create(['code' => 'OLD']);

        $response = $this->actingAs($admin)->patch("/admin/coupons/{$coupon->id}", [
            'code'           => 'NEW',
            'discount_type'  => 'percentage',
            'discount_value' => 15,
            'is_active'      => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'code' => 'NEW', 'discount_value' => 15]);
    }

    public function test_admin_can_delete_coupon(): void
    {
        $admin  = User::factory()->admin()->create();
        $coupon = Coupon::factory()->create();

        $response = $this->actingAs($admin)->delete("/admin/coupons/{$coupon->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('coupons', ['id' => $coupon->id]);
    }

    public function test_admin_can_toggle_coupon_active_state(): void
    {
        $admin  = User::factory()->admin()->create();
        $coupon = Coupon::factory()->create(['is_active' => true]);

        $this->actingAs($admin)->patch("/admin/coupons/{$coupon->id}/toggle");

        $this->assertFalse($coupon->fresh()->is_active);

        $this->actingAs($admin)->patch("/admin/coupons/{$coupon->id}/toggle");

        $this->assertTrue($coupon->fresh()->is_active);
    }

    public function test_coupon_code_must_be_unique(): void
    {
        $admin = User::factory()->admin()->create();
        Coupon::factory()->create(['code' => 'DUPE']);

        $response = $this->actingAs($admin)->post('/admin/coupons', [
            'code'           => 'DUPE',
            'discount_type'  => 'percentage',
            'discount_value' => 10,
            'is_active'      => true,
        ]);

        $response->assertSessionHasErrors('code');
    }

    public function test_coupon_code_can_be_reused_on_update_for_same_coupon(): void
    {
        $admin  = User::factory()->admin()->create();
        $coupon = Coupon::factory()->create(['code' => 'MYCODE']);

        $response = $this->actingAs($admin)->patch("/admin/coupons/{$coupon->id}", [
            'code'           => 'MYCODE',
            'discount_type'  => 'percentage',
            'discount_value' => 20,
            'is_active'      => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('coupons', ['id' => $coupon->id, 'discount_value' => 20]);
    }

    public function test_coupon_creation_fails_without_required_fields(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/admin/coupons', []);

        $response->assertSessionHasErrors(['code', 'discount_type', 'discount_value']);
    }

    public function test_non_admin_cannot_manage_coupons(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin/coupons');

        $response->assertForbidden();
    }
}
