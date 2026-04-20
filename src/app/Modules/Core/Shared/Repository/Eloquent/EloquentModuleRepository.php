<?php

namespace App\Modules\Core\Shared\Repository\Eloquent;

use App\Modules\Core\Shared\Models\Module;
use App\Modules\Core\Shared\Repository\Contracts\ModuleRepositoryInterface;
use Illuminate\Support\Collection;

class EloquentModuleRepository implements ModuleRepositoryInterface
{

    public function getRootModules(array $keys): Collection
    {
        return Module::query()
            ->whereNull('parent_id')
            ->whereIn('key', $keys)
            ->where('is_active', true)
            ->orderBy('order')
            ->get(['id','key','label','icon','route']);
    }


    public function getChildrenByParentRouteSegment(string $segment): Collection
    {
        $parentRoute = '/' . ltrim($segment, '/');

        $parent = Module::query()
            ->where('route', $parentRoute)
            ->first();

        if (!$parent) {
            return collect();
        }

        return Module::query()
            ->where('parent_id', $parent->id)
            ->where('is_active', true)
            ->orderBy('order')
            ->get(['id', 'key', 'label', 'icon', 'route']);
    }

    public function findByKey(string $key): ?object
    {
        return Module::query()
            ->where('key', $key)
            ->first();
    }

}
