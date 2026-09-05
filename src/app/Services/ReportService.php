<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Expense;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use Carbon\Carbon;

class ReportService
{
    public function dashboard(): array
    {
        $today = Carbon::today()->toDateString();
        $monthStart = Carbon::now()->startOfMonth()->toDateString();

        $todayAppointments = Appointment::query()->whereDate('appointment_date', $today);
        $accounting = app(AccountingService::class);
        $payments = app(PaymentService::class);

        $newPatients = Patient::query()->whereDate('created_at', '>=', $monthStart)->count();
        $returning = Patient::query()
            ->whereHas('appointments', fn ($q) => $q->whereDate('appointment_date', '<', $monthStart))
            ->whereHas('appointments', fn ($q) => $q->whereDate('appointment_date', '>=', $monthStart))
            ->count();
        $inactive = Patient::query()
            ->whereDoesntHave('appointments', fn ($q) => $q->whereDate('appointment_date', '>=', Carbon::now()->subMonths(3)))
            ->count();

        return [
            'today_appointments' => (clone $todayAppointments)->count(),
            'today_patients' => (clone $todayAppointments)->distinct('patient_id')->count('patient_id'),
            'today_revenue' => (float) Payment::query()->whereDate('paid_at', $today)->sum('amount'),
            'month_revenue' => (float) Payment::query()->whereDate('paid_at', '>=', $monthStart)->sum('amount'),
            'month_expenses' => (float) Expense::query()->whereDate('expense_date', '>=', $monthStart)->sum('amount'),
            'cancelled_today' => (clone $todayAppointments)->where('status', AppointmentStatus::Cancelled)->count(),
            'pending_today' => (clone $todayAppointments)->whereIn('status', [
                AppointmentStatus::Scheduled,
                AppointmentStatus::Confirmed,
            ])->count(),
            'doctors_count' => Doctor::query()->where('active', true)->count(),
            'follow_ups_due' => \App\Models\PatientFollowUp::query()
                ->where('status', 'pending')
                ->whereDate('due_at', '<=', $today)
                ->count(),
            'receivables' => $accounting->totalReceivables(),
            'profit_month' => $accounting->profit($monthStart, Carbon::now()->toDateString()),
            'retention' => [
                'new' => $newPatients,
                'returning' => $returning,
                'inactive' => $inactive,
            ],
            'upcoming' => Appointment::query()
                ->with(['patient', 'doctor'])
                ->whereDate('appointment_date', '>=', $today)
                ->whereNotIn('status', [AppointmentStatus::Cancelled->value, AppointmentStatus::Completed->value])
                ->orderBy('appointment_date')
                ->orderBy('start_time')
                ->limit(8)
                ->get(),
            'recent_patients' => Patient::query()->latest()->limit(8)->get(),
            'today_list' => Appointment::query()
                ->with(['patient', 'doctor', 'service'])
                ->whereDate('appointment_date', $today)
                ->orderBy('start_time')
                ->get(),
        ];
    }

    public function doctorPerformance(string $from, string $to): array
    {
        return Doctor::query()->get()->map(function (Doctor $doctor) use ($from, $to) {
            $apps = Appointment::query()
                ->where('doctor_id', $doctor->id)
                ->whereBetween('appointment_date', [$from, $to])
                ->get();

            $completed = $apps->where('status', AppointmentStatus::Completed);
            $revenue = Payment::query()
                ->whereIn('appointment_id', $completed->pluck('id'))
                ->sum('amount');

            return [
                'doctor' => $doctor->fullName(),
                'appointments' => $apps->count(),
                'completed' => $completed->count(),
                'cancelled' => $apps->where('status', AppointmentStatus::Cancelled)->count(),
                'no_show' => $apps->where('status', AppointmentStatus::NoShow)->count(),
                'revenue' => (float) $revenue,
            ];
        })->all();
    }

    public function servicePerformance(string $from, string $to): array
    {
        return Service::query()->get()->map(function (Service $service) use ($from, $to) {
            $apps = Appointment::query()
                ->where('service_id', $service->id)
                ->whereBetween('appointment_date', [$from, $to])
                ->where('status', AppointmentStatus::Completed)
                ->get();
            $count = $apps->count();
            $revenue = $count * (float) $service->price;

            return [
                'service' => $service->name,
                'count' => $count,
                'revenue' => $revenue,
                'average' => $count ? round($revenue / $count, 2) : 0,
            ];
        })->all();
    }
}
