<?php

namespace App\Modules\Core\Shared\Services;

use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Shared\Models\Module;
use App\Modules\Core\Shared\Repository\Contracts\ModuleRepositoryInterface;
use App\Modules\Core\Shared\Services\Cache\CacheManager;
use Illuminate\Support\Facades\Cache;
use phpDocumentor\Reflection\Types\Collection;

class NavigationService
{
    public function __construct(
        private CacheManager $cache,
        private PermissionCompilerService $CompilerService,
    ) {}
    public function sidebar(User $user): array
    {

        $snapshot = $this->cache->get("iam:snapshot:user:{$user->id}") ?? [];

        if (!$snapshot) {
            $snapshot = $this->CompilerService->compile($user);
        }

        return Resource::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->filter(function ($r) use ($snapshot) {
                $userPerms = $snapshot[$r->key] ?? [];

                // Return true if they have 'view' OR if they have ANY other permission
                return in_array('view', $userPerms) || !empty($userPerms);
            })
            ->map(fn ($r) => [
                'key' => $r->key,
                'label' => $r->name,
                'route' => $r->route,
                'icon' => $r->icon,
                'children' => $this->children($snapshot, $r)
            ])
            ->values()
           ->toArray();
    }

    private function children($snapshot, $parent)
    {
        return $parent->children() // or however you access the relationship
        ->where('is_active', true)
            ->get()
            ->filter(function ($child) use ($snapshot) {
                $childPerms = $snapshot[$child->key] ?? [];
                return in_array('view', $childPerms) || !empty($childPerms);
            })
            ->map(fn ($child) => [
                'key' => $child->key,
                'label' => $child->name,
                'route' => $child->route,
                'icon' => $child->icon,
            ])
            ->values()
            ->toArray();
    }

    public function buildBreadcrumb(string $path): array
    {
        $segments = request()->segments();

        $map = $this->cache->remember('iam:breadcrumb:map', 86400, function () {
            return Resource::query()
                ->select('key', 'name as label')
                ->get()
                ->keyBy('key');
        });

        $breadcrumb = [];
        $accumulated = '';

        if (empty($segments) || $segments[0] !== 'dashboard') {
            $breadcrumb[] = [
                'label' => 'Dashboard',
                'route' => '/dashboard',
            ];
        }

        foreach ($segments as $segment) {
            $accumulated .= '/' . $segment;

            if (isset($map[$segment])) {
                $breadcrumb[] = [
                    'label' => $map[$segment]->label,
                    'route' => $accumulated,
                ];
            }
        }

        return $breadcrumb;
    }
}
//app(CacheManager::class)->forget("nav:roles:{$roleSignature}");
