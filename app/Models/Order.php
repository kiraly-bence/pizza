<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'coupon_id',
        'status',
        'payment_method',
        'zip',
        'city',
        'street',
        'note',
        'delivery_message',
        'subtotal',
        'delivery_fee',
        'service_fee',
        'discount_amount',
        'total',
    ];

    protected $casts = [
        'status'          => OrderStatus::class,
        'payment_method'  => PaymentMethod::class,
        'subtotal'        => 'integer',
        'delivery_fee'    => 'integer',
        'service_fee'     => 'integer',
        'discount_amount' => 'integer',
        'total'           => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }
}
