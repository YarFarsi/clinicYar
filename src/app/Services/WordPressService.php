<?php

namespace App\Services;

use App\Models\Clinic;
use App\Models\Integration;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WordPressService
{
    public function saveCredentials(Clinic $clinic, string $siteUrl, string $username, string $applicationPassword): Integration
    {
        return Integration::query()->updateOrCreate(
            ['clinic_id' => $clinic->id, 'provider' => 'wordpress'],
            [
                'enabled' => true,
                'credentials' => [
                    'site_url' => rtrim($siteUrl, '/'),
                    'username' => $username,
                    'application_password' => $applicationPassword,
                ],
            ],
        );
    }

    public function ping(Integration $integration): bool
    {
        $creds = $integration->credentials ?? [];
        if (empty($creds['site_url'])) {
            return false;
        }

        try {
            $response = Http::timeout(5)->get($creds['site_url']);

            return $response->successful();
        } catch (\Throwable) {
            Log::info('wordpress_ping_skipped');

            return false;
        }
    }
}
