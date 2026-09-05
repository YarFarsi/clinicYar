<?php

namespace App\Services;

use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Service;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OnboardingService
{
    public function registerOwner(array $userData, array $clinicData): User
    {
        return DB::transaction(function () use ($userData, $clinicData) {
            $user = User::query()->create($userData);
            $clinic = Clinic::query()->create([
                'name' => $clinicData['name'],
                'phone' => $clinicData['phone'] ?? null,
                'address' => $clinicData['address'] ?? null,
                'type' => $clinicData['type'] ?? 'general',
                'timezone' => 'Asia/Tehran',
                'currency' => 'IRT',
                'operation_mode' => 'local_only',
                'slug' => Str::slug($clinicData['name']).'-'.Str::lower(Str::random(6)),
            ]);
            $clinic->users()->attach($user->id, ['role' => UserRole::Owner->value]);
            $this->seedCategories($clinic);
            Tenant::set($clinic);

            return $user;
        });
    }

    public function addDoctor(array $data): Doctor
    {
        return Doctor::query()->create($data);
    }

    public function addService(array $data): Service
    {
        return Service::query()->create($data);
    }

    public function seedCategories(Clinic $clinic): void
    {
        $defaults = [
            'salary' => 'حقوق',
            'rent' => 'اجاره',
            'consumables' => 'مواد مصرفی',
            'equipment' => 'تجهیزات',
            'ads' => 'تبلیغات',
            'utilities' => 'قبوض',
            'other' => 'سایر',
        ];
        foreach ($defaults as $slug => $name) {
            $clinic->id && \App\Models\ExpenseCategory::query()->firstOrCreate(
                ['clinic_id' => $clinic->id, 'slug' => $slug],
                ['name' => $name],
            );
        }
    }
}
