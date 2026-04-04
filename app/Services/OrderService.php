<?php

namespace App\Services;

use App\Mail\OrderConfirmation;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class OrderService
{
    const DELIVERY_FEE = 990;
    const SERVICE_FEE  = 199;

    public function deliveryFee(): int
    {
        return (int) Setting::get('delivery_fee', self::DELIVERY_FEE);
    }

    public function serviceFee(): int
    {
        return (int) Setting::get('service_fee', self::SERVICE_FEE);
    }

    public function place(User $user, array $data): Order
    {
        $products  = Product::whereIn('id', collect($data['items'])->pluck('id'))->get()->keyBy('id');

        $itemsData = collect($data['items'])->map(fn($item) => [
            'product_id' => $item['id'],
            'name'       => $products[$item['id']]->name,
            'price'      => $products[$item['id']]->sale_price ?? $products[$item['id']]->price,
            'quantity'   => $item['quantity'],
        ]);

        $subtotal = $itemsData->sum(fn($i) => $i['price'] * $i['quantity']);

        $coupon         = null;
        $discountAmount = 0;

        if (!empty($data['coupon_code'])) {
            $coupon = Coupon::where('code', strtoupper($data['coupon_code']))->first();
            if ($coupon && $coupon->isValidForUser($user->id)) {
                $discountAmount = $coupon->calculateDiscount($subtotal);
            } else {
                $coupon = null;
            }
        }

        $deliveryFee = $this->deliveryFee();
        $serviceFee  = $this->serviceFee();
        $total       = max(0, $subtotal + $deliveryFee + $serviceFee - $discountAmount);

        $order = DB::transaction(function () use ($user, $data, $itemsData, $subtotal, $total, $coupon, $discountAmount, $deliveryFee, $serviceFee) {
            $order = Order::create([
                'user_id'          => $user->id,
                'coupon_id'        => $coupon?->id,
                'payment_method'   => $data['payment_method'],
                'zip'              => $data['zip'],
                'city'             => $data['city'],
                'street'           => $data['street'],
                'note'             => $data['note'] ?? null,
                'delivery_message' => $data['delivery_message'] ?? null,
                'subtotal'         => $subtotal,
                'delivery_fee'     => $deliveryFee,
                'service_fee'      => $serviceFee,
                'discount_amount'  => $discountAmount,
                'total'            => $total,
            ]);

            $order->items()->createMany($itemsData->toArray());

            if ($coupon) {
                CouponUsage::create([
                    'coupon_id' => $coupon->id,
                    'user_id'   => $user->id,
                    'order_id'  => $order->id,
                ]);
            }

            if (!empty($data['save_address'])) {
                $user->update([
                    'zip'    => $data['zip'],
                    'city'   => $data['city'],
                    'street' => $data['street'],
                    'note'   => $data['note'] ?? null,
                ]);
            }

            return $order;
        });

        Mail::to($user->email)->send(new OrderConfirmation($user, $order));

        return $order;
    }

    public function forUser(User $user): \Illuminate\Support\Collection
    {
        return Order::where('user_id', $user->id)
            ->with('items')
            ->latest()
            ->get()
            ->map(fn($order) => [
                'id'             => $order->id,
                'status'         => $order->status,
                'payment_method' => $order->payment_method,
                'address'        => trim("{$order->zip} {$order->city}, {$order->street}" . ($order->note ? ", {$order->note}" : '')),
                'subtotal'        => $order->subtotal,
                'delivery_fee'    => $order->delivery_fee,
                'service_fee'     => $order->service_fee,
                'discount_amount' => $order->discount_amount,
                'total'           => $order->total,
                'created_at'     => $order->created_at->format('Y. m. d. H:i'),
                'items'          => $order->items->map(fn($item) => [
                    'name'     => $item->name,
                    'price'    => $item->price,
                    'quantity' => $item->quantity,
                ]),
            ]);
    }
}
