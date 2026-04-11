<?php

namespace App\Modules\Core\Shared\Models;

use App\Modules\Core\Iam\Models\User;
use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model
{

    protected static function booted()
    {

        static::creating(function ($model) {
            if (empty($model->created_by)) {
                $model->created_by = auth()->id();
            }
        });
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
