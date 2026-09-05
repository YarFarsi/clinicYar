<?php

namespace App\Http\Controllers;

use App\Services\ReportService;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request, ReportService $reports)
    {
        $from = $request->string('from')->toString() ?: now()->startOfMonth()->toDateString();
        $to = $request->string('to')->toString() ?: now()->toDateString();

        return view('reports.index', [
            'from' => $from,
            'to' => $to,
            'doctors' => $reports->doctorPerformance($from, $to),
            'services' => $reports->servicePerformance($from, $to),
            'dashboard' => $reports->dashboard(),
        ]);
    }
}
