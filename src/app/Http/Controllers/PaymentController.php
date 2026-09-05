<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payments.index', [
            'payments' => Payment::query()->with(['patient', 'appointment'])->latest()->paginate(20),
        ]);
    }

    public function store(Request $request, PaymentService $service)
    {
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'appointment_id' => 'nullable|exists:appointments,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'reference' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        $service->create($data, $request->user());

        return back()->with('ok', 'پرداخت ثبت شد.');
    }
}
