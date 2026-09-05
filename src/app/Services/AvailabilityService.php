<?php

namespace App\Services;

use App\Enums\AppointmentStatus;
use App\Exceptions\DomainException;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorUnavailableTime;
use App\Models\DoctorWorkingHour;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class AvailabilityService
{
    public function slotsFor(Doctor $doctor, CarbonInterface $date, int $durationMinutes = 30): array
    {
        $hours = DoctorWorkingHour::query()
            ->where('doctor_id', $doctor->id)
            ->where('day_of_week', $date->dayOfWeek)
            ->where('active', true)
            ->get();

        $slots = [];
        foreach ($hours as $hour) {
            $cursor = Carbon::parse($date->toDateString().' '.$hour->start_time);
            $end = Carbon::parse($date->toDateString().' '.$hour->end_time);
            $step = $hour->slot_duration ?: $durationMinutes;

            while ($cursor->copy()->addMinutes($durationMinutes)->lte($end)) {
                $slotEnd = $cursor->copy()->addMinutes($durationMinutes);
                if ($this->isFree($doctor, $date->toDateString(), $cursor->format('H:i:s'), $slotEnd->format('H:i:s'))) {
                    $slots[] = $cursor->format('H:i');
                }
                $cursor->addMinutes($step);
            }
        }

        return $slots;
    }

    public function assertBookable(Doctor $doctor, string $date, string $start, string $end, ?int $ignoreAppointmentId = null): void
    {
        $day = Carbon::parse($date);
        $working = DoctorWorkingHour::query()
            ->where('doctor_id', $doctor->id)
            ->where('day_of_week', $day->dayOfWeek)
            ->where('active', true)
            ->get();

        $inside = $working->contains(function (DoctorWorkingHour $hour) use ($start, $end) {
            return $start >= $hour->start_time && $end <= $hour->end_time;
        });

        if (! $inside) {
            throw DomainException::outsideWorkingHours();
        }

        $startAt = Carbon::parse($date.' '.$start);
        $endAt = Carbon::parse($date.' '.$end);

        $blocked = DoctorUnavailableTime::query()
            ->where('doctor_id', $doctor->id)
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists();

        if ($blocked) {
            throw DomainException::doctorUnavailable();
        }

        if (! $this->isFree($doctor, $date, $start, $end, $ignoreAppointmentId)) {
            throw DomainException::slotTaken();
        }
    }

    public function isFree(Doctor $doctor, string $date, string $start, string $end, ?int $ignoreAppointmentId = null): bool
    {
        $query = Appointment::query()
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->where('start_time', '<', $end)
            ->where('end_time', '>', $start)
            ->whereNotIn('status', [
                AppointmentStatus::Cancelled->value,
                AppointmentStatus::NoShow->value,
            ]);

        if ($ignoreAppointmentId) {
            $query->where('id', '!=', $ignoreAppointmentId);
        }

        return ! $query->exists();
    }
}
