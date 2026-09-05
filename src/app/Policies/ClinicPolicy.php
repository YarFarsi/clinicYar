<?php

namespace App\Policies;

use App\Models\Clinic;
use App\Models\User;

class ClinicPolicy
{
    public function view(User $user, Clinic $clinic): bool
    {
        return $user->clinics()->where('clinics.id', $clinic->id)->exists();
    }

    public function manage(User $user, Clinic $clinic): bool
    {
        return $user->canPermission('settings.manage', $clinic);
    }
}
