<?php

namespace Database\Factories;

use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        return [
            'code'               => strtoupper(Str::random(8)),
            'discount_type'      => 'percentage',
            'discount_value'     => 10,
            'max_uses_per_user'  => null,
            'expires_at'         => null,
            'is_active'          => true,
        ];
    }

    public function fixed(int $amount = 500): static
    {
        return $this->state(['discount_type' => 'fixed', 'discount_value' => $amount]);
    }

    public function expired(): static
    {
        return $this->state(['expires_at' => now()->subDay()]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function maxUsesPerUser(int $max): static
    {
        return $this->state(['max_uses_per_user' => $max]);
    }
}
