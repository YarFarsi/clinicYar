<?php

namespace App\Services;

use App\Enums\AppointmentSource;
use App\Enums\AppointmentStatus;
use App\Events\AppointmentCancelled;
use App\Events\AppointmentCompleted;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentCreated;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AppointmentService
{
    public function __construct(private AvailabilityService $availability) {}

    public function create(array $data, ?User $actor = null): Appointment
    {
        return DB::transaction(function () use ($data, $actor) {
            $doctor = Doctor::query()->lockForUpdate()->findOrFail($data['doctor_id']);
            $service = isset($data['service_id']) ? Service::query()->find($data['service_id']) : null;

            $start = $data['start_time'];
            $duration = (int) ($service?->duration_minutes ?? 30);
            $end = $data['end_time'] ?? \Carbon\Carbon::parse($data['appointment_date'].' '.$start)
                ->addMinutes($duration)
                ->format('H:i:s');

            Appointment::query()
                ->where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $data['appointment_date'])
                ->lockForUpdate()
                ->get();

            $this->availability->assertBookable($doctor, $data['appointment_date'], $this->normalizeTime($start), $this->normalizeTime($end));

            $appointment = Appointment::query()->create([
                'doctor_id' => $doctor->id,
                'patient_id' => $data['patient_id'],
                'service_id' => $service?->id,
                'appointment_date' => $data['appointment_date'],
                'start_time' => $this->normalizeTime($start),
                'end_time' => $this->normalizeTime($end),
                'status' => $data['status'] ?? AppointmentStatus::Scheduled->value,
                'source' => $data['source'] ?? AppointmentSource::Manual->value,
                'notes' => $data['notes'] ?? null,
                'created_by' => $actor?->id,
            ]);

            AppointmentCreated::dispatch($appointment);

            return $appointment;
        });
    }

    public function changeStatus(Appointment $appointment, AppointmentStatus $status): Appointment
    {
        return DB::transaction(function () use ($appointment, $status) {
            $appointment->update(['status' => $status]);

            match ($status) {
                AppointmentStatus::Confirmed => AppointmentConfirmed::dispatch($appointment),
                AppointmentStatus::Cancelled => AppointmentCancelled::dispatch($appointment),
                AppointmentStatus::Completed => AppointmentCompleted::dispatch($appointment),
                default => null,
            };

            return $appointment->refresh();
        });
    }

    public function waitingRoom(string $date)
    {
        return Appointment::query()
            ->with(['patient', 'doctor', 'service'])
            ->whereDate('appointment_date', $date)
            ->whereNotIn('status', [AppointmentStatus::Cancelled->value])
            ->orderBy('start_time')
            ->get();
    }

    private function normalizeTime(string $time): string
    {
        if (strlen($time) === 5) {
            return $time.':00';
        }

        return $time;
    }
}
