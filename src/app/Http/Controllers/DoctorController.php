<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\DoctorWorkingHour;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        return view('doctors.index', [
            'doctors' => Doctor::query()->with('workingHours')->orderBy('last_name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'specialty' => 'nullable|string',
            'license_number' => 'nullable|string',
            'bio' => 'nullable|string',
        ]);
        Doctor::query()->create($data + ['active' => true]);

        return back()->with('ok', 'پزشک ثبت شد.');
    }

    public function show(Doctor $doctor)
    {
        $doctor->load(['workingHours', 'appointments' => fn ($q) => $q->latest()->limit(20)]);

        return view('doctors.show', compact('doctor'));
    }

    public function storeHours(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'start_time' => 'required',
            'end_time' => 'required',
            'slot_duration' => 'required|integer|min:5',
        ]);
        DoctorWorkingHour::query()->create($data + ['doctor_id' => $doctor->id, 'active' => true]);

        return back()->with('ok', 'ساعت کاری ذخیره شد.');
    }
}
