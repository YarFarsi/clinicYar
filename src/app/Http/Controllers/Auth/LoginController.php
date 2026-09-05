<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'mobile' => 'required|string',
            'password' => 'required|string',
        ]);

        if (! Auth::attempt(['mobile' => $data['mobile'], 'password' => $data['password']], $request->boolean('remember'))) {
            return back()->withErrors(['mobile' => 'شماره موبایل یا رمز عبور نادرست است.'])->onlyInput('mobile');
        }

        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        \App\Support\Tenant::forget();

        return redirect('/');
    }
}
