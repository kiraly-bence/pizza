<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Services\OrderService;

class SettingService
{
    public function fees(): array
    {
        return [
            'delivery_fee' => (int) Setting::get('delivery_fee', OrderService::DELIVERY_FEE),
            'service_fee'  => (int) Setting::get('service_fee',  OrderService::SERVICE_FEE),
        ];
    }

    public function updateFees(int $deliveryFee, int $serviceFee): void
    {
        Setting::set('delivery_fee', $deliveryFee);
        Setting::set('service_fee',  $serviceFee);
    }
}
