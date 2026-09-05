<?php

namespace App\Http\Middleware;

use App\Support\Tenant;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = $request->user();
        if (! $user || ! $user->canPermission($permission, Tenant::clinic())) {
            abort(403, 'دسترسی به این بخش را ندارید.');
        }

        return $next($request);
    }
}
