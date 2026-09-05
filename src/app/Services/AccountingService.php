<?php

namespace App\Services;

use App\Events\ExpenseCreated;
use App\Models\Expense;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    public function createExpense(array $data, ?User $actor = null): Expense
    {
        return DB::transaction(function () use ($data, $actor) {
            $expense = Expense::query()->create([
                ...$data,
                'created_by' => $actor?->id,
            ]);
            ExpenseCreated::dispatch($expense);

            return $expense;
        });
    }

    public function profit(?string $from = null, ?string $to = null): array
    {
        $payments = Payment::query()
            ->when($from, fn ($q) => $q->whereDate('paid_at', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('paid_at', '<=', $to))
            ->sum('amount');

        $expenses = Expense::query()
            ->when($from, fn ($q) => $q->whereDate('expense_date', '>=', $from))
            ->when($to, fn ($q) => $q->whereDate('expense_date', '<=', $to))
            ->sum('amount');

        $revenue = (float) $payments;
        $cost = (float) $expenses;

        return [
            'revenue' => $revenue,
            'expenses' => $cost,
            'net' => round($revenue - $cost, 2),
        ];
    }

    public function totalReceivables(): float
    {
        $patients = \App\Models\Patient::query()->get();
        $payments = new PaymentService;

        return round($patients->sum(fn ($p) => max(0, $payments->patientBalance($p)['balance'])), 2);
    }
}
