<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Database\Factories\DoctorFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'clinic_id', 'user_id', 'first_name', 'last_name', 'specialty',
    'license_number', 'bio', 'active',
])]
class Doctor extends Model
{
    /** @use HasFactory<DoctorFactory> */
    use BelongsToClinic, HasFactory;

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function fullName(): string
    {
        return 'دکتر '.$this->first_name.' '.$this->last_name;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function workingHours(): HasMany
    {
        return $this->hasMany(DoctorWorkingHour::class);
    }

    public function unavailableTimes(): HasMany
    {
        return $this->hasMany(DoctorUnavailableTime::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
