<?php

namespace App\Modules\Core\Iam\Services;

use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Models\User;
use Illuminate\Support\Collection;

class EffectiveAccessService
{
    public function getUserRoles(User $user): Collection
    {
        $user->loadMissing('roles.parents.policies', 'roles.policies');

        $roles = collect();

        foreach ($user->roles as $role) {
            $this->collectRoleWithParents($role, $roles);
        }

        return $roles->unique('id')->values();
    }

    public function getRolePolicies(Role $role): Collection
    {
        $role->loadMissing('policies', 'parents.policies', 'parents.parents');

        $roles = collect();
        $this->collectRoleWithParents($role, $roles);

        return $roles
            ->flatMap(fn ($role) => $role->policies)
            ->unique('id')
            ->values();
    }

    public function getUserPolicies(User $user): Collection
    {
        return $this->getUserRoles($user)
            ->flatMap(fn ($role) => $role->policies)
            ->unique('id')
            ->values();
    }

    public function resolveUserMatrix(User $user): array
    {
        $policies = $this->getUserPolicies($user);

        return $this->buildPermissionMatrix($policies);
    }

    public function resolveRoleMatrix(Role $role): array
    {
        $policies = $this->getRolePolicies($role);

        return $this->buildPermissionMatrix($policies);
    }

    public function simulateForPolicies(Collection $policies, string $action, string $resourceArn): array
    {
        $matched = [];

        foreach ($policies as $policy) {
            $document = $policy->document ?? [];
            $statements = $document['statements'] ?? [];

            foreach ($statements as $statement) {
                $actionMatched = $this->matchesAction($action, $statement['actions'] ?? []);
                $resourceMatched = $this->matchesResource($resourceArn, $statement['resources'] ?? []);

                if ($actionMatched && $resourceMatched) {
                    $matched[] = [
                        'policy_id' => $policy->id,
                        'policy_name' => $policy->name,
                        'sid' => $statement['sid'] ?? null,
                        'effect' => strtolower($statement['effect'] ?? 'deny'),
                        'actions' => $statement['actions'] ?? [],
                        'resources' => $statement['resources'] ?? [],
                    ];
                }
            }
        }

        $hasDeny = collect($matched)->contains(fn ($row) => $row['effect'] === 'deny');
        $hasAllow = collect($matched)->contains(fn ($row) => $row['effect'] === 'allow');

        return [
            'result' => $hasDeny ? 'denied' : ($hasAllow ? 'allowed' : 'not_granted'),
            'matched_statements' => $matched,
        ];
    }

    protected function buildPermissionMatrix(Collection $policies): array
    {
        $matrix = [];

        foreach ($policies as $policy) {
            $document = $policy->document ?? [];
            $statements = $document['statements'] ?? [];

            foreach ($statements as $statement) {
                $effect = strtolower($statement['effect'] ?? 'deny');
                $actions = $statement['actions'] ?? [];
                $resources = $statement['resources'] ?? [];

                foreach ($resources as $resource) {
                    foreach ($actions as $action) {
                        $key = $resource . '|' . $action;

                        if (! isset($matrix[$key])) {
                            $matrix[$key] = [
                                'resource' => $resource,
                                'action' => $action,
                                'result' => $effect === 'deny' ? 'denied' : 'allowed',
                                'sources' => [[
                                    'policy_id' => $policy->id,
                                    'policy_name' => $policy->name,
                                    'sid' => $statement['sid'] ?? null,
                                    'effect' => $effect,
                                ]],
                            ];
                            continue;
                        }

                        $matrix[$key]['sources'][] = [
                            'policy_id' => $policy->id,
                            'policy_name' => $policy->name,
                            'sid' => $statement['sid'] ?? null,
                            'effect' => $effect,
                        ];

                        if ($effect === 'deny') {
                            $matrix[$key]['result'] = 'denied';
                        }
                    }
                }
            }
        }

        return array_values($matrix);
    }

    protected function collectRoleWithParents(Role $role, Collection &$roles): void
    {
        if ($roles->contains('id', $role->id)) {
            return;
        }

        $role->loadMissing('parents.policies', 'parents.parents', 'policies');
        $roles->push($role);

        foreach ($role->parents as $parent) {
            $this->collectRoleWithParents($parent, $roles);
        }
    }

    protected function matchesAction(string $action, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($pattern === '*') {
                return true;
            }

            if ($pattern === $action) {
                return true;
            }

            if (str_contains($pattern, '*')) {
                $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
                if (preg_match($regex, $action)) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function matchesResource(string $resourceArn, array $patterns): bool
    {
        foreach ($patterns as $pattern) {
            if ($pattern === '*') {
                return true;
            }

            if ($pattern === $resourceArn) {
                return true;
            }

            if (str_contains($pattern, '*')) {
                $regex = '/^' . str_replace('\*', '.*', preg_quote($pattern, '/')) . '$/';
                if (preg_match($regex, $resourceArn)) {
                    return true;
                }
            }
        }

        return false;
    }
}
