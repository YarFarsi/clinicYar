<?php

namespace App\Providers;

use App\Ai\AiProviderInterface;
use App\Ai\MockAiProvider;
use App\Listeners\DomainEventSubscriber;
use App\Models\Appointment;
use App\Models\Clinic;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Policies\AppointmentPolicy;
use App\Policies\ClinicPolicy;
use App\Policies\MedicalRecordPolicy;
use App\Policies\PatientPolicy;
use App\Services\SyncService;
use App\Support\Tenant;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(AiProviderInterface::class, MockAiProvider::class);
    }

    public function boot(): void
    {
        Event::subscribe(DomainEventSubscriber::class);

        Gate::policy(Clinic::class, ClinicPolicy::class);
        Gate::policy(Patient::class, PatientPolicy::class);
        Gate::policy(Appointment::class, AppointmentPolicy::class);
        Gate::policy(MedicalRecord::class, MedicalRecordPolicy::class);

        Gate::define('permission', function ($user, string $permission) {
            return $user->canPermission($permission);
        });

        View::composer('layouts.app', function ($view): void {
            $view->with('currentClinic', Tenant::clinic());
            $view->with('pendingSync', Tenant::clinic() ? app(SyncService::class)->pendingCount() : 0);
        });
    }
}
