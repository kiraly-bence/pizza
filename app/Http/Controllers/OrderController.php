<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceOrderRequest;
use App\Services\Admin\SettingService;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class OrderController extends Controller
{
    public function __construct(private readonly OrderService $orderService) {}

    public function checkout(): Response
    {
        $user = auth()->user();

        return Inertia::render('Checkout', [
            'savedAddress' => [
                'zip' => $user->zip,
                'city' => $user->city,
                'street' => $user->street,
                'note' => $user->note,
            ],
            'deliveryFee' => $this->orderService->deliveryFee(),
            'serviceFee' => $this->orderService->serviceFee(),
        ]);
    }

    public function myOrders(): Response
    {
        return Inertia::render('Orders', [
            'orders' => $this->orderService->forUser(auth()->user()),
        ]);
    }

    public function store(PlaceOrderRequest $request, SettingService $settingService): RedirectResponse
    {
        if (! auth()->user()->hasVerifiedEmail()) {
            throw ValidationException::withMessages([
                'store' => 'A rendelés leadásához előbb erősítsd meg az e-mail címedet.',
            ]);
        }

        if (! $settingService->isOpen()) {
            throw ValidationException::withMessages([
                'store' => 'Az étterem jelenleg nem fogad rendeléseket.',
            ]);
        }

        $this->orderService->place(auth()->user(), $request->validated());

        return redirect()->route('home')->with('order_success', true);
    }
}
