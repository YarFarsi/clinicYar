<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['clinic_id', 'user_id', 'type', 'filename', 'mapping', 'status', 'result'])]
class ImportJob extends Model
{
    use BelongsToClinic;

    protected $table = 'imports';

    protected function casts(): array
    {
        return [
            'mapping' => 'array',
            'result' => 'array',
        ];
    }
}
