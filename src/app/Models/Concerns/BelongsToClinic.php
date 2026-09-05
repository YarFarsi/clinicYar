<?php

namespace App\Models\Concerns;

use App\Models\Clinic;
use App\Support\Tenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToClinic
{
    public static function bootBelongsToClinic(): void
    {
        static::addGlobalScope('clinic', function (Builder $builder): void {
            $id = Tenant::id();
            if ($id) {
                $builder->where($builder->getModel()->getTable().'.clinic_id', $id);

                return;
            }

            if (! app()->runningInConsole()) {
                $builder->whereRaw('1 = 0');
            }
        });

        static::creating(function (Model $model): void {
            if (! $model->clinic_id && Tenant::id()) {
                $model->clinic_id = Tenant::id();
            }
        });
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }
}
