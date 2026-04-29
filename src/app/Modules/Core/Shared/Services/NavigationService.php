<?php

namespace App\Modules\Core\Shared\Services;

use App\Modules\Core\Iam\Models\Resource;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Services\IamAuthorizationService;
use App\Modules\Core\Shared\Services\Cache\CacheManager;

class NavigationService
{
    public function __construct(
        private CacheManager $cache,
        private IamAuthorizationService $iamService,
    ) {}

    public function sidebar(User $user): array
    {
        // 1. Get snapshot from cache or compile it
        $snapshot = $this->cache->get("iam:snapshot:user:{$user->id}");

        if (empty($snapshot)) {
            $snapshot = $this->iamService->buildSnapshot($user);
        }

        // Fetch Top-Level Menu Items
        return Resource::whereNull('parent_id')
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->filter(function ($r) use ($snapshot, $user) {
                // Super Admin sees everything
                if ($user->id === 1) return true;

                // Others see if they have ANY permission for this key
                return !empty($snapshot[$r->key]);
            })
            ->map(fn ($r) => [
                'key'      => $r->key,
                'label'    => $r->name,
                'route'    => $r->route ?? '#', // Ensure route isn't null for frontend
                'icon'     => $r->icon,
                'children' => $this->children($snapshot, $r, $user)
            ])
            ->values()
            ->toArray();
    }

    private function children(array $snapshot, Resource $parent, User $user): array
    {
        return $parent->children()
            ->where('is_active', true)
            ->orderBy('order')
            ->get()
            ->filter(function ($child) use ($snapshot, $user) {
                if ($user->id === 1) return true;
                return !empty($snapshot[$child->key]);
            })
            ->map(fn ($child) => [
                'key'   => $child->key,
                'label' => $child->name,
                'route' => $child->route ?? '#',
                'icon'  => $child->icon,
            ])
            ->values()
            ->toArray();
    }

    public function buildBreadcrumb(string $path): array
    {
        $segments = request()->segments();

        // Cached map for performance
        $map = $this->cache->remember('iam:breadcrumb:map', 86400, function () {
            return Resource::query()
                ->select('key', 'name as label')
                ->get()
                ->keyBy('key');
        });

        $breadcrumb = [];
        if (empty($segments) || $segments[0] !== 'dashboard') {
            $breadcrumb[] = ['label' => 'Dashboard', 'route' => '/dashboard'];
        }

        $accumulated = '';
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
