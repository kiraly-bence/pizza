<?php

namespace App\Http\Middleware;

use App\Http\Resources\UserResource;
use App\Services\Admin\SettingService;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function __construct(private readonly SettingService $settingService) {}

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => fn () => [
                'user' => $request->user() ? new UserResource($request->user()) : null,
            ],
            'flash' => [
                'forgot_status' => $request->session()->get('forgot_status'),
                'reset_success' => $request->session()->get('reset_success'),
                'order_success' => $request->session()->get('order_success'),
                'address_saved' => $request->session()->get('address_saved'),
                'profile_saved' => $request->session()->get('profile_saved'),
                'email_changed' => $request->session()->get('email_changed'),
                'password_saved' => $request->session()->get('password_saved'),
                'verified' => $request->session()->get('verified'),
                'verification_sent' => $request->session()->get('verification_sent'),
            ],
            'restaurant' => fn () => [
                'is_open' => $this->settingService->isOpen(),
                'is_paused' => $this->settingService->isPaused(),
                'opening_hours' => $this->settingService->openingHours(),
                'contact' => $this->settingService->contactInfo(),
            ],
        ];
    }
}
