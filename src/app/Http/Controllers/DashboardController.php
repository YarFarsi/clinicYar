<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use App\Services\SyncService;
use App\Support\Tenant;

class DashboardController extends Controller
{
    public function __invoke(ReportService $reports, SyncService $sync)
    {
        return view('dashboard', [
            'stats' => $reports->dashboard(),
            'clinic' => Tenant::requireClinic(),
            'pendingSync' => $sync->pendingCount(),
        ]);
    }
}
