<?php

namespace Database\Seeders;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Enums\PaymentMethod;
use App\Enums\UserRole;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\Doctor;
use App\Models\DoctorWorkingHour;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Models\PatientFollowUp;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DemoClinicSeeder extends Seeder
{
    public function run(): void
    {
        $owner = User::query()->firstOrCreate(
            ['mobile' => '09120000000'],
            ['name' => 'مدیر نمونه', 'password' => Hash::make('password')]
        );

        $clinic = Clinic::query()->firstOrCreate(
            ['slug' => 'demo-clinic'],
            [
                'name' => 'کلینیک نمونه',
                'phone' => '02188000000',
                'city' => 'تهران',
                'address' => 'خیابان ولیعصر',
                'timezone' => 'Asia/Tehran',
                'currency' => 'IRT',
                'type' => 'general',
                'operation_mode' => 'local_only',
            ]
        );

        if (! $clinic->users()->where('users.id', $owner->id)->exists()) {
            $clinic->users()->attach($owner->id, ['role' => UserRole::Owner->value]);
        }

        Tenant::set($clinic);

        $categories = [
            'salary' => 'حقوق', 'rent' => 'اجاره', 'consumables' => 'مواد مصرفی',
            'equipment' => 'تجهیزات', 'ads' => 'تبلیغات', 'utilities' => 'قبوض', 'other' => 'سایر',
        ];
        foreach ($categories as $slug => $name) {
            ExpenseCategory::query()->firstOrCreate(['clinic_id' => $clinic->id, 'slug' => $slug], ['name' => $name]);
        }

        $doctorDefs = [
            ['سارا', 'احمدی', 'داخلی'],
            ['علی', 'رضایی', 'قلب'],
            ['مریم', 'محمدی', 'پوست'],
            ['حسین', 'کاظمی', 'ارتوپدی'],
            ['نگار', 'موسوی', 'روانشناسی'],
        ];
        $doctors = collect();
        foreach ($doctorDefs as $i => [$fn, $ln, $sp]) {
            $doctors->push(Doctor::query()->firstOrCreate(
                ['clinic_id' => $clinic->id, 'first_name' => $fn, 'last_name' => $ln],
                ['specialty' => $sp, 'active' => true]
            ));
        }

        foreach ($doctors as $doctor) {
            foreach ([6, 0, 1, 2, 3] as $day) {
                DoctorWorkingHour::query()->firstOrCreate([
                    'clinic_id' => $clinic->id,
                    'doctor_id' => $doctor->id,
                    'day_of_week' => $day,
                    'start_time' => '09:00:00',
                    'end_time' => '13:00:00',
                ], ['slot_duration' => 30, 'active' => true]);
            }
        }

        $serviceDefs = [
            ['ویزیت عمومی', 'ویزیت', 30, 500000],
            ['ویزیت تخصصی', 'ویزیت', 45, 800000],
            ['مشاوره', 'مشاوره', 30, 400000],
            ['پیگیری', 'پیگیری', 20, 250000],
            ['خدمات تخصصی', 'تخصصی', 60, 1500000],
        ];
        for ($i = 0; $i < 20; $i++) {
            $base = $serviceDefs[$i % count($serviceDefs)];
            Service::query()->firstOrCreate(
                ['clinic_id' => $clinic->id, 'name' => $base[0].($i >= 5 ? ' '.($i + 1) : '')],
                ['category' => $base[1], 'duration_minutes' => $base[2], 'price' => $base[3], 'active' => true]
            );
        }
        $services = Service::query()->where('clinic_id', $clinic->id)->get();

        $firstNames = ['مریم', 'سارا', 'نگار', 'محمد', 'علی', 'زهرا', 'حسین', 'فاطمه', 'رضا', 'مینا'];
        $lastNames = ['حسینی', 'رضایی', 'احمدی', 'کریمی', 'محمدی', 'موسوی', 'کاظمی', 'نوری', 'جعفری', 'اکبری'];

        if (Patient::query()->where('clinic_id', $clinic->id)->count() < 200) {
            for ($i = 0; $i < 200; $i++) {
                Patient::query()->create([
                    'clinic_id' => $clinic->id,
                    'first_name' => $firstNames[$i % 10],
                    'last_name' => $lastNames[intdiv($i, 10) % 10],
                    'mobile' => '0912'.str_pad((string) (1000000 + $i), 7, '0', STR_PAD_LEFT),
                    'gender' => $i % 2 ? 'female' : 'male',
                ]);
            }
        }
        $patients = Patient::query()->where('clinic_id', $clinic->id)->get();

        if (Appointment::query()->where('clinic_id', $clinic->id)->count() < 500) {
            $statuses = AppointmentStatus::cases();
            for ($i = 0; $i < 500; $i++) {
                $doctor = $doctors[$i % $doctors->count()];
                $dayOffset = ($i % 40) - 10;
                $slot = 9 + ($i % 8);
                $minute = ($i % 2) * 30;
                $date = now()->addDays($dayOffset)->toDateString();
                $start = sprintf('%02d:%02d:00', $slot, $minute);
                $end = sprintf('%02d:%02d:00', $minute === 30 ? $slot + 1 : $slot, $minute === 30 ? 0 : 30);
                $exists = Appointment::query()
                    ->where('clinic_id', $clinic->id)
                    ->where('doctor_id', $doctor->id)
                    ->whereDate('appointment_date', $date)
                    ->where('start_time', $start)
                    ->exists();
                if ($exists) {
                    continue;
                }
                $status = $statuses[$i % count($statuses)];
                $service = $services[$i % $services->count()];
                $patient = $patients[$i % $patients->count()];
                $appointment = Appointment::query()->create([
                    'clinic_id' => $clinic->id,
                    'doctor_id' => $doctor->id,
                    'patient_id' => $patient->id,
                    'service_id' => $service->id,
                    'appointment_date' => $date,
                    'start_time' => $start,
                    'end_time' => $end,
                    'status' => $status,
                    'source' => AppointmentSource::Manual,
                    'created_by' => $owner->id,
                ]);
                if ($i % 5 === 0) {
                    Payment::query()->create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'appointment_id' => $appointment->id,
                        'amount' => $service->price,
                        'payment_method' => PaymentMethod::Cash,
                        'paid_at' => now()->subDays(rand(0, 20)),
                        'created_by' => $owner->id,
                    ]);
                }
                if ($i % 12 === 0) {
                    MedicalRecord::query()->create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'doctor_id' => $doctor->id,
                        'appointment_id' => $appointment->id,
                        'chief_complaint' => 'سردرد و خستگی',
                        'diagnosis' => 'ثبت‌شده توسط پزشک (غیرخودکار)',
                        'notes' => 'نمونه پرونده',
                    ]);
                    PatientFollowUp::query()->create([
                        'clinic_id' => $clinic->id,
                        'patient_id' => $patient->id,
                        'doctor_id' => $doctor->id,
                        'title' => 'تماس برای پیگیری وضعیت بیمار',
                        'due_at' => now()->addDays(3),
                        'status' => 'pending',
                        'created_by' => $owner->id,
                    ]);
                }
            }
        }

        $rent = ExpenseCategory::query()->where('clinic_id', $clinic->id)->where('slug', 'rent')->first();
        if ($rent && Expense::query()->where('clinic_id', $clinic->id)->count() < 50) {
            for ($i = 0; $i < 50; $i++) {
                Expense::query()->create([
                    'clinic_id' => $clinic->id,
                    'category_id' => $rent->id,
                    'amount' => 1000000 + ($i * 50000),
                    'description' => 'هزینه نمونه',
                    'expense_date' => now()->subDays($i)->toDateString(),
                    'payment_method' => PaymentMethod::Transfer,
                    'created_by' => $owner->id,
                ]);
            }
        }
    }
}
