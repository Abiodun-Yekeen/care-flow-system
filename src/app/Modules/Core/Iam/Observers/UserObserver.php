<?php

namespace App\Modules\Core\Iam\Observers;



use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Shared\Services\Cache\CacheManager;

class UserObserver
{
    public function __construct(
        private CacheManager $cacheManager,
    )
    {
    }
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        // Find the 'staff' or 'basic' role
        $role = Role::whereName('user')->first();

        if ($role) {
            $user->roles()->attach($role->id);

            // Clear IAM cache so the new permissions are active immediately
            $user->flushIamCache();
        }
    }
    public function saved(User $user)
    {
        // This fires for both created and updated
        app(IamAuthorizationService::class)->clearUserCache($user);
    }



}
