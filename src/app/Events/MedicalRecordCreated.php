<?php

namespace App\Events;

use App\Models\MedicalRecord;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicalRecordCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(public MedicalRecord $record) {}
}
