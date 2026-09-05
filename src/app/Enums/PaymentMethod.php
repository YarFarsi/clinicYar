<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case Transfer = 'transfer';
    case Online = 'online';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'نقد',
            self::Card => 'کارت',
            self::Transfer => 'حواله',
            self::Online => 'آنلاین',
            self::Other => 'سایر',
        };
    }
}
