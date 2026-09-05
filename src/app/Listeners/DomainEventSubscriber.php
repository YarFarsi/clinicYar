<?php

namespace App\Listeners;

use App\Events\AppointmentCancelled;
use App\Events\AppointmentCompleted;
use App\Events\AppointmentConfirmed;
use App\Events\AppointmentCreated;
use App\Events\ExpenseCreated;
use App\Events\MedicalRecordCreated;
use App\Events\PatientCreated;
use App\Events\PaymentCreated;
use App\Models\AuditLog;
use App\Models\ClinicNotification;
use App\Services\SyncService;
use App\Support\Tenant;
use Illuminate\Events\Dispatcher;

class DomainEventSubscriber
{
    public function __construct(private SyncService $sync) {}

    public function subscribe(Dispatcher $events): void
    {
        $events->listen(AppointmentCreated::class, [self::class, 'appointmentCreated']);
        $events->listen(AppointmentConfirmed::class, [self::class, 'appointmentConfirmed']);
        $events->listen(AppointmentCancelled::class, [self::class, 'appointmentCancelled']);
        $events->listen(AppointmentCompleted::class, [self::class, 'appointmentCompleted']);
        $events->listen(PaymentCreated::class, [self::class, 'paymentCreated']);
        $events->listen(MedicalRecordCreated::class, [self::class, 'medicalRecordCreated']);
        $events->listen(PatientCreated::class, [self::class, 'patientCreated']);
        $events->listen(ExpenseCreated::class, [self::class, 'expenseCreated']);
    }

    public function appointmentCreated(AppointmentCreated $event): void
    {
        $this->audit('create_appointment', $event->appointment);
        $this->notify('نوبت جدید', 'نوبت برای بیمار ثبت شد.');
        $this->sync->queue('appointment', $event->appointment->id, 'created');
    }

    public function appointmentConfirmed(AppointmentConfirmed $event): void
    {
        $this->audit('confirm_appointment', $event->appointment);
    }

    public function appointmentCancelled(AppointmentCancelled $event): void
    {
        $this->audit('cancel_appointment', $event->appointment, $event->appointment->toArray(), ['status' => 'cancelled']);
        $this->sync->queue('appointment', $event->appointment->id, 'cancelled');
    }

    public function appointmentCompleted(AppointmentCompleted $event): void
    {
        $this->audit('complete_appointment', $event->appointment);
    }

    public function paymentCreated(PaymentCreated $event): void
    {
        $this->audit('create_payment', $event->payment);
        $this->sync->queue('payment', $event->payment->id, 'created');
    }

    public function medicalRecordCreated(MedicalRecordCreated $event): void
    {
        $this->audit('create_medical_record', $event->record);
    }

    public function patientCreated(PatientCreated $event): void
    {
        $this->audit('create_patient', $event->patient);
        $this->sync->queue('patient', $event->patient->id, 'created');
    }

    public function expenseCreated(ExpenseCreated $event): void
    {
        $this->audit('create_expense', $event->expense);
    }

    private function audit(string $action, object $entity, ?array $old = null, ?array $new = null): void
    {
        AuditLog::query()->create([
            'clinic_id' => Tenant::id() ?? ($entity->clinic_id ?? null),
            'user_id' => auth()->id(),
            'action' => $action,
            'entity_type' => $entity::class,
            'entity_id' => $entity->id ?? null,
            'old_values' => $old,
            'new_values' => $new ?? ['id' => $entity->id ?? null],
            'ip_address' => request()?->ip(),
            'user_agent' => request()?->userAgent(),
            'created_at' => now(),
        ]);
    }

    private function notify(string $title, string $body): void
    {
        if (! Tenant::id() || ! auth()->id()) {
            return;
        }
        ClinicNotification::query()->create([
            'user_id' => auth()->id(),
            'type' => 'local',
            'title' => $title,
            'body' => $body,
        ]);
    }
}
