<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    public function store(Request $request, OnboardingService $onboarding)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'password' => 'required|string|min:8|confirmed',
            'clinic_name' => 'required|string|max:160',
            'clinic_phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'type' => 'required|string',
        ]);

        $user = $onboarding->registerOwner(
            ['name' => $data['name'], 'mobile' => $data['mobile'], 'password' => $data['password']],
            ['name' => $data['clinic_name'], 'phone' => $data['clinic_phone'] ?? null, 'address' => $data['address'] ?? null, 'type' => $data['type']],
        );

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('onboarding.wizard');
    }
}
