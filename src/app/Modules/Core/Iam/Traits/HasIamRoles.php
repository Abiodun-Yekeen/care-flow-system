<?php

namespace App\Modules\Core\Iam\Traits;

use App\Modules\Core\Iam\Services\IamAuthorizationService;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Cache;
use Models\Models\Role;

trait HasIamRoles
{
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot('metadata')
            ->withTimestamps();
    }

    protected function getIamService(): IamAuthorizationService
    {
        return app(IamAuthorizationService::class);
    }

    public function assignRole(string|Role $role, array $metadata = []): void
    {
        $roleId = $role instanceof Role ? $role->id : Role::where('name', $role)->firstOrFail()->id;

        $this->roles()->syncWithoutDetaching([
            $roleId => ['metadata' => json_encode($metadata)]
        ]);

        $this->clearAuthorizationCache();
    }

    public function removeRole(string|Role $role): void
    {
        $roleId = $role instanceof Role ? $role->id : Role::where('name', $role)->firstOrFail()->id;

        $this->roles()->detach($roleId);
        $this->clearAuthorizationCache();
    }

    public function syncRoles(array $roles): void
    {
        $roleIds = [];
        foreach ($roles as $role) {
            if ($role instanceof Role) {
                $roleIds[$role->id] = ['metadata' => json_encode($role['metadata'] ?? [])];
            } else {
                $roleModel = Role::where('name', $role['name'])->firstOrFail();
                $roleIds[$roleModel->id] = ['metadata' => json_encode($role['metadata'] ?? [])];
            }
        }

        $this->roles()->sync($roleIds);
        $this->clearAuthorizationCache();
    }

    public function hasRole(string|array $roles): bool
    {
        $roles = (array) $roles;
        return $this->roles()->whereIn('name', $roles)->exists();
    }

    public function hasAnyRole(array $roles): bool
    {
        return $this->hasRole($roles);
    }

    public function hasAllRoles(array $roles): bool
    {
        $userRoles = $this->roles()->pluck('name')->toArray();
        return empty(array_diff($roles, $userRoles));
    }

    public function can($abilities, $arguments = []): bool
    {
        // $abilities is the 'action' (string)
        // $arguments is usually the $resource (string or object)
        return $this->getIamService()->authorize($this, $abilities, $arguments);
    }

    /**
     * Signature must match the framework's expectation for generic access
     */
    public function canAny($abilities, $arguments = []): bool
    {
        $actions = (array) $abilities;
        return $this->getIamService()->authorizeAny($this, $actions, $arguments);
    }

    /**
     * Custom method (not in parent), but kept consistent for your API
     */
    public function canAll($abilities, $arguments = []): bool
    {
        $actions = (array) $abilities;
        return $this->getIamService()->authorizeAll($this, $actions, $arguments);
    }

    public function getAllowedActions($resource, array $context = []): array
    {
        return $this->getIamService()->getAllowedActions($this, $resource, $context);
    }

    public function getAllowedActionsForInstance(object $instance, array $context = []): array
    {
        return $this->getIamService()->getAllowedActionsForInstance($this, $instance, $context);
    }

    protected function clearAuthorizationCache(): void
    {
        $this->getIamService()->clearUserCache($this);
        Cache::forget("user:{$this->id}:policies");
    }
}
