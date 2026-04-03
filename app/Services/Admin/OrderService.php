<?php

namespace App\Services\Admin;

use App\Models\Order;
use Illuminate\Support\Collection;

class OrderService
{
    public function all(): Collection
    {
        return Order::with(['user', 'items'])
            ->latest()
            ->get()
            ->map(fn($o) => [
                'id'               => $o->id,
                'user'             => ['name' => $o->user->name, 'email' => $o->user->email],
                'status'           => $o->status,
                'payment_method'   => $o->payment_method,
                'address'          => trim("{$o->zip} {$o->city}, {$o->street}" . ($o->note ? ", {$o->note}" : '')),
                'delivery_message' => $o->delivery_message,
                'items_count'      => $o->items->sum('quantity'),
                'subtotal'         => $o->subtotal,
                'delivery_fee'     => $o->delivery_fee,
                'service_fee'      => $o->service_fee,
                'total'            => $o->total,
                'items'            => $o->items->map(fn($i) => [
                    'name'     => $i->name,
                    'price'    => $i->price,
                    'quantity' => $i->quantity,
                ]),
                'created_at' => $o->created_at->format('Y. m. d. H:i'),
            ]);
    }

    public function updateStatus(Order $order, string $status): void
    {
        $order->update(['status' => $status]);
    }
}
