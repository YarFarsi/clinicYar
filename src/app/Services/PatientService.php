<?php

namespace App\Services;

use App\Events\PatientCreated;
use App\Models\Patient;
use App\Models\User;

class PatientService
{
    public function create(array $data, ?User $actor = null): Patient
    {
        $patient = Patient::query()->create($data);
        PatientCreated::dispatch($patient);

        return $patient;
    }

    public function search(string $q)
    {
        $q = trim($q);
        if ($q === '') {
            return Patient::query()->orderBy('last_name')->limit(20)->get();
        }

        return Patient::query()
            ->where(function ($query) use ($q) {
                $query->where('first_name', 'like', "%{$q}%")
                    ->orWhere('last_name', 'like', "%{$q}%")
                    ->orWhere('mobile', 'like', "%{$q}%")
                    ->orWhere('phone', 'like', "%{$q}%")
                    ->orWhere('national_id', 'like', "%{$q}%");
            })
            ->orderBy('last_name')
            ->limit(50)
            ->get();
    }
}
