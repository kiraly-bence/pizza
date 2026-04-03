<?php

namespace App\Services\Admin;

use App\Models\Coupon;
use Illuminate\Support\Collection;

class CouponService
{
    public function all(): Collection
    {
        return Coupon::withCount('usages')->latest()->get()->map(fn($c) => [
            'id'                => $c->id,
            'code'              => $c->code,
            'discount_type'     => $c->discount_type,
            'discount_value'    => $c->discount_value,
            'max_uses_per_user' => $c->max_uses_per_user,
            'expires_at'        => $c->expires_at?->format('Y-m-d\TH:i'),
            'is_active'         => $c->is_active,
            'total_uses'        => $c->usages_count,
        ]);
    }

    public function create(array $data): Coupon
    {
        return Coupon::create($data);
    }

    public function update(Coupon $coupon, array $data): void
    {
        $coupon->update($data);
    }

    public function delete(Coupon $coupon): void
    {
        $coupon->delete();
    }

    public function toggle(Coupon $coupon): void
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
    }
}
