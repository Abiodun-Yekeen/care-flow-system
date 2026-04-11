<?php

namespace App\Modules\Core\Shared\Services;

use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Shared\Models\Module;
use App\Modules\Core\Shared\Repository\Contracts\ModuleRepositoryInterface;
use Illuminate\Support\Facades\Cache;
use phpDocumentor\Reflection\Types\Collection;

class ModuleService
{
    public function __construct(
        protected IamAuthorizationService $iam,
        protected ModuleRepositoryInterface $modules
    ) {}

    /**
     * Get modules user can VIEW (used for navigation)
     */
    protected function getViewableModuleKeys(User $user): array
    {
        $permissions = $this->iam->getPermissionMatrix($user);

        return collect($permissions)
            ->filter(fn ($actions) => in_array('view', $actions))
            ->keys()
            ->toArray();
    }

    /**
     * Sidebar parent modules
     */
    public function getSidebarModules(User $user): array
    {
        $viewable = $this->getViewableModuleKeys($user);

        $roleSignature = $this->getRoleSignature($user);

        return $this->modules
            ->getRootModules($viewable)
            ->values()
            ->toArray();
//        return Cache::remember(
//            "sidebar:roles:{$roleSignature}",
//            now()->addMinutes(60),



    }

    /**
     * Children for current parent
     */
    public function getChildrenModules(User $user, string $parentKey): array
    {
        $viewable = $this->getViewableModuleKeys($user);

        return $this->modules
            ->getChildrenByParentKey($parentKey)
            ->filter(fn ($module) => in_array($module->key, $viewable))
            ->values()
            ->toArray();
    }

    public function buildBreadcrumb(string $path): array
    {
        // Use request segments to avoid manual string manipulation issues
        $segments = request()->segments();

        $breadcrumb = [];
        $accumulated = '';

        // IF the segments array is empty (we are at the root /)
        // OR if we want to ensure Dashboard is ALWAYS the first link:
        if (empty($segments) || $segments[0] !== 'dashboard') {
            $dashModule = $this->modules->findByKey('dashboard');
            if ($dashModule) {
                $breadcrumb[] = [
                    'label' => $dashModule->label,
                    'route' => '/dashboard',
                ];
            }
        }

        foreach ($segments as $segment) {
            $accumulated .= '/' . $segment;
            $module = $this->modules->findByKey($segment);

            if ($module) {
                // Check to avoid double-adding dashboard if it's already there
                $alreadyExists = collect($breadcrumb)->contains('label', $module->label);

                if (!$alreadyExists) {
                    $breadcrumb[] = [
                        'label' => $module->label,
                        'route' => $accumulated,
                    ];
                }
            }
        }

        return $breadcrumb;
    }

    protected function getRoleSignature($user): string
    {
        return $user->roles()
            ->pluck('roles.id')
            ->sort()
            ->implode('-');
    }

}
