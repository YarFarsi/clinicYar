<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Clinic;
use App\Models\User;
use App\Services\OnboardingService;
use Database\Seeders\DemoClinicSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;

class InstallController extends Controller
{
    public function index()
    {
        if (File::exists(storage_path('app/installed.lock'))) {
            return redirect('/');
        }

        return view('install.index', [
            'php' => PHP_VERSION,
            'extensions' => [
                'pdo' => extension_loaded('pdo'),
                'mbstring' => extension_loaded('mbstring'),
                'openssl' => extension_loaded('openssl'),
                'tokenizer' => extension_loaded('tokenizer'),
            ],
        ]);
    }

    public function store(Request $request, OnboardingService $onboarding)
    {
        if (File::exists(storage_path('app/installed.lock'))) {
            return redirect('/');
        }

        $data = $request->validate([
            'name' => 'required|string',
            'mobile' => 'required|string',
            'password' => 'required|string|min:8',
            'clinic_name' => 'required|string',
            'seed_demo' => 'nullable|boolean',
        ]);

        Artisan::call('migrate', ['--force' => true]);

        if ($request->boolean('seed_demo')) {
            Artisan::call('db:seed', ['--class' => DemoClinicSeeder::class, '--force' => true]);
        } else {
            $user = $onboarding->registerOwner(
                ['name' => $data['name'], 'mobile' => $data['mobile'], 'password' => $data['password']],
                ['name' => $data['clinic_name']],
            );
        }

        File::put(storage_path('app/installed.lock'), now()->toIso8601String());

        return redirect('/login')->with('ok', 'نصب کامل شد.');
    }
}
