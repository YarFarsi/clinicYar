<?php

namespace App\Services;

use App\Events\PaymentCreated;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PaymentService
{
    public function create(array $data, ?User $actor = null): Payment
    {
        return DB::transaction(function () use ($data, $actor) {
            $payment = Payment::query()->create([
                ...$data,
                'paid_at' => $data['paid_at'] ?? now(),
                'created_by' => $actor?->id,
            ]);

            PaymentCreated::dispatch($payment);

            return $payment;
        });
    }

    public function patientBalance(Patient $patient): array
    {
        $charges = (float) Appointment::query()
            ->with('service')
            ->where('patient_id', $patient->id)
            ->whereNotIn('status', ['cancelled'])
            ->get()
            ->sum(function (Appointment $appointment) {
                return (float) ($appointment->service?->price ?? 0);
            });

        $paid = (float) Payment::query()->where('patient_id', $patient->id)->sum('amount');

        return [
            'charges' => $charges,
            'paid' => $paid,
            'balance' => round($charges - $paid, 2),
        ];
    }

    public function appointmentTotals(Appointment $appointment): array
    {
        $appointment->loadMissing('service', 'payments');
        $charges = (float) ($appointment->service?->price ?? 0);
        $paid = (float) $appointment->payments->sum('amount');

        return [
            'charges' => $charges,
            'paid' => $paid,
            'remaining' => round($charges - $paid, 2),
        ];
    }
}
