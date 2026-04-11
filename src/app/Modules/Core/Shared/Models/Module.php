<?php

namespace App\Modules\Core\Shared\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    protected $fillable = [
        'key',
        'label',
        'route',
        'icon',
        'order',
        'is_active',
        'parent_id'
    ];

    public function parent()
    {
        return $this->belongsTo(Module::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Module::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }
}
