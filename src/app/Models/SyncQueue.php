<?php

namespace App\Models;

use App\Enums\SyncStatus;
use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'clinic_id', 'user_id', 'entity_type', 'entity_id', 'action', 'payload',
    'status', 'attempts', 'last_error', 'available_at', 'processed_at',
])]
class SyncQueue extends Model
{
    use BelongsToClinic;

    protected $table = 'sync_queue';

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'status' => SyncStatus::class,
            'available_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }
}
