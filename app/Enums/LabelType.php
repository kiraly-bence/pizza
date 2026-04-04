<?php

namespace App\Enums;

enum LabelType: string
{
    case Primary   = 'primary';
    case Secondary = 'secondary';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
