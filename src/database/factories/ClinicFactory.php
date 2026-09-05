<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Clinic> */
class ClinicFactory extends Factory
{
    public function definition(): array
    {
        $name = 'کلینیک '.fake()->unique()->word();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::lower(Str::random(4)),
            'phone' => '021'.fake()->numerify('########'),
            'timezone' => 'Asia/Tehran',
            'currency' => 'IRT',
            'type' => 'general',
            'operation_mode' => 'local_only',
        ];
    }
}
