<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfNotInstalled
{
    public function handle(Request $request, Closure $next): Response
    {
        $installed = File::exists(storage_path('app/installed.lock'));
        if (! $installed && ! $request->is('install*') && ! app()->runningUnitTests()) {
            return redirect('/install');
        }

        return $next($request);
    }
}
