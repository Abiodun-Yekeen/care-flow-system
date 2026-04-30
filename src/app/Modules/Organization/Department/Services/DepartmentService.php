<?php

namespace App\Modules\Organization\Department\Services;

use App\Modules\Core\Shared\Services\Cache\CacheManager;
use App\Modules\Organization\Department\Models\Department;

class DepartmentService
{


    public function __construct(
        private CacheManager $cacheManager
    )
    {}

    public function getStaff(Department $department)
    {
        return $this->cacheManager->remember("dept_staff_{$department->id}", 600, function() use ($department) {
            return $department->users()
                ->with('roles')
                ->where('is_active', true)
                ->get()
                ->map(function ($user) {
                    $roleName = $user->role_name ?? $user->roles->first()?->name ?? 'Staff';
                    return [
                        'value' => $user->id,
                        'label' => "{$user->name} - {$user->staff_id} ({$roleName})"
                    ];
                });
        });
    }
}
