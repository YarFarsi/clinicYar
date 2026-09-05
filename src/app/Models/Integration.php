<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['clinic_id', 'provider', 'enabled', 'credentials', 'settings'])]
class Integration extends Model
{
    use BelongsToClinic;

    protected function casts(): array
    {
        return [
            'enabled' => 'boolean',
            'credentials' => 'encrypted:array',
            'settings' => 'array',
        ];
    }

    protected $hidden = ['credentials'];
}
