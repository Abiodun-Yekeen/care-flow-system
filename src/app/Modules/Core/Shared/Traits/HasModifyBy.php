<?php

namespace App\Modules\Core\Shared\Traits;

trait HasModifyBy
{
    protected static function bootHasModifyBy(): void
    {
        static::creating(function ($model) {
            if (!$model->created_by)
            $model->created_by = auth()->user()?->id;
        });

        static::updating(function ($model) {
            if (!$model->created_by)
            $model->updated_by = auth()->user()?->id;
        });

        // Only works if using SoftDeletes
        static::deleting(function ($model) {
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($model))) {
                $model->deleted_by = auth()->user()?->id;
            }
        });
    }
}
