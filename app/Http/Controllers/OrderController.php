<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\Admin\SettingService;
use App\Services\OrderService;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function checkout()
    {
        $user = auth()->user();

        return Inertia::render('Checkout', [
            'auth'         => ['user' => $user],
            'savedAddress' => [
                'zip'    => $user->zip,
                'city'   => $user->city,
                'street' => $user->street,
                'note'   => $user->note,
            ],
            'deliveryFee' => $this->orderService->deliveryFee(),
            'serviceFee'  => $this->orderService->serviceFee(),
        ]);
    }

    public function myOrders()
    {
        $user = auth()->user();

        return Inertia::render('Orders', [
            'auth'   => ['user' => $user],
            'orders' => $this->orderService->forUser($user),
        ]);
    }

    public function store(PlaceOrderRequest $request, SettingService $settingService)
    {
        if (!$settingService->isOpen()) {
            throw ValidationException::withMessages([
                'store' => 'Az étterem jelenleg nem fogad rendeléseket.',
            ]);
        }

        $this->orderService->place(auth()->user(), $request->validated());

        return redirect()->route('home')->with('order_success', true);
    }
}
