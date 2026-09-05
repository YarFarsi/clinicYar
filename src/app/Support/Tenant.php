<?php

namespace App\Support;

use App\Models\Clinic;
use App\Models\User;

final class Tenant
{
    private static ?Clinic $clinic = null;

    public static function set(?Clinic $clinic): void
    {
        self::$clinic = $clinic;
    }

    public static function id(): ?int
    {
        return self::$clinic?->id;
    }

    public static function clinic(): ?Clinic
    {
        return self::$clinic;
    }

    public static function requireClinic(): Clinic
    {
        if (! self::$clinic) {
            abort(403, 'کلینیک انتخاب نشده است.');
        }

        return self::$clinic;
    }

    public static function forget(): void
    {
        self::$clinic = null;
    }

    public static function bindFromUser(User $user, ?int $clinicId = null): void
    {
        $clinic = $clinicId
            ? $user->clinics()->where('clinics.id', $clinicId)->first()
            : $user->clinics()->first();

        self::set($clinic);
        if ($clinic) {
            session(['current_clinic_id' => $clinic->id]);
        }
    }
}
