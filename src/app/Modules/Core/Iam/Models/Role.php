<?php

namespace App\Modules\Core\Iam\Models;

use App\Modules\Core\Iam\Security\RoleHierarchyResolver;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;


class Role extends Model
{
    protected $fillable = ['name', 'description', 'is_system', 'metadata'];

    protected $casts = [
        'is_system' => 'boolean',
        'metadata' => 'array',
    ];

    private ?RoleHierarchyResolver $hierarchyResolver = null;

    protected static function booted()
    {
        static::saved(function ($role) {
            cache()->forget("role:{$role->id}:inherited");
        });

        static::deleted(function ($role) {
            cache()->forget("role:{$role->id}:inherited");
        });
    }

    public function policies(): BelongsToMany
    {
        return $this->belongsToMany(Policy::class, 'role_policy')
            ->withTimestamps();
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_roles')
            ->withPivot('metadata')
            ->withTimestamps();
    }

    public function parents()
    {
        return $this->belongsToMany(
            Role::class,
            'role_inherits',
            'child_role_id',
            'parent_role_id'
        );
    }

    public function children()
    {
        return $this->belongsToMany(
            Role::class,
            'role_inherits',
            'parent_role_id',
            'child_role_id'
        );
    }

    private function getHierarchyResolver(): RoleHierarchyResolver
    {
        if (!$this->hierarchyResolver) {
            $this->hierarchyResolver = app(RoleHierarchyResolver::class);
        }
        return $this->hierarchyResolver;
    }

    public function getAllInheritedRoles(): array
    {
        return cache()->remember(
            "role:{$this->id}:inherited",
            3600,
            fn() => $this->getHierarchyResolver()
                ->getAllInheritedRoles($this)
                ->pluck('id')
                ->toArray()
        );
    }

    public function attachParent(Role $parent): void
    {
        if (!$this->getHierarchyResolver()->canAttachParent($this, $parent)) {
            throw new \RuntimeException(
                "Cannot attach parent '{$parent->name}' to '{$this->name}' - would create cycle"
            );
        }

        $this->parents()->attach($parent);
        cache()->forget("role:{$this->id}:inherited");
    }

    public function detachParent(Role $parent): void
    {
        $this->parents()->detach($parent);
        cache()->forget("role:{$this->id}:inherited");
    }

    public function hasParent(Role $parent): bool
    {
        return $this->parents()->where('parent_role_id', $parent->id)->exists();
    }

    public function isDescendantOf(Role $role): bool
    {
        return in_array($role->id, $this->getAllInheritedRoles());
    }
}
