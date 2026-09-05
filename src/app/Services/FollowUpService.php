<?php

namespace App\Services;

use App\Models\PatientFollowUp;
use App\Models\User;

class FollowUpService
{
    public function create(array $data, ?User $actor = null): PatientFollowUp
    {
        return PatientFollowUp::query()->create([
            ...$data,
            'created_by' => $actor?->id,
            'status' => $data['status'] ?? 'pending',
        ]);
    }
}
