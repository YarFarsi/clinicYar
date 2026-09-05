<?php

namespace App\Services;

use App\Models\ImportJob;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class ImportService
{
    public function importPatients(array $rows, array $mapping, User $actor): ImportJob
    {
        $imported = 0;
        $errors = [];

        foreach ($rows as $i => $row) {
            $data = [];
            foreach ($mapping as $column => $field) {
                $data[$field] = $row[$column] ?? null;
            }
            $v = Validator::make($data, [
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'mobile' => 'required|string',
            ]);
            if ($v->fails()) {
                $errors[] = ['row' => $i + 1, 'errors' => $v->errors()->all()];
                continue;
            }
            Patient::query()->create($v->validated() + [
                'phone' => $data['phone'] ?? null,
            ]);
            $imported++;
        }

        return ImportJob::query()->create([
            'user_id' => $actor->id,
            'type' => 'patients',
            'status' => 'completed',
            'mapping' => $mapping,
            'result' => ['imported' => $imported, 'errors' => $errors],
        ]);
    }
}
