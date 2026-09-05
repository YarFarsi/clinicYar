<?php

namespace App\Models;

use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'mobile', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function clinics(): BelongsToMany
    {
        return $this->belongsToMany(Clinic::class)->withPivot('role')->withTimestamps();
    }

    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    public function roleIn(?Clinic $clinic): ?UserRole
    {
        if (! $clinic) {
            return null;
        }

        $pivot = $this->clinics()->where('clinics.id', $clinic->id)->first()?->pivot;

        return $pivot ? UserRole::from($pivot->role) : null;
    }

    public function canPermission(string $permission, ?Clinic $clinic = null): bool
    {
        $clinic ??= \App\Support\Tenant::clinic();
        $role = $this->roleIn($clinic);

        return $role !== null && in_array($permission, $role->permissions(), true);
    }
}
