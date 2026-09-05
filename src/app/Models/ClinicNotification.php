<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['clinic_id', 'user_id', 'type', 'title', 'body', 'read_at'])]
class ClinicNotification extends Model
{
    use BelongsToClinic;

    protected $table = 'notifications';

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }
}
