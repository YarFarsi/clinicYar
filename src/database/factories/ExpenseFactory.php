<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Expense> */
class ExpenseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'clinic_id' => Clinic::factory(),
            'category_id' => ExpenseCategory::factory(),
            'amount' => 1000000,
            'expense_date' => now()->toDateString(),
            'payment_method' => 'cash',
        ];
    }
}
