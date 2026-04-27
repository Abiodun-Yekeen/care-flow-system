<?php

namespace App\Modules\Core\Shared\Services;

use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Shared\Services\Cache\CacheManager;

class PermissionCompilerService
{
    public function __construct(
        protected IamAuthorizationService $iam,
        protected CacheManager $cache
    ) {}

    public function compile(User $user): array
    {
        $key = "iam:snapshot:user:{$user->id}";
        $data = $this->build($user);

        $this->cache->put($key, $data, 86400);
        return $data;
    }

    private function build(User $user): array
    {
        //  Get all resources once and index them by key for fast lookup
        $resources = Resource::all()->pluck('key')->toArray();

        $result = [];

        // Pre-load the user's effective policy statements
        foreach ($resources as $resourceKey) {

            // This is the call that needs to be efficient
            $actions = $this->iam->getAllowedActions(
                $user,
                $resourceKey
            );

            if (!empty($actions)) {
                // Ensure we return a simple array of action strings
                $result[$resourceKey] = array_values($actions);
            }
        }


        return $result;
    }
}
