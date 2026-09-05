<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Service> */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'name' => 'ویزیت',
            'category' => 'ویزیت',
            'duration_minutes' => 30,
            'price' => 500000,
            'active' => true,
        ];
    }
}
