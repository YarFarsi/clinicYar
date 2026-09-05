<?php

namespace Tests\Feature;

use App\Enums\AppointmentStatus;
use App\Enums\UserRole;
use App\Exceptions\DomainException;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorWorkingHour;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Service;
use App\Models\SyncQueue;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\AppointmentService;
use App\Services\BackupService;
use App\Services\ImportService;
use App\Services\OnboardingService;
use App\Services\PaymentService;
use App\Services\SyncService;
use App\Support\Tenant;
use Tests\TestCase;

class ClinicCrmTest extends TestCase
{
    public function test_user_can_register_and_create_clinic(): void
    {
        $this->get('/register');
        $this->post('/register', [
            'name' => 'مدیر تست',
            'mobile' => '09121111111',
            'password' => 'password8',
            'password_confirmation' => 'password8',
            'clinic_name' => 'مطب تست',
            'type' => 'general',
        ])->assertRedirect();

        $this->assertDatabaseHas('users', ['mobile' => '09121111111']);
        $this->assertDatabaseHas('clinics', ['name' => 'مطب تست']);
    }

    public function test_user_cannot_access_another_clinic(): void
    {
        [$userA, $clinicA] = $this->createUserWithClinic();
        $clinicB = Clinic::factory()->create(['name' => 'کلینیک ب']);
        Tenant::set($clinicB);
        $patientB = Patient::factory()->create(['clinic_id' => $clinicB->id, 'first_name' => 'محرمانه', 'last_name' => 'ب']);

        Tenant::set($clinicA);
        $this->asClinic($userA, $clinicA)->get('/patients/'.$patientB->id)->assertNotFound();
    }

    public function test_receptionist_can_create_appointment(): void
    {
        [$user, $clinic] = $this->createUserWithClinic(UserRole::Receptionist);
        $doctor = Doctor::factory()->create(['clinic_id' => $clinic->id]);
        DoctorWorkingHour::query()->create([
            'clinic_id' => $clinic->id,
            'doctor_id' => $doctor->id,
            'day_of_week' => now()->dayOfWeek,
            'start_time' => '09:00:00',
            'end_time' => '13:00:00',
            'slot_duration' => 30,
            'active' => true,
        ]);
        $patient = Patient::factory()->create(['clinic_id' => $clinic->id]);
        $service = Service::factory()->create(['clinic_id' => $clinic->id]);

        $this->asClinic($user, $clinic)->post('/appointments', [
            'doctor_id' => $doctor->id,
            'patient_id' => $patient->id,
            'service_id' => $service->id,
            'appointment_date' => now()->toDateString(),
            'start_time' => '10:00',
        ])->assertSessionHas('ok');

        $this->assertDatabaseHas('appointments', ['patient_id' => $patient->id]);
    }

    public function test_appointment_collision_is_prevented(): void
    {
        [$user, $clinic] = $this->createUserWithClinic();
        $doctor = Doctor::factory()->create(['clinic_id' => $clinic->id]);
        DoctorWorkingHour::query()->create([
            'clinic_id' => $clinic->id, 'doctor_id' => $doctor->id, 'day_of_week' => now()->dayOfWeek,
            'start_time' => '09:00:00', 'end_time' => '13:00:00', 'slot_duration' => 30, 'active' => true,
        ]);
        $p1 = Patient::factory()->create(['clinic_id' => $clinic->id]);
        $p2 = Patient::factory()->create(['clinic_id' => $clinic->id]);
        $service = app(AppointmentService::class);
        $this->actingAs($user);
        $service->create([
            'doctor_id' => $doctor->id, 'patient_id' => $p1->id,
            'appointment_date' => now()->toDateString(), 'start_time' => '10:00', 'end_time' => '10:30:00',
        ], $user);

        $this->expectException(DomainException::class);
        $service->create([
            'doctor_id' => $doctor->id, 'patient_id' => $p2->id,
            'appointment_date' => now()->toDateString(), 'start_time' => '10:00', 'end_time' => '10:30:00',
        ], $user);
    }

    public function test_cancelled_appointment_releases_slot(): void
    {
        [$user, $clinic] = $this->createUserWithClinic();
        $doctor = Doctor::factory()->create(['clinic_id' => $clinic->id]);
        DoctorWorkingHour::query()->create([
            'clinic_id' => $clinic->id, 'doctor_id' => $doctor->id, 'day_of_week' => now()->dayOfWeek,
            'start_time' => '09:00:00', 'end_time' => '13:00:00', 'slot_duration' => 30, 'active' => true,
        ]);
        $p1 = Patient::factory()->create(['clinic_id' => $clinic->id]);
        $p2 = Patient::factory()->create(['clinic_id' => $clinic->id]);
        $svc = app(AppointmentService::class);
        $this->actingAs($user);
        $a = $svc->create([
            'doctor_id' => $doctor->id, 'patient_id' => $p1->id,
            'appointment_date' => now()->toDateString(), 'start_time' => '10:00', 'end_time' => '10:30:00',
        ], $user);
        $svc->changeStatus($a, AppointmentStatus::Cancelled);
        $second = $svc->create([
            'doctor_id' => $doctor->id, 'patient_id' => $p2->id,
            'appointment_date' => now()->toDateString(), 'start_time' => '10:00', 'end_time' => '10:30:00',
        ], $user);
        $this->assertNotNull($second->id);
    }

    public function test_payment_updates_patient_balance(): void
    {
        [$user, $clinic] = $this->createUserWithClinic();
        $patient = Patient::factory()->create(['clinic_id' => $clinic->id]);
        $doctor = Doctor::factory()->create(['clinic_id' => $clinic->id]);
        $service = Service::factory()->create(['clinic_id' => $clinic->id, 'price' => 1000000]);
        Appointment::factory()->create([
            'clinic_id' => $clinic->id, 'patient_id' => $patient->id, 'doctor_id' => $doctor->id,
            'service_id' => $service->id, 'status' => AppointmentStatus::Completed,
        ]);
        app(PaymentService::class)->create([
            'patient_id' => $patient->id, 'amount' => 400000, 'payment_method' => 'cash',
        ], $user);
        $balance = app(PaymentService::class)->patientBalance($patient);
        $this->assertEquals(600000.0, $balance['balance']);
    }

    public function test_expense_updates_clinic_profit(): void
    {
        [$user, $clinic] = $this->createUserWithClinic();
        $patient = Patient::factory()->create(['clinic_id' => $clinic->id]);
        app(PaymentService::class)->create(['patient_id' => $patient->id, 'amount' => 250000000, 'payment_method' => 'cash'], $user);
        $cat = ExpenseCategory::factory()->create(['clinic_id' => $clinic->id]);
        app(AccountingService::class)->createExpense([
            'category_id' => $cat->id, 'amount' => 90000000, 'expense_date' => now()->toDateString(),
        ], $user);
        $profit = app(AccountingService::class)->profit(now()->toDateString(), now()->toDateString());
        $this->assertEquals(160000000.0, $profit['net']);
    }

    public function test_doctor_cannot_view_another_doctors_record(): void
    {
        [$owner, $clinic] = $this->createUserWithClinic();
        $docUser = User::factory()->create();
        $clinic->users()->attach($docUser->id, ['role' => UserRole::Doctor->value]);
        $doctorA = Doctor::factory()->create(['clinic_id' => $clinic->id, 'user_id' => $docUser->id]);
        $doctorB = Doctor::factory()->create(['clinic_id' => $clinic->id]);
        $patient = Patient::factory()->create(['clinic_id' => $clinic->id]);
        $record = MedicalRecord::query()->create([
            'clinic_id' => $clinic->id, 'patient_id' => $patient->id, 'doctor_id' => $doctorB->id,
            'notes' => 'secret',
        ]);
        $this->actingAs($docUser);
        Tenant::set($clinic);
        $this->assertFalse($docUser->can('view', $record));
        $this->assertTrue($owner->can('view', $record));
    }

    public function test_backup_can_be_created(): void
    {
        [$user, $clinic] = $this->createUserWithClinic();
        $this->actingAs($user);
        $backup = app(BackupService::class)->create($user);
        $this->assertNotEmpty($backup->checksum);
        $this->assertFileExists(storage_path('app/'.$backup->path));
    }

    public function test_csv_import_works(): void
    {
        [$user, $clinic] = $this->createUserWithClinic();
        $this->actingAs($user);
        $job = app(ImportService::class)->importPatients([
            ['نام' => 'آزیتا', 'نام‌خانوادگی' => 'نوری', 'موبایل' => '09123334444'],
        ], ['نام' => 'first_name', 'نام‌خانوادگی' => 'last_name', 'موبایل' => 'mobile'], $user);
        $this->assertSame(1, $job->result['imported']);
        $this->assertDatabaseHas('patients', ['mobile' => '09123334444']);
    }

    public function test_sync_queue_stays_pending_in_local_only(): void
    {
        [$user, $clinic] = $this->createUserWithClinic();
        $queued = app(SyncService::class)->queue('patient', 1, 'created');
        $this->assertNull($queued);
        $clinic->update(['operation_mode' => 'connected']);
        Tenant::set($clinic->fresh());
        $queued = app(SyncService::class)->queue('patient', 1, 'created', [], $user->id);
        $this->assertNotNull($queued);
        $this->assertSame(1, app(SyncService::class)->pendingCount());
        $this->assertGreaterThan(0, app(SyncService::class)->processDue());
    }

    public function test_offline_core_does_not_call_external_api(): void
    {
        [$user, $clinic] = $this->createUserWithClinic();
        $this->assertTrue($clinic->isLocalOnly());
        $this->asClinic($user, $clinic)->get('/dashboard')->assertOk();
        $this->asClinic($user, $clinic)->get('/connectivity')->assertOk()->assertJsonPath('data.local', true);
    }

    public function test_onboarding_service_creates_owner(): void
    {
        $user = app(OnboardingService::class)->registerOwner(
            ['name' => 'مالک', 'mobile' => '09125556666', 'password' => 'password8'],
            ['name' => 'کلینیک مالک']
        );
        $this->assertTrue($user->clinics()->exists());
    }
}
