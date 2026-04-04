<?php

namespace App\Services\Admin;

use App\Models\Setting;
use App\Services\OrderService;

class SettingService
{
    const DEFAULT_HOURS = [
        0 => ['open' => '11:00', 'close' => '22:00', 'closed' => false],
        1 => ['open' => '11:00', 'close' => '22:00', 'closed' => false],
        2 => ['open' => '11:00', 'close' => '22:00', 'closed' => false],
        3 => ['open' => '11:00', 'close' => '22:00', 'closed' => false],
        4 => ['open' => '11:00', 'close' => '22:00', 'closed' => false],
        5 => ['open' => '11:00', 'close' => '23:00', 'closed' => false],
        6 => ['open' => '11:00', 'close' => '23:00', 'closed' => false],
    ];

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

    public function openingHours(): array
    {
        $stored = Setting::get('opening_hours');

        return $stored ? json_decode($stored, true) : self::DEFAULT_HOURS;
    }

    public function updateOpeningHours(array $hours): void
    {
        Setting::set('opening_hours', json_encode($hours));
    }

    public function isPaused(): bool
    {
        return (bool) Setting::get('orders_paused', false);
    }

    public function setPaused(bool $paused): void
    {
        Setting::set('orders_paused', $paused ? '1' : '0');
    }

    public function isOpen(): bool
    {
        if ($this->isPaused()) return false;

        $stored = Setting::get('opening_hours');
        if (!$stored) return true; // no hours configured → always open

        $hours     = json_decode($stored, true);
        $day       = (int) now()->format('w'); // 0 = Sunday
        $dayHours  = $hours[$day] ?? null;

        if (!$dayHours || ($dayHours['closed'] ?? false)) return false;

        $now = now()->format('H:i');

        return $now >= $dayHours['open'] && $now < $dayHours['close'];
    }
}
