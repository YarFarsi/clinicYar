<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;
use App\Support\Tenant;

class PatientPolicy
{
    public function view(User $user, Patient $patient): bool
    {
        return $user->canPermission('patients.view', Tenant::clinic())
            && (int) $patient->clinic_id === (int) Tenant::id();
    }

    public function create(User $user): bool
    {
        return $user->canPermission('patients.create', Tenant::clinic());
    }

    public function update(User $user, Patient $patient): bool
    {
        return $this->view($user, $patient) && $user->canPermission('patients.edit', Tenant::clinic());
    }
}
