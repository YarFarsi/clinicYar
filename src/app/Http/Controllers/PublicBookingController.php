<?php

namespace App\Http\Controllers;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Services\AppointmentService;
use App\Services\AvailabilityService;
use App\Services\PatientService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class PublicBookingController extends Controller
{
    public function show(string $slug)
    {
        $clinic = Clinic::query()->where('slug', $slug)->firstOrFail();

        return view('public.book', [
            'clinic' => $clinic,
            'doctors' => $clinic->doctors()->where('active', true)->get(),
            'services' => $clinic->services()->where('active', true)->get(),
        ]);
    }

    public function slots(Request $request, string $slug, AvailabilityService $availability)
    {
        $clinic = Clinic::query()->where('slug', $slug)->firstOrFail();
        \App\Support\Tenant::set($clinic);
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'service_id' => 'nullable|exists:services,id',
        ]);
        $doctor = Doctor::withoutGlobalScopes()->where('clinic_id', $clinic->id)->findOrFail($data['doctor_id']);
        $duration = 30;
        if (! empty($data['service_id'])) {
            $duration = (int) Service::withoutGlobalScopes()->find($data['service_id'])?->duration_minutes ?: 30;
        }

        return response()->json([
            'success' => true,
            'data' => $availability->slotsFor($doctor, \Carbon\Carbon::parse($data['date']), $duration),
            'message' => null,
            'errors' => [],
        ]);
    }

    public function store(Request $request, string $slug, PatientService $patients, AppointmentService $appointments)
    {
        $clinic = Clinic::query()->where('slug', $slug)->firstOrFail();
        \App\Support\Tenant::set($clinic);

        $key = 'book:'.$request->ip();
        if (RateLimiter::tooManyAttempts($key, 8)) {
            return back()->withErrors(['error' => 'تعداد درخواست‌ها زیاد است. کمی بعد تلاش کنید.']);
        }
        RateLimiter::hit($key, 60);

        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'service_id' => 'nullable|exists:services,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'mobile' => 'required|string|min:10|max:15',
        ]);

        $patient = Patient::withoutGlobalScopes()
            ->where('clinic_id', $clinic->id)
            ->where('mobile', $data['mobile'])
            ->first();

        if (! $patient) {
            $patient = $patients->create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'mobile' => $data['mobile'],
            ]);
        }

        $appointments->create([
            'doctor_id' => $data['doctor_id'],
            'patient_id' => $patient->id,
            'service_id' => $data['service_id'] ?? null,
            'appointment_date' => $data['appointment_date'],
            'start_time' => $data['start_time'],
            'status' => AppointmentStatus::Scheduled->value,
            'source' => AppointmentSource::Online->value,
        ]);

        return back()->with('ok', 'درخواست نوبت ثبت شد. پس از تأیید منشی قطعی می‌شود.');
    }
}
