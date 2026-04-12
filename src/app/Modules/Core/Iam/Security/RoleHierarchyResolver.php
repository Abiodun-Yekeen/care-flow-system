<?php

namespace App\Modules\Core\Iam\Security;


use App\Modules\Core\Iam\Models\Role;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class RoleHierarchyResolver
{
    private array $visited = [];
    private array $cycleCache = [];

    public function getAllInheritedRoles(Role $role): Collection
    {
        $this->visited = [];

        try {
            $roleIds = $this->resolveRoleIds($role);
            return Role::whereIn('id', $roleIds)->get();
        } catch (\RuntimeException $e) {
            Log::error('Role hierarchy cycle detected', [
                'role_id' => $role->id,
                'role_name' => $role->name,
                'error' => $e->getMessage()
            ]);

            // Return only direct parents on cycle
            return $role->parents;
        }
    }

    private function resolveRoleIds(Role $role, array $path = []): array
    {
        if (in_array($role->id, $path)) {
            throw new \RuntimeException(
                "Cycle detected in role hierarchy: " . implode(' -> ', array_merge($path, [$role->id]))
            );
        }

        $path[] = $role->id;
        $ids = [$role->id];

        foreach ($role->parents as $parent) {
            $ids = array_merge($ids, $this->resolveRoleIds($parent, $path));
        }

        //array_pop($path);
        return array_unique($ids);
    }

    public function validateHierarchy(?Role $startFrom = null): bool
    {
        $roles = $startFrom ? [$startFrom] : Role::all();

        foreach ($roles as $role) {
            try {
                $this->resolveRoleIds($role);
            } catch (\RuntimeException $e) {
                return false;
            }
        }

        return true;
    }

    public function findCycles(): array
    {
        $cycles = [];
        $roles = Role::all();

        foreach ($roles as $role) {
            $path = [];
            try {
                $this->resolveRoleIds($role, $path);
            } catch (\RuntimeException $e) {
                $cycles[] = [
                    'role' => $role->name,
                    'path' => $path
                ];
            }
        }

        return $cycles;
    }

    public function canAttachParent(Role $child, Role $parent): bool
    {
        // Check immediate cycle
        if ($child->id === $parent->id) {
            return false;
        }

        // Check if parent is already a descendant
        $descendants = $this->getAllInheritedRoles($parent);
        if ($descendants->contains('id', $child->id)) {
            return false;
        }

        return true;
    }
}
