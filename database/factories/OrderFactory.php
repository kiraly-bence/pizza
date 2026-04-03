<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal    = fake()->numberBetween(1000, 5000);
        $deliveryFee = 990;
        $serviceFee  = 199;

        return [
            'user_id'          => User::factory(),
            'coupon_id'        => null,
            'status'           => 'pending',
            'payment_method'   => fake()->randomElement(['cash', 'card']),
            'zip'              => '1000',
            'city'             => 'Budapest',
            'street'           => 'Fő utca 1.',
            'note'             => null,
            'delivery_message' => null,
            'subtotal'         => $subtotal,
            'delivery_fee'     => $deliveryFee,
            'service_fee'      => $serviceFee,
            'discount_amount'  => 0,
            'total'            => $subtotal + $deliveryFee + $serviceFee,
        ];
    }
}
