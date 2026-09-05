<?php

namespace App\Enums;

enum ClinicType: string
{
    case General = 'general';
    case Dental = 'dental';
    case Beauty = 'beauty';
    case Physiotherapy = 'physiotherapy';
    case Psychology = 'psychology';
    case Medical = 'medical';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::General => 'عمومی',
            self::Dental => 'دندانپزشکی',
            self::Beauty => 'زیبایی',
            self::Physiotherapy => 'فیزیوتراپی',
            self::Psychology => 'روانشناسی',
            self::Medical => 'پزشکی تخصصی',
            self::Other => 'سایر',
        };
    }
}
