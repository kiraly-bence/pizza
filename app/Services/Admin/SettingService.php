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
            'service_fee' => (int) Setting::get('service_fee', OrderService::SERVICE_FEE),
        ];
    }

    public function updateFees(int $deliveryFee, int $serviceFee): void
    {
        Setting::set('delivery_fee', $deliveryFee);
        Setting::set('service_fee', $serviceFee);
    }

    public function openingHours(): array
    {
        return json_decode(Setting::get('opening_hours', '[]'), true);
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

    public function contactInfo(): array
    {
        return [
            'phone' => Setting::get('contact_phone', ''),
            'email' => Setting::get('contact_email', ''),
            'address' => Setting::get('contact_address', ''),
        ];
    }

    public function updateContactInfo(string $phone, string $email, string $address): void
    {
        Setting::set('contact_phone', $phone);
        Setting::set('contact_email', $email);
        Setting::set('contact_address', $address);
    }

    public function isOpen(): bool
    {
        if ($this->isPaused()) {
            return false;
        }

        $hours = $this->openingHours();

        if (empty($hours)) {
            return true; // no hours configured → always open
        }

        $day = ((int) now()->format('w') + 6) % 7; // convert: Mon=0 … Sun=6
        $dayHours = $hours[$day] ?? null;

        if (! $dayHours || ($dayHours['closed'] ?? false)) {
            return false;
        }

        $now = now()->format('H:i');

        return $now >= $dayHours['open'] && $now < $dayHours['close'];
    }
}
