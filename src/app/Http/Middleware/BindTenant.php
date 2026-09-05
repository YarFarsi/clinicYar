<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BindTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        if ($user) {
            $clinicId = (int) ($request->session()->get('current_clinic_id') ?: 0);
            Tenant::bindFromUser($user, $clinicId ?: null);
            if (! Tenant::clinic()) {
                if ($request->routeIs('onboarding.*', 'logout')) {
                    return $next($request);
                }

                return redirect()->route('onboarding.clinic');
            }
        }

        return $next($request);
    }
}
