<?php

namespace App\Enums;

enum UserRole: string
{
    case Owner = 'owner';
    case Admin = 'admin';
    case Doctor = 'doctor';
    case Receptionist = 'receptionist';
    case Accountant = 'accountant';

    public function label(): string
    {
        return match ($this) {
            self::Owner => 'مالک',
            self::Admin => 'مدیر',
            self::Doctor => 'پزشک',
            self::Receptionist => 'منشی',
            self::Accountant => 'حسابدار',
        };
    }

    /** @return list<string> */
    public function permissions(): array
    {
        $all = PermissionName::all();

        return match ($this) {
            self::Owner, self::Admin => $all,
            self::Doctor => [
                'patients.view', 'patients.create', 'patients.edit',
                'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
                'doctors.view',
                'medical_records.view', 'medical_records.create', 'medical_records.edit',
                'payments.view',
                'reports.view',
            ],
            self::Receptionist => [
                'patients.view', 'patients.create', 'patients.edit',
                'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
                'doctors.view',
                'payments.view', 'payments.create', 'payments.edit',
            ],
            self::Accountant => [
                'patients.view',
                'appointments.view',
                'doctors.view',
                'payments.view', 'payments.create', 'payments.edit',
                'expenses.view', 'expenses.create', 'expenses.edit',
                'reports.view',
            ],
        };
    }
}
