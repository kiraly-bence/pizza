<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'discount_type',
        'discount_value',
        'max_uses_per_user',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active'  => 'boolean',
    ];

    public function usages()
    {
        return $this->hasMany(CouponUsage::class);
    }

    public function isValidForUser(int $userId): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->max_uses_per_user !== null) {
            $userUsages = $this->usages()->where('user_id', $userId)->count();
            if ($userUsages >= $this->max_uses_per_user) {
                return false;
            }
        }

        return true;
    }

    public function calculateDiscount(int $subtotal): int
    {
        if ($this->discount_type === 'percentage') {
            return (int) round($subtotal * $this->discount_value / 100);
        }

        return (int) $this->discount_value;
    }
}
