<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use App\Support\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiController extends Controller
{
    protected function ok(mixed $data = [], ?string $message = null, int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $data,
            'message' => $message,
            'errors' => [],
        ], $code);
    }

    protected function fail(string $message, array $errors = [], int $code = 422): JsonResponse
    {
        return response()->json([
            'success' => false,
            'data' => null,
            'message' => $message,
            'errors' => $errors,
        ], $code);
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'mobile' => 'required',
            'password' => 'required',
        ]);
        if (! Auth::attempt($data)) {
            return $this->fail('ورود ناموفق', [], 401);
        }
        $user = $request->user();
        Tenant::bindFromUser($user);

        return $this->ok(['user' => $user->only('id', 'name', 'mobile'), 'clinic_id' => Tenant::id()]);
    }

    public function dashboard(ReportService $reports): JsonResponse
    {
        $stats = $reports->dashboard();
        unset($stats['upcoming'], $stats['recent_patients'], $stats['today_list']);

        return $this->ok($stats);
    }
}
