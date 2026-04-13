<?php

namespace App\Modules\Core\Shared\Repository\Eloquent;


namespace App\Modules\Core\Shared\Repository\Eloquent;

use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Shared\Repository\Contracts\LoadDataRepositoryInterface;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EloquentLoadDataRepository implements LoadDataRepositoryInterface
{
    /**
     * Cache TTL in seconds (1 hour)
     */
    private const CACHE_TTL = 3600;

    /**
     * Model mapping for better maintainability
     */
    private const MODEL_MAP = [
        'roles' => Role::class,
        'departments'=>Department::class,
    ];

    /**
     * Field mapping for different data types
     */
    private const FIELD_MAP = [
        'departments' => ['id', 'name'],
        'roles' => ['id', 'name'],
    ];

    public function getRoles(): Collection
    {
        return $this->getFormattedOptions('roles');
    }

    public function getDepartments(): Collection
    {
        return $this->getFormattedOptions('departments');
    }

    public function getAllFormatted(): array
    {
            return [
                'departments' => $this->getDepartments(),
                'roles' => $this->getRoles(),
            ];
    }
    /**
     * Generic method to get formatted options
     */
    private function getFormattedOptions(string $type): Collection
    {
        if (!isset(self::MODEL_MAP[$type])) {
            return collect();
        }

        return Cache::remember("{$type}.formatted", self::CACHE_TTL, function () use ($type) {
            $model = self::MODEL_MAP[$type];
            $fields = self::FIELD_MAP[$type] ?? ['name'];

            return $model::query()
                ->orderBy('name')
                ->get($fields)
                ->map(fn($item) => $this->formatItem($item, $type));
        });
    }

    /**
     * Format individual item based on type
     */
    private function formatItem($item, string $type): array
    {
        $base = [
            'value' => $item->id,
            'label' => $item->name,
        ];

        return $base;
    }
}
