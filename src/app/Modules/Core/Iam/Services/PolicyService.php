<?php

namespace App\Modules\Core\Iam\Services;

use App\Modules\Core\Iam\Models\Policy;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PolicyService
{
    public function __construct(
        Private Policy  $policy,
    )
    {}

    public function createRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = $this->$policy->create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'description' => $data['description'],

            ]);

            return $policy;
        });
    }

    public function listRoles(array $filters)
    {
        return $this->paginate($filters);
    }

    public function updateRole(Policy $policy, array $data)
    {
        $policy->update($data);
        return $policy->refresh();
    }

    private function paginate(array $filters = [], int $perPage = 5): LengthAwarePaginator
    {
        return $this->policy->query()
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    // ILIKE is PostgreSQL's built-in case-insensitive search
                    $q->where('name', 'ILIKE', "%{$search}%");

                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }


}
