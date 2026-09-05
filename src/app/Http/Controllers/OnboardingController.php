<?php

namespace App\Http\Controllers;

use App\Models\DoctorWorkingHour;
use App\Services\AppointmentService;
use App\Services\OnboardingService;
use App\Support\Tenant;
use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function wizard()
    {
        $clinic = Tenant::requireClinic();

        return view('onboarding.wizard', [
            'clinic' => $clinic,
            'doctors' => $clinic->doctors()->get(),
            'services' => $clinic->services()->get(),
        ]);
    }

    public function doctor(Request $request, OnboardingService $onboarding)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'specialty' => 'nullable|string',
        ]);
        $onboarding->addDoctor($data);

        return back()->with('ok', 'پزشک افزوده شد.');
    }

    public function service(Request $request, OnboardingService $onboarding)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'duration_minutes' => 'required|integer|min:5',
            'price' => 'required|numeric|min:0',
        ]);
        $onboarding->addService($data + ['active' => true, 'category' => 'ویزیت']);

        return back()->with('ok', 'خدمت افزوده شد.');
    }

    public function hours(Request $request)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_duration' => 'required|integer|min:5',
        ]);
        DoctorWorkingHour::query()->create($data + ['active' => true]);

        return back()->with('ok', 'ساعت کاری ثبت شد.');
    }

    public function appointment(Request $request, AppointmentService $appointments)
    {
        $data = $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'patient_id' => 'required|exists:patients,id',
            'service_id' => 'nullable|exists:services,id',
            'appointment_date' => 'required|date',
            'start_time' => 'required',
        ]);
        $appointments->create($data, $request->user());

        return redirect()->route('dashboard')->with('ok', 'اولین نوبت ثبت شد.');
    }
}
