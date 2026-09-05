<?php

use App\Http\Controllers\Api\ApiController;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Expense;
use App\Models\Patient;
use App\Models\PatientFollowUp;
use App\Models\Payment;
use App\Models\Service;
use App\Services\ReportService;
use Illuminate\Support\Facades\Route;

Route::post('/auth', [ApiController::class, 'login'])->middleware('throttle:10,1');

Route::middleware(['web', 'auth', 'tenant'])->group(function () {
    Route::get('/dashboard', [ApiController::class, 'dashboard']);
    Route::get('/patients', fn () => response()->json(['success' => true, 'data' => Patient::query()->limit(50)->get(), 'message' => null, 'errors' => []]));
    Route::get('/appointments', fn () => response()->json(['success' => true, 'data' => Appointment::query()->with(['patient', 'doctor'])->limit(50)->get(), 'message' => null, 'errors' => []]));
    Route::get('/doctors', fn () => response()->json(['success' => true, 'data' => Doctor::query()->get(), 'message' => null, 'errors' => []]));
    Route::get('/services', fn () => response()->json(['success' => true, 'data' => Service::query()->get(), 'message' => null, 'errors' => []]));
    Route::get('/payments', fn () => response()->json(['success' => true, 'data' => Payment::query()->limit(50)->get(), 'message' => null, 'errors' => []]));
    Route::get('/expenses', fn () => response()->json(['success' => true, 'data' => Expense::query()->limit(50)->get(), 'message' => null, 'errors' => []]));
    Route::get('/follow-ups', fn () => response()->json(['success' => true, 'data' => PatientFollowUp::query()->limit(50)->get(), 'message' => null, 'errors' => []]));
    Route::get('/reports', fn (ReportService $r) => response()->json(['success' => true, 'data' => $r->dashboard(), 'message' => null, 'errors' => []]));
});
