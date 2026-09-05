<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function __invoke(Request $request)
    {
        $view = $request->string('view')->toString() ?: 'day';
        $date = $request->date('date') ?: now();
        $doctorId = $request->integer('doctor_id') ?: null;

        $start = match ($view) {
            'week' => $date->copy()->startOfWeek(),
            'month' => $date->copy()->startOfMonth(),
            default => $date->copy(),
        };
        $end = match ($view) {
            'week' => $date->copy()->endOfWeek(),
            'month' => $date->copy()->endOfMonth(),
            default => $date->copy(),
        };

        $appointments = Appointment::query()
            ->with(['patient', 'doctor', 'service', 'payments'])
            ->whereBetween('appointment_date', [$start->toDateString(), $end->toDateString()])
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->orderBy('start_time')
            ->get();

        return view('calendar.index', [
            'view' => $view,
            'date' => $date,
            'appointments' => $appointments,
            'doctors' => Doctor::query()->where('active', true)->get(),
            'doctorId' => $doctorId,
        ]);
    }
}
