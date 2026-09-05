<?php

namespace App\Services;

use App\Models\Backup;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;

class BackupService
{
    public function create(?User $actor = null): Backup
    {
        $dir = storage_path('app/private/backups');
        File::ensureDirectoryExists($dir);

        $name = 'backup-'.now()->format('Ymd-His').'.json';
        $path = $dir.DIRECTORY_SEPARATOR.$name;

        $tables = [
            'clinics', 'users', 'clinic_user', 'patients', 'patient_medical_profiles',
            'doctors', 'doctor_working_hours', 'doctor_unavailable_times', 'services',
            'appointments', 'medical_records', 'payments', 'expense_categories', 'expenses',
            'patient_follow_ups', 'notifications', 'sync_queue', 'audit_logs', 'integrations',
        ];

        $dump = ['generated_at' => now()->toIso8601String(), 'tables' => []];
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $dump['tables'][$table] = DB::table($table)->get()->map(fn ($r) => (array) $r)->all();
            }
        }

        $json = json_encode($dump, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        File::put($path, $json);

        return Backup::query()->create([
            'user_id' => $actor?->id,
            'path' => 'private/backups/'.$name,
            'size' => strlen($json),
            'checksum' => hash('sha256', $json),
            'verified_at' => now(),
        ]);
    }
}
