<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\MedicalRecord;
use App\Services\MedicalRecordService;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    public function index()
    {
        $records = MedicalRecord::query()->with(['patient', 'doctor'])->latest()->paginate(20);

        return view('records.index', compact('records'));
    }

    public function store(Request $request, MedicalRecordService $service)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'chief_complaint' => 'nullable|string',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'treatment' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
        ]);
        $record = $service->create($data, $request->user());

        return redirect()->route('patients.show', $record->patient_id)->with('ok', 'پرونده ثبت شد.');
    }

    public function create(Request $request)
    {
        $appointment = $request->integer('appointment_id')
            ? Appointment::query()->with(['patient', 'doctor'])->findOrFail($request->integer('appointment_id'))
            : null;

        return view('records.create', compact('appointment'));
    }
}
