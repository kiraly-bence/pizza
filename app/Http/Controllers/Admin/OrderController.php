<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateOrderStatusRequest;
use App\Models\Order;
use App\Services\Admin\OrderService;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function __construct(private OrderService $orderService) {}

    public function index()
    {
        return Inertia::render('Admin/Orders', [
            'auth'   => ['user' => auth()->user()],
            'orders' => $this->orderService->all(),
        ]);
    }

    public function updateStatus(UpdateOrderStatusRequest $request, Order $order)
    {
        $this->orderService->updateStatus($order, $request->validated('status'));

        return back();
    }
}
