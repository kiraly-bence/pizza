<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Pending    = 'pending';
    case Confirmed  = 'confirmed';
    case Preparing  = 'preparing';
    case Delivering = 'delivering';
    case Delivered  = 'delivered';
    case Cancelled  = 'cancelled';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
