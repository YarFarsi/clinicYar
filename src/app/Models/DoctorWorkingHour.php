<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['clinic_id', 'doctor_id', 'day_of_week', 'start_time', 'end_time', 'slot_duration', 'active'])]
class DoctorWorkingHour extends Model
{
    use BelongsToClinic;

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'day_of_week' => 'integer',
            'slot_duration' => 'integer',
        ];
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
