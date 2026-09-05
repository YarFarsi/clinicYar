<?php

namespace App\Enums;

enum AppointmentStatus: string
{
    case Scheduled = 'scheduled';
    case Confirmed = 'confirmed';
    case Arrived = 'arrived';
    case InProgress = 'in_progress';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case NoShow = 'no_show';

    public function label(): string
    {
        return match ($this) {
            self::Scheduled => 'زمان‌بندی‌شده',
            self::Confirmed => 'تأیید شده',
            self::Arrived => 'حضور یافته',
            self::InProgress => 'در حال ویزیت',
            self::Completed => 'تکمیل‌شده',
            self::Cancelled => 'لغوشده',
            self::NoShow => 'عدم حضور',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Scheduled => 'slate',
            self::Confirmed => 'sky',
            self::Arrived => 'amber',
            self::InProgress => 'violet',
            self::Completed => 'emerald',
            self::Cancelled => 'rose',
            self::NoShow => 'orange',
        };
    }

    public function occupiesSlot(): bool
    {
        return ! in_array($this, [self::Cancelled, self::NoShow], true);
    }
}
