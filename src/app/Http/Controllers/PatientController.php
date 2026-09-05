<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\PatientService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index(Request $request, PatientService $patients)
    {
        $q = $request->string('q')->toString();
        $items = $q !== ''
            ? $patients->search($q)
            : Patient::query()->latest()->paginate(20);

        return view('patients.index', ['patients' => $items, 'q' => $q]);
    }

    public function store(Request $request, PatientService $patients)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:80',
            'last_name' => 'required|string|max:80',
            'mobile' => 'required|string|max:20',
            'phone' => 'nullable|string|max:20',
            'national_id' => 'nullable|string|max:20',
            'gender' => 'nullable|string',
            'birth_date' => 'nullable|date',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $patient = $patients->create($data, $request->user());

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $patient,
                'message' => 'بیمار ثبت شد.',
                'errors' => [],
            ]);
        }

        return redirect()->route('patients.show', $patient)->with('ok', 'بیمار ثبت شد.');
    }

    public function show(Patient $patient, PaymentService $payments)
    {
        $this->authorize('view', $patient);
        $patient->load(['appointments.doctor', 'appointments.service', 'payments', 'medicalRecords.doctor', 'followUps', 'medicalProfile']);

        $timeline = collect()
            ->concat($patient->appointments->map(fn ($a) => ['at' => $a->appointment_date, 'type' => 'appointment', 'item' => $a]))
            ->concat($patient->payments->map(fn ($p) => ['at' => $p->paid_at, 'type' => 'payment', 'item' => $p]))
            ->concat($patient->medicalRecords->map(fn ($r) => ['at' => $r->created_at, 'type' => 'record', 'item' => $r]))
            ->concat($patient->followUps->map(fn ($f) => ['at' => $f->due_at, 'type' => 'followup', 'item' => $f]))
            ->sortByDesc('at')
            ->values();

        return view('patients.show', [
            'patient' => $patient,
            'balance' => $payments->patientBalance($patient),
            'timeline' => $timeline,
        ]);
    }
}
