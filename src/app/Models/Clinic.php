<?php

namespace App\Models;

use App\Enums\ClinicType;
use App\Enums\OperationMode;
use Database\Factories\ClinicFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable([
    'name', 'slug', 'phone', 'email', 'address', 'city', 'timezone',
    'currency', 'logo', 'type', 'operation_mode', 'settings',
])]
class Clinic extends Model
{
    /** @use HasFactory<ClinicFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            'type' => ClinicType::class,
            'operation_mode' => OperationMode::class,
            'settings' => 'array',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Clinic $clinic): void {
            if (! $clinic->slug) {
                $clinic->slug = Str::slug($clinic->name).'-'.Str::lower(Str::random(6));
            }
        });
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withPivot('role')->withTimestamps();
    }

    public function patients(): HasMany
    {
        return $this->hasMany(Patient::class);
    }

    public function doctors(): HasMany
    {
        return $this->hasMany(Doctor::class);
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function isLocalOnly(): bool
    {
        return $this->operation_mode === OperationMode::LocalOnly;
    }
}
