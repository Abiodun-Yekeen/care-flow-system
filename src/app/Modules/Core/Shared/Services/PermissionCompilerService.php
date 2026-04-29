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
        $resources = Resource::all();
        $result = [];

        foreach ($resources as $resource) {
            // WE MUST PASS CONTEXT HERE
            $actions = $this->iam->getAllowedActions(
                $user,
                $resource->key,
                [
                    'user:department_id' => $user->department_id,
                    'user:id'            => $user->id,
                    'user:role'          => $user->role_name, // Optional but helpful
                ]
            );

            if (!empty($actions)) {
                $result[$resource->key] = array_values($actions);
            }
        }

        return $result;
    }
}
