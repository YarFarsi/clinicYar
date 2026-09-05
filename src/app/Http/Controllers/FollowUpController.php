<?php

namespace App\Http\Controllers;

use App\Models\PatientFollowUp;
use App\Services\FollowUpService;
use Illuminate\Http\Request;

class FollowUpController extends Controller
{
    public function index()
    {
        return view('follow-ups.index', [
            'items' => PatientFollowUp::query()->with(['patient', 'doctor'])->orderBy('due_at')->paginate(20),
        ]);
    }

    public function store(Request $request, FollowUpService $service)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'due_at' => 'required|date',
        ]);
        $service->create($data, $request->user());

        return back()->with('ok', 'پیگیری ثبت شد.');
    }
}
