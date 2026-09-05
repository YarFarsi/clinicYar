<?php

use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\InstallController;
use App\Http\Controllers\MedicalRecordController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PublicBookingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\WaitingRoomController;
use Illuminate\Support\Facades\Route;

Route::get('/install', [InstallController::class, 'index'])->name('install');
Route::post('/install', [InstallController::class, 'store']);

Route::view('/', 'landing')->name('landing');
Route::get('/book/{slug}', [PublicBookingController::class, 'show'])->name('book.show');
Route::get('/book/{slug}/slots', [PublicBookingController::class, 'slots'])->middleware('throttle:30,1');
Route::post('/book/{slug}', [PublicBookingController::class, 'store'])->middleware('throttle:8,1');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:10,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware(['auth', 'tenant'])->group(function () {
    Route::get('/onboarding', [OnboardingController::class, 'wizard'])->name('onboarding.wizard');
    Route::post('/onboarding/doctor', [OnboardingController::class, 'doctor'])->name('onboarding.doctor');
    Route::post('/onboarding/service', [OnboardingController::class, 'service'])->name('onboarding.service');
    Route::post('/onboarding/hours', [OnboardingController::class, 'hours'])->name('onboarding.hours');
    Route::post('/onboarding/appointment', [OnboardingController::class, 'appointment'])->name('onboarding.appointment');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/search', [SettingsController::class, 'search'])->name('search');
    Route::get('/notifications', [SettingsController::class, 'notifications'])->name('notifications.index');
    Route::get('/connectivity', [SettingsController::class, 'connectivity'])->name('connectivity');
    Route::post('/sync', [SettingsController::class, 'sync'])->name('sync.run');

    Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
    Route::get('/appointments/slots', [AppointmentController::class, 'slots'])->name('appointments.slots');
    Route::get('/appointments/form-data', [AppointmentController::class, 'formData'])->name('appointments.form-data');

    Route::get('/calendar', CalendarController::class)->name('calendar');
    Route::get('/waiting-room', WaitingRoomController::class)->name('waiting-room');

    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');

    Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
    Route::post('/doctors', [DoctorController::class, 'store'])->name('doctors.store');
    Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');
    Route::post('/doctors/{doctor}/hours', [DoctorController::class, 'storeHours'])->name('doctors.hours');

    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::post('/services', [ServiceController::class, 'store'])->name('services.store');

    Route::get('/records', [MedicalRecordController::class, 'index'])->name('records.index');
    Route::get('/records/create', [MedicalRecordController::class, 'create'])->name('records.create');
    Route::post('/records', [MedicalRecordController::class, 'store'])->name('records.store');

    Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');

    Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
    Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/follow-ups', [FollowUpController::class, 'index'])->name('follow-ups.index');
    Route::post('/follow-ups', [FollowUpController::class, 'store'])->name('follow-ups.store');

    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingsController::class, 'update'])->name('settings.update');
    Route::get('/settings/backup', [SettingsController::class, 'backup'])->name('settings.backup');
    Route::post('/settings/backup', [SettingsController::class, 'createBackup'])->name('settings.backup.create');
    Route::get('/settings/backup/{backup}/download', [SettingsController::class, 'downloadBackup'])->name('settings.backup.download');
    Route::get('/settings/integrations', [SettingsController::class, 'integrations'])->name('settings.integrations');
    Route::post('/settings/integrations/wordpress', [SettingsController::class, 'saveWordpress'])->name('settings.wordpress');
    Route::get('/settings/users', [SettingsController::class, 'users'])->name('settings.users');
});
