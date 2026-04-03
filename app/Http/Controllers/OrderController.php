<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class OrderController extends Controller
{
    const DELIVERY_FEE = 990;
    const SERVICE_FEE  = 199;

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
            'deliveryFee' => self::DELIVERY_FEE,
            'serviceFee'  => self::SERVICE_FEE,
        ]);
    }

    public function myOrders()
    {
        $user = auth()->user();

        $orders = Order::where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->get()
            ->map(fn($order) => [
                'id'             => $order->id,
                'status'         => $order->status,
                'payment_method' => $order->payment_method,
                'address'        => trim("{$order->zip} {$order->city}, {$order->street}" . ($order->note ? ", {$order->note}" : '')),
                'subtotal'       => $order->subtotal,
                'delivery_fee'   => $order->delivery_fee,
                'service_fee'    => $order->service_fee,
                'total'          => $order->total,
                'created_at'     => $order->created_at->format('Y. m. d. H:i'),
                'items'          => $order->items->map(fn($item) => [
                    'name'     => $item->name,
                    'price'    => $item->price,
                    'quantity' => $item->quantity,
                ]),
            ]);

        return Inertia::render('Orders', [
            'auth'   => ['user' => $user],
            'orders' => $orders,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items'          => ['required', 'array', 'min:1'],
            'items.*.id'     => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'payment_method' => ['required', 'in:card,cash'],
            'zip'            => ['required', 'string', 'max:10'],
            'city'           => ['required', 'string', 'max:100'],
            'street'         => ['required', 'string', 'max:255'],
            'note'             => ['nullable', 'string', 'max:255'],
            'delivery_message' => ['nullable', 'string', 'max:1000'],
            'save_address'     => ['boolean'],
        ], [
            'items.required'          => 'A kosár üres.',
            'payment_method.required' => 'Válassz fizetési módot.',
            'zip.required'            => 'Az irányítószám megadása kötelező.',
            'city.required'           => 'A város megadása kötelező.',
            'street.required'         => 'Az utca, házszám megadása kötelező.',
        ]);

        $user = auth()->user();

        // Fetch current prices from DB — never trust client-sent prices
        $productIds = collect($validated['items'])->pluck('id');
        $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

        $itemsData = collect($validated['items'])->map(function ($item) use ($products) {
            $product = $products->get($item['id']);
            return [
                'product_id' => $product->id,
                'name'       => $product->name,
                'price'      => $product->price,
                'quantity'   => $item['quantity'],
            ];
        });

        $subtotal = $itemsData->sum(fn($i) => $i['price'] * $i['quantity']);
        $total    = $subtotal + self::DELIVERY_FEE + self::SERVICE_FEE;

        DB::transaction(function () use ($validated, $user, $itemsData, $subtotal, $total) {
            $order = Order::create([
                'user_id'        => $user->id,
                'payment_method' => $validated['payment_method'],
                'zip'            => $validated['zip'],
                'city'           => $validated['city'],
                'street'         => $validated['street'],
                'note'             => $validated['note'] ?? null,
                'delivery_message' => $validated['delivery_message'] ?? null,
                'subtotal'       => $subtotal,
                'delivery_fee'   => self::DELIVERY_FEE,
                'service_fee'    => self::SERVICE_FEE,
                'total'          => $total,
            ]);

            $order->items()->createMany($itemsData->toArray());

            if (!empty($validated['save_address'])) {
                $user->update([
                    'zip'    => $validated['zip'],
                    'city'   => $validated['city'],
                    'street' => $validated['street'],
                    'note'   => $validated['note'] ?? null,
                ]);
            }
        });

        return redirect()->route('home')->with('order_success', true);
    }
}
