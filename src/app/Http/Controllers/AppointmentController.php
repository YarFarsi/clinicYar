<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Services\AppointmentService;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->string('date')->toString() ?: now()->toDateString();
        $appointments = Appointment::query()
            ->with(['patient', 'doctor', 'service'])
            ->whereDate('appointment_date', $date)
            ->orderBy('start_time')
            ->get();

        return view('appointments.index', compact('appointments', 'date'));
    }

    public function store(Request $request, AppointmentService $service)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'nullable|exists:services,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'notes' => 'nullable|string',
        ]);
        $service->create($data, $request->user());

        return back()->with('ok', 'نوبت ثبت شد.');
    }

    public function updateStatus(Request $request, Appointment $appointment, AppointmentService $service)
    {
        $this->authorize('update', $appointment);
        $status = AppointmentStatus::from($request->validate(['status' => 'required|string'])['status']);
        $service->changeStatus($appointment, $status);

        return back()->with('ok', 'وضعیت نوبت به‌روز شد.');
    }

    public function slots(Request $request, AvailabilityService $availability)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'service_id' => 'nullable|exists:services,id',
        ]);
        $doctor = Doctor::query()->findOrFail($data['doctor_id']);
        $duration = 30;
        if (! empty($data['service_id'])) {
            $duration = (int) Service::query()->find($data['service_id'])?->duration_minutes ?: 30;
        }

        return response()->json([
            'success' => true,
            'data' => $availability->slotsFor($doctor, \Carbon\Carbon::parse($data['date']), $duration),
            'message' => null,
            'errors' => [],
        ]);
    }

    public function formData()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'doctors' => Doctor::query()->where('active', true)->get(['id', 'first_name', 'last_name']),
                'patients' => Patient::query()->orderBy('last_name')->limit(100)->get(['id', 'first_name', 'last_name', 'mobile']),
                'services' => Service::query()->where('active', true)->get(['id', 'name', 'duration_minutes', 'price']),
            ],
            'message' => null,
            'errors' => [],
        ]);
    }
}
