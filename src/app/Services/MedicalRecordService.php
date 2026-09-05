<?php

namespace App\Services;

use App\Events\MedicalRecordCreated;
use App\Exceptions\DomainException;
use App\Models\MedicalRecord;
use App\Models\User;
use App\Support\Tenant;

class MedicalRecordService
{
    public function create(array $data, User $actor): MedicalRecord
    {
        if (! $actor->canPermission('medical_records.create', Tenant::clinic())) {
            throw DomainException::forbiddenRecord();
        }

        $record = MedicalRecord::query()->create($data);
        MedicalRecordCreated::dispatch($record);

        return $record;
    }
}
