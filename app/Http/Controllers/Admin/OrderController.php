<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with(['user', 'items'])
            ->latest()
            ->get()
            ->map(fn($o) => [
                'id'             => $o->id,
                'user'           => ['name' => $o->user->name, 'email' => $o->user->email],
                'status'         => $o->status,
                'payment_method' => $o->payment_method,
                'address'        => trim("{$o->zip} {$o->city}, {$o->street}" . ($o->note ? ", {$o->note}" : '')),
                'delivery_message' => $o->delivery_message,
                'items_count'    => $o->items->sum('quantity'),
                'subtotal'       => $o->subtotal,
                'delivery_fee'   => $o->delivery_fee,
                'service_fee'    => $o->service_fee,
                'total'          => $o->total,
                'items'          => $o->items->map(fn($i) => [
                    'name'     => $i->name,
                    'price'    => $i->price,
                    'quantity' => $i->quantity,
                ]),
                'created_at' => $o->created_at->format('Y. m. d. H:i'),
            ]);

        return Inertia::render('Admin/Orders', [
            'auth'   => ['user' => auth()->user()],
            'orders' => $orders,
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => ['required', 'in:pending,confirmed,preparing,delivering,delivered,cancelled'],
        ]);

        $order->update(['status' => $request->status]);

        return back();
    }
}
