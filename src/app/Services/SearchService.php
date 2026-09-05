<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;

class SearchService
{
    public function global(string $q): array
    {
        $q = trim($q);
        if (mb_strlen($q) < 2) {
            return ['patients' => [], 'appointments' => [], 'doctors' => [], 'services' => []];
        }

        return [
            'patients' => Patient::query()
                ->where(function ($query) use ($q) {
                    $query->where('first_name', 'like', "%{$q}%")
                        ->orWhere('last_name', 'like', "%{$q}%")
                        ->orWhere('mobile', 'like', "%{$q}%");
                })->limit(10)->get(),
            'doctors' => Doctor::query()
                ->where('first_name', 'like', "%{$q}%")
                ->orWhere('last_name', 'like', "%{$q}%")
                ->orWhere('specialty', 'like', "%{$q}%")
                ->limit(10)->get(),
            'services' => Service::query()->where('name', 'like', "%{$q}%")->limit(10)->get(),
            'appointments' => Appointment::query()->with(['patient', 'doctor'])
                ->whereHas('patient', fn ($p) => $p->where('last_name', 'like', "%{$q}%")->orWhere('first_name', 'like', "%{$q}%"))
                ->limit(10)->get(),
        ];
    }
}
