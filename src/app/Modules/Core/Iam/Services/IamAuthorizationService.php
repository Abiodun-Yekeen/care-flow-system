<?php

namespace App\Modules\Core\Iam\Services;

use App\Modules\Core\Iam\Models\Policy;
use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Security\Arn;
use App\Modules\Core\Iam\Security\PolicyEvaluator;
use App\Modules\Core\Shared\Models\Module;
use App\Modules\Core\Shared\Services\Cache\CacheManager;
use App\Modules\Core\Shared\Services\PermissionCompilerService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class IamAuthorizationService
{
    private PolicyEvaluator $evaluator;

    public function __construct(PolicyEvaluator $evaluator)
    {
        $this->evaluator = $evaluator;
    }
    public function authorize($user, $action, $resource = null, array $context = []): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        if (is_array($resource)) {
            $context = array_merge($context, $resource[1] ?? []);
            $resource = $resource[0] ?? null;
        }

        // Build full evaluation context ONCE
        $evalContext = array_merge([
            'user'        => $user,
            'user_id'     => $user->id,
            'user.id'     => $user->id,
            'user_email'  => $user->email,
            'user.email'  => $user->email,
        ], $context);

        try {
            // Replace placeholders in REQUEST resource
            $resource = str_replace(
                ['${user.id}', '${user_id}'],
                $user->id,
                (string) $resource
            );

            $arn = $this->resolveArn($resource);

            //Use evalContext for cache key too
            $cacheKey = $this->getCacheKey($user, $action, $arn, $evalContext);

            return Cache::remember($cacheKey, 3600, function () use ($user, $action, $arn, $evalContext) {

                $policies = $this->getUserPolicies($user);

                $result = $this->evaluator->evaluateMultiple(
                    $policies,
                    $action,
                    $arn->toString(),
                    $evalContext
                );

                audit()->log([
                    'category' => 'IAM',
                    'action' => $result ? 'allow' : 'deny',
                    'resource_type' => 'authorization',
                    'metadata' => [
                        'ability' => $action,
                        'resource' => $arn->toString(),
                        'context' => $context,
                    ],
                ]);
                $this->logDecision($result, $user, $action, $arn, $evalContext);

                return $result;
            });

        } catch (\Exception $e) {
            // If resource is missing or ARN fails, deny and log
           Log::warning("IAM Authorization failed for action '{$action}': " . $e->getMessage());
            return false;
        }
    }


    public function authorizeAny($user, array $abilities, $arguments = [], array $context = []): bool
    {
        foreach ($abilities as $ability) {
            if ($this->authorize($user, $ability, $arguments, $context)) {
                return true;
            }
        }
        return false;
    }

    public function authorizeAll($user, array $abilities, $arguments = [], array $context = []): bool
    {
        foreach ($abilities as $ability) {
            if (!$this->authorize($user, $ability, $arguments, $context)) {
                return false;
            }
        }
        return true;
    }
    public function getAllowedActions(User $user, $resource, array $context = []): array
    {
        $arn = $this->resolveArn($resource);
        $resourceKey = $resource instanceof Resource ? $resource->key : (string)$resource;

        $possibleActions = config("iam.actions.{$resourceKey}", ['view', 'create', 'update', 'delete']);
        $policies = $this->getUserPolicies($user);
        \Log::info("IAM Compiler Sync Check for Resource: {$resourceKey}", [
            'arn' => $arn->toString(),
            'policy_count' => $policies->count(),
            'policy_ids' => $policies->pluck('id')->toArray(),
            'user_id' => $user->id
        ]);
        return $this->evaluator->getAllowedActions(
            $policies,
            $possibleActions,
            $arn->toString(),
            $context
        );
    }

    public function getAllowedActionsForInstance(User $user, object $instance, array $context = []): array
    {
        if (!method_exists($instance, 'getResourceKey')) {
            throw new \InvalidArgumentException('Instance must have getResourceKey method');
        }

        $resourceKey = $instance->getResourceKey();
        $resource = Resource::where('key', $resourceKey)->firstOrFail();

        $instanceContext = array_merge($context, [
            'resource:id' => $instance->id,
            'resource:owner' => $instance->user_id ?? null,
            'resource:department' => $instance->department_id ?? null,
            'resource:status' => $instance->status ?? null,
        ]);

        return $this->getAllowedActions($user, $resource->arnFor($instance->id), $instanceContext);
    }

    private function getUserPolicies(User $user): Collection
    {
        return Cache::remember("user:{$user->id}:policies", 3600, function () use ($user) {
            $roleIds = $this->getUserRoleIds($user);

            return Policy::whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('roles.id', $roleIds);
            })->get();
        });
    }

    private function getUserRoleIds(User $user): array
    {
        //  Get roles directly assigned to user
        $directRoles = $user->roles()->get();
        $allRoleIds = [];

        foreach ($directRoles as $role) {
            $allRoleIds[] = $role->id;

            // Merge in inherited IDs
            // Make sure your Role model's getAllInheritedRoles() returns an array of IDs
            $inherited = $role->getAllInheritedRoles();
            $allRoleIds = array_merge($allRoleIds, $inherited);
        }

        return array_unique($allRoleIds);
    }

    private function resolveArn($resource): Arn
    {
        if ($resource instanceof Arn) {
            return $resource;
        }

        if ($resource instanceof Resource) {
            return $resource->arn;
        }

        // Use first() instead of firstOrFail() to prevent ModelNotFoundException in tests
        if (is_string($resource)) {
            // Check if it's already an ARN string
            try {
                $arn = Arn::fromString($resource);
                if ($arn) return $arn;
            } catch (\Exception $e) {}

            // Look up by 'key' or 'name' (Check your migration column name!)
            $resourceModel = Resource::where('key', $resource)
                ->orWhere('name', $resource)
                ->first();

            if (!$resourceModel) {
                throw new \InvalidArgumentException("Resource definition for '{$resource}' not found in database.");
            }

            return $resourceModel->arn;
        }

        throw new \InvalidArgumentException('Unable to resolve ARN from resource');
    }

    private function getCacheKey(User $user, string $action, Arn $arn, array $context): string
    {
        $contextHash = empty($context) ? '' : ':' . md5(json_encode($context));
        return "iam:decision:{$user->id}:{$action}:{$arn}{$contextHash}";
    }

    private function logDecision(bool $allowed, User $user, string $action, Arn $arn, array $context): void
    {
        if (!config('iam.audit.enabled', true)) {
            return;
        }

        $shouldLog = !$allowed
            ? config('iam.audit.log_denials', true)
            : config('iam.audit.log_allows', false);

        if (!$shouldLog) {
            return;
        }

        Log::channel(config('iam.audit.channel', 'stack'))->info('IAM Authorization', [
            'decision' => $allowed ? 'ALLOW' : 'DENY',
            'user_id' => $user->id,
            'user_email' => $user->email,
            'action' => $action,
            'resource' => $arn->toString(),
            'context' => $context,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    public function clearUserCache(User $user): void
    {
        Cache::forget("user:{$user->id}:policies");

        // Clear all decision caches for this user using Redis SCAN
        $this->clearCachePattern("iam:decision:{$user->id}:*");
    }

    private function clearCachePattern(string $pattern): void
    {
        $store = Cache::getStore();

        if ($store instanceof \Illuminate\Cache\RedisStore) {
            $redis = Cache::getRedis();
            $cursor = 0;

            do {
                $result = $redis->scan($cursor, ['MATCH' => $pattern, 'COUNT' => 100]);

                if (is_array($result)) {
                    $cursor = $result[0];
                    $keys = $result[1];

                    if (!empty($keys)) {
                        $redis->del($keys);
                    }
                }
            } while ($cursor != 0);
        }
    }

    public function getPermissionMatrix(User $user): array
    {
        $policies = $this->getUserPolicies($user);

        $permissions = [];

        foreach ($policies as $policy) {

            $document = $policy->getDocument();

            foreach ($document->getStatements() as $statement) {

                if (!$statement->isAllow()) {
                    continue;
                }

                $actions = $statement->getActions();

                foreach ($statement->getResources() as $resource) {

                    // -----------------------------------
                    // CASE 0: Global wildcard "*"
                    // -----------------------------------
                    if ($resource === '*') {

                        $allResources = Resource::pluck('key')->toArray();
                        $allActions = config('iam.actions');

                        $flatActions = collect($allActions)->flatten()->unique()->toArray();

                        foreach ($allResources as $resourceKey) {
                            foreach (
                                in_array('*', $actions)
                                    ? $flatActions
                                    : $actions
                                as $action
                            ) {
                                $permissions[$resourceKey][] = $action;
                            }
                        }

                        continue;
                    }

                    // -----------------------------------
                    // Parse ARN (IAM layer)
                    // -----------------------------------
                    $arn = Arn::fromString($resource);

                    if (!$arn) {
                        continue;
                    }

                    $resourceModel = Resource::findByArn($arn);

                    if (!$resourceModel) {
                        continue;
                    }

                    $parent = $resourceModel->module_key;
                    $child  = $resourceModel->key;

                    // -----------------------------------
                    // CASE 1: Wildcard resource (registry:*)
                    // -----------------------------------
                    if ($arn->getResourceId() === '*' || str_ends_with($resource, ':*')) {

                        $children = Resource::where('module_key', $parent)
                            ->pluck('key')
                            ->toArray();

                        foreach ($children as $resourceKey) {
                            foreach ($actions as $action) {
                                $permissions[$resourceKey][] = $action;
                            }
                        }

                        continue;
                    }

                    // ----------------------- re------------
                    // CASE 2: Specificsource
                    // -----------------------------------
                    $resourceModel = Resource::where('module_key', $parent)
                        ->where('key', $child)
                        ->first();

                    if (!$resourceModel) {
                        continue;
                    }

                    foreach ($actions as $action) {
                        $permissions[$resourceModel->key][] = $action;
                    }
                }
            }
        }

        // -----------------------------------
        // Remove duplicates
        // -----------------------------------
        foreach ($permissions as $key => $actions) {
            $permissions[$key] = array_values(array_unique($actions));
        }

        return $permissions;
    }

    public function can(User $user, string $action, string $resource, array $context = []): bool
    {

        $key = "iam:snapshot:user:{$user->id}";

        $snapshot = $this->cache->get($key);


        if (!$snapshot) {
            $snapshot = app(PermissionCompilerService::class)->compile($user);
        }

        return in_array($action, $snapshot[$resource] ?? []);
    }

    public function buildSnapshot(User $user): array
    {
        $permissions = $this->getPermissionMatrix($user);
          app(CacheManager::class)->put("iam:snapshot:user:{$user->id}", $permissions, 3600);

        return $permissions;
    }

    public function forgetUserSnapshot(int $userId): void
    {
        app(CacheManager::class)->forget("iam:snapshot:user:{$userId}");
    }
}
