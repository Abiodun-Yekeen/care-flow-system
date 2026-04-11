<?php

namespace App\Modules\Core\Shared\Repository\Eloquent;


namespace App\Modules\Core\Shared\Repository\Eloquent;

use App\Modules\Clinical\Encounter\Models\EncounterType;
use App\Modules\Core\Shared\Models\BloodGroup;
use App\Modules\Core\Shared\Models\Gender;
use App\Modules\Core\Shared\Models\Genotype;
use App\Modules\Core\Shared\Models\Lga;
use App\Modules\Core\Shared\Models\MaritalStatus;
use App\Modules\Core\Shared\Models\Relationship;
use App\Modules\Core\Shared\Models\Severity;
use App\Modules\Core\Shared\Models\State;
use App\Modules\Core\Shared\Models\Title;
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
        'genders' => Gender::class,
        'titles' => Title::class,
        'marital_statuses' => MaritalStatus::class,
        'departments'=>Department::class,
    ];

    /**
     * Field mapping for different data types
     */
    private const FIELD_MAP = [
        'genders' => ['id','name'],
        'titles' => ['id','name'],
        'marital_statuses' => ['id','name'],
        'departments' => ['id', 'name'],
    ];

    public function getGenders(): Collection
    {
        return $this->getFormattedOptions('genders');
    }

    public function getTitles(): Collection
    {
        return $this->getFormattedOptions('titles');
    }

    public function getMaritalStatuses(): Collection
    {
        return $this->getFormattedOptions('marital_statuses');
    }

    public function getDepartments(): Collection
    {
        return $this->getFormattedOptions('departments');
    }

    public function getAllFormatted(): array
    {
            return [
                'gender' => $this->getGenders(),
                'title' => $this->getTitles(),
                'marital_status' => $this->getMaritalStatuses(),
                'departments' => $this->getDepartments(),
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
