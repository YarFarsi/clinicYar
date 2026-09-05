<?php

namespace App\Policies;

use App\Enums\UserRole;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Support\Tenant;

class MedicalRecordPolicy
{
    public function view(User $user, MedicalRecord $record): bool
    {
        if (! $user->canPermission('medical_records.view', Tenant::clinic())) {
            return false;
        }

        $role = $user->roleIn(Tenant::clinic());
        if ($role === UserRole::Doctor) {
            $doctor = $user->doctor;
            return $doctor && (int) $record->doctor_id === (int) $doctor->id;
        }

        return true;
    }

    public function create(User $user): bool
    {
        return $user->canPermission('medical_records.create', Tenant::clinic());
    }

    public function update(User $user, MedicalRecord $record): bool
    {
        return $this->view($user, $record) && $user->canPermission('medical_records.edit', Tenant::clinic());
    }
}
