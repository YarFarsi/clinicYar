<?php

namespace Tests;

use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\User;
use App\Support\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        \Illuminate\Support\Facades\File::ensureDirectoryExists(storage_path('app'));
        \Illuminate\Support\Facades\File::put(storage_path('app/installed.lock'), 'testing');
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            \Illuminate\Foundation\Http\Middleware\PreventRequestForgery::class,
        ]);
    }

    public function createApplication()
    {
        $app = require dirname(__DIR__).'/bootstrap/app.php';
        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function createUserWithClinic(UserRole $role = UserRole::Owner): array
    {
        $user = User::factory()->create();
        $clinic = Clinic::factory()->create();
        $clinic->users()->attach($user->id, ['role' => $role->value]);
        Tenant::set($clinic);
        session(['current_clinic_id' => $clinic->id]);

        return [$user, $clinic];
    }

    protected function asClinic(User $user, Clinic $clinic)
    {
        Tenant::set($clinic);

        return $this->actingAs($user);
    }
}
