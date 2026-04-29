<?php

namespace App\Modules\Core\Iam\Services;

use App\Modules\Core\Iam\Models\Policy;
use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Security\Arn;
use App\Modules\Core\Iam\Security\PolicyEvaluator;
use App\Modules\Core\Shared\Services\Cache\CacheManager;
use App\Modules\Core\Shared\Services\PermissionCompilerService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class IamAuthorizationService
{
    private PolicyEvaluator $evaluator;
    private CacheManager $cache; // Use your custom manager

    public function __construct(PolicyEvaluator $evaluator, CacheManager $cache)
    {
        $this->evaluator = $evaluator;
        $this->cache = $cache;
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
            'user'               => $user,
            'user_id'            => $user->id,
            'user_email'         => $user->email,
            'user:department_id' => $user->department_id,
            'user:role'          => $this->getUserRoleIds($user),
        ], $context);


        $policies = $this->getUserPolicies($user);

        // --- TEMPORARY DEBUG START ---
        if ($action !== 'dashboard.view') { // Avoid spamming the dashboard
            dump([
                'Action' => $action,
                'User_Dept' => $user->department_id,
                'Context_Sent' => $evalContext,
                'Policies_Found' => $policies->pluck('name')->toArray(),
                'First_Policy_Statements' => $policies->first()?->statements,
            ]);
        }
        // --- TEMPORARY DEBUG END ---


        try {
            // Replace placeholders in REQUEST resource
            if (is_string($resource)) {
                $resource = str_replace(
                    ['${user.id}', '${user_id}'],
                    (string) $user->id,
                    $resource
                );
            }

            $arn = $this->resolveArn($resource);

            //Use evalContext for cache key too
            $cacheKey = $this->getCacheKey($user, $action, $arn, $evalContext);

            return $this->cache->remember($cacheKey, 3600, function () use ($user, $action, $arn, $evalContext) {

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
        // Resolve the ARN (This will handle 'arn:cf:office_files:my_desk:*')
        $arn = $this->resolveArn($resource);

        // Determine the Resource Key to look up actions in config
        // If it's an instance (like File-123), we need the base key (my_desk)
        if ($resource instanceof Resource) {
            $resourceKey = $resource->key;
        } elseif (is_string($resource) && str_contains($resource, 'arn:')) {
            // Extract 'my_desk' from 'arn:cf:office_files:my_desk:file-123'
            $parts = explode(':', $resource);
            $resourceKey = $parts[3] ?? (string)$resource;
        } else {
            $resourceKey = (string)$resource;
        }

        // Get the actions from config (e.g., my_desk:ListInTray, etc.)
        $possibleActions = config("iam.actions.{$resourceKey}");

        // Fallback if config is missing
        if (empty($possibleActions)) {
            $possibleActions = array_map(fn($a) => "{$resourceKey}:{$a}", ['view', 'create', 'update']);
        }

        $policies = $this->getUserPolicies($user);

        //  Evaluate against the specific ARN passed (Wildcard or ID)
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

        // Suggestion: Use cache for Resource lookups to speed up the UI
        $resource = $this->cache->remember("iam:res_def:{$resourceKey}", 3600, function() use ($resourceKey) {
            return Resource::where('key', $resourceKey)->first();
        });

        if (!$resource) {
            return []; // Resource not registered in IAM, deny everything
        }

        $instanceContext = array_merge($context, [
            'resource:id'         => $instance->id,
            'resource:owner'      => $instance->user_id ?? null,
            'resource:department' => $instance->department_id ?? null,
            'resource:status'     => $instance->status ?? null,
            'resource:type'       => $resourceKey,
        ]);

        // Build the specific ARN: arn:cf:office_files:my_desk:file-123
        $specificArn = $resource->arnFor($instance->id);

        return $this->getAllowedActions($user, $specificArn, $instanceContext);
    }

    private function getUserPolicies(User $user): Collection
    {
        return $this->cache->remember("user:{$user->id}:policies", 3600, function () use ($user) {
            //  Get the Role IDs (This method should fetch from DB, not $user->roles)
            $roleIds = $this->getUserRoleIds($user);

            if (empty($roleIds)) {
                return collect();
            }

            // Fetch all policies linked to any of these roles
            // We use whereHas to perform a clean join in the DB
            return Policy::whereHas('roles', function ($query) use ($roleIds) {
                $query->whereIn('roles.id', $roleIds);
            })->get();
        });
    }

    private function getUserRoleIds(User $user): array
    {
        // allRelatedIds() will now correctly use 'user_roles'
        $directRoleIds = $user->roles()->allRelatedIds()->toArray();

        $allRoleIds = $directRoleIds;

        if (!empty($directRoleIds)) {
            // Fetch Role models to process inheritance logic
            $roles = Role::whereIn('id', $directRoleIds)->get();

            foreach ($roles as $role) {
                // Ensure this method exists on your Role model
                // and returns an array of IDs
                $inherited = $role->getAllInheritedRoles();
                $allRoleIds = array_merge($allRoleIds, $inherited);
            }
        }

        return array_unique($allRoleIds);
    }

    private function resolveArn($resource): Arn
    {
        // 1. Handle Null or Empty immediately to prevent the crash
        if (!$resource) {
            return Arn::fromString('arn:cf:::');
        }

        if ($resource instanceof Arn) {
            return $resource;
        }

        if ($resource instanceof Resource) {
            return $resource->arn;
        }

        if (is_string($resource)) {
            // Check if it's already an ARN string
            try {
                // Only try parsing if it looks like an ARN
                if (str_starts_with($resource, 'arn:')) {
                    $arn = Arn::fromString($resource);
                    if ($arn) return $arn;
                }
            } catch (\Exception $e) {}

            // Look up by 'key' or 'name'
            $resourceModel = Resource::where('key', $resource)
                ->orWhere('name', $resource)
                ->first();

            // Instead of throwing an exception, log it and return a null ARN
            // This stops the 500 error and lets the login proceed
            if (!$resourceModel) {
                Log::debug("IAM: Resource definition for '{$resource}' not found.");
                return Arn::fromString("arn:cf:unknown:{$resource}:*");
            }

            return $resourceModel->arn;
        }

        return Arn::fromString('arn:cf:::');
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
        $this->cache->forget("user:{$user->id}:policies");

        $this->cache->forget("iam:snapshot:user:{$user->id}");

        // Clear all decision caches for this user using Redis SCAN
        $this->clearCachePattern("iam:decision:{$user->id}:*");
        //Force a rebuild of the snapshot immediately
       // $this->buildSnapshot($user);
    }

    private function clearCachePattern(string $pattern): void
    {
        // Check if our custom manager is currently using Redis
        if (!$this->cache->isUsingRedis()) {
            // If Redis is down, we are using the Database store.
            // Pattern clearing 'database' is harder (requires SQL LIKE),
            // but since Redis is the primary for performance, we handle it here.
            return;
        }

        try {
            // Get the Redis connection directly via Laravel's facade
            // (This matches how your manager pings it)
            $redis = \Illuminate\Support\Facades\Redis::connection();
            $prefix = config('database.redis.options.prefix', '');

            $fullPattern = $prefix . $pattern;
            $cursor = 0;

            do {
                // SCAN for the keys
                $result = $redis->scan($cursor, ['MATCH' => $fullPattern, 'COUNT' => 100]);

                if (is_array($result)) {
                    $cursor = $result[0];
                    $keys = $result[1];

                    if (!empty($keys)) {
                        // We must strip the prefix before passing back to Redis del
                        // if the client adds it automatically, or just use the raw del.
                        $strippedKeys = array_map(function($key) use ($prefix) {
                            return str_replace($prefix, '', $key);
                        }, $keys);

                        $redis->del($strippedKeys);
                    }
                }
            } while ($cursor != 0);

        } catch (\Throwable $e) {
            Log::warning("IAM: Failed to clear Redis pattern: " . $e->getMessage());
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
          $this->cache->put("iam:snapshot:user:{$user->id}", $permissions, 3600);

        return $permissions;
    }

    public function forgetUserSnapshot(int $userId): void
    {
        app(CacheManager::class)->forget("iam:snapshot:user:{$userId}");
    }
}
