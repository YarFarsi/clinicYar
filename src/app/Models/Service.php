<?php

namespace App\Models;

use App\Models\Concerns\BelongsToClinic;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['clinic_id', 'name', 'category', 'duration_minutes', 'price', 'description', 'active'])]
class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use BelongsToClinic, HasFactory;

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'active' => 'boolean',
            'duration_minutes' => 'integer',
        ];
    }
}
