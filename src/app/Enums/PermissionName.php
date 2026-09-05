<?php

namespace App\Enums;

final class PermissionName
{
    /** @return list<string> */
    public static function all(): array
    {
        return [
            'patients.view', 'patients.create', 'patients.edit', 'patients.delete',
            'appointments.view', 'appointments.create', 'appointments.edit', 'appointments.cancel',
            'doctors.view', 'doctors.manage',
            'staff.manage',
            'medical_records.view', 'medical_records.create', 'medical_records.edit',
            'payments.view', 'payments.create', 'payments.edit',
            'expenses.view', 'expenses.create', 'expenses.edit',
            'reports.view',
            'settings.manage',
            'integrations.manage',
            'users.manage',
        ];
    }
}
