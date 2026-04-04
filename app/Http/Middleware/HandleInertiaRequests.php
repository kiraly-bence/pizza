<?php

namespace App\Http\Middleware;

use App\Services\Admin\SettingService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'flash' => [
                'forgot_status'       => $request->session()->get('forgot_status'),
                'reset_success'       => $request->session()->get('reset_success'),
                'order_success'       => $request->session()->get('order_success'),
                'address_saved'       => $request->session()->get('address_saved'),
                'verified'            => $request->session()->get('verified'),
                'verification_sent'   => $request->session()->get('verification_sent'),
            ],
            'restaurant' => fn () => [
                'is_open'   => app(SettingService::class)->isOpen(),
                'is_paused' => app(SettingService::class)->isPaused(),
            ],
        ];
    }
}
