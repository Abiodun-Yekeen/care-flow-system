<?php

namespace App\Modules\Core\Iam\Models;

use App\Modules\Core\Iam\Security\Arn;
use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $table = 'resources';

    protected $fillable = ['key', 'name', 'module_key', 'metadata','parent_id','route','icon','order','is_active'];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function getArnAttribute(): Arn
    {
        return Arn::build(
            $this->module_key,
            "{$this->key}:*"
        );
    }

    public function arnFor(string $resourceId): Arn
    {
        return Arn::build(
            $this->module_key,
            $this->key . ':' . $resourceId
        );
    }

    public function arnForMany(array $resourceIds): array
    {
        return array_map(fn($id) => $this->arnFor($id), $resourceIds);
    }

    public static function findByArn(Arn $arn): ?self
    {
        return self::where('module_key', $arn->getService())
            ->where('key', $arn->getResourceType())
            ->first();
    }

    public function getAvailableActions(): array
    {
        //  fetch the specific actions from the config using the resource key
        $actions = config("iam.actions.{$this->key}");

        // If the config exists, return it exactly as defined (since you already included prefixes)
        if (!empty($actions)) {
            return $actions;
        }

        // FALLBACK: If the key isn't in config, generate standard ones using the key as service
        // This handles modules you haven't explicitly defined yet.
        return [
            "{$this->key}:List",
            "{$this->key}:Get",
            "{$this->key}:Create",
            "{$this->key}:Update",
            "{$this->key}:Delete",
        ];
    }

    public function parent()
    {
        return $this->belongsTo(Resource::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function children()
    {
        return $this->hasMany(Resource::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('order');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
