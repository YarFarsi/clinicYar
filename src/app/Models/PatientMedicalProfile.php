<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'clinic_id', 'patient_id', 'allergies', 'medications', 'medical_conditions',
    'past_surgeries', 'family_history', 'notes',
])]
class PatientMedicalProfile extends Model
{
    use BelongsToClinic;

    protected function casts(): array
    {
        return [
            'allergies' => 'encrypted',
            'medications' => 'encrypted',
            'medical_conditions' => 'encrypted',
            'past_surgeries' => 'encrypted',
            'family_history' => 'encrypted',
            'notes' => 'encrypted',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }
}
