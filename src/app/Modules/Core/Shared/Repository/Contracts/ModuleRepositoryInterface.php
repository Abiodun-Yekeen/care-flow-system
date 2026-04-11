<?php

namespace App\Modules\Core\Shared\Repository\Contracts;
use Illuminate\Support\Collection;

interface ModuleRepositoryInterface
{

    public function getRootModules(array $keys): Collection;

    public function getChildrenByParentKey(string $parentKey): Collection;

    public function findByKey(string $key): ?object;
}
