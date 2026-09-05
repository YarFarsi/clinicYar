<?php

namespace App\Models;

use App\Enums\FollowUpStatus;
use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'clinic_id', 'patient_id', 'doctor_id', 'appointment_id', 'title',
    'description', 'due_at', 'status', 'created_by', 'completed_at',
])]
class PatientFollowUp extends Model
{
    use BelongsToClinic;

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => FollowUpStatus::class,
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
