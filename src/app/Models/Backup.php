<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['clinic_id', 'user_id', 'path', 'size', 'checksum', 'verified_at'])]
class Backup extends Model
{
    use BelongsToClinic;

    protected function casts(): array
    {
        return [
            'verified_at' => 'datetime',
        ];
    }
}
