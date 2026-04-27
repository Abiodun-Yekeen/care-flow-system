<?php

namespace App\Modules\Core\Iam\Services;

use App\Modules\Core\Iam\Models\Role;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class RoleService
{
    public function __construct(
        Private Role  $role,
    )
    {}

    public function createRole(array $data)
    {
        return DB::transaction(function () use ($data) {
            $role = $this->role->create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'description' => $data['description'],

            ]);

            return $role;
        });
    }

    public function listRoles(array $filters)
    {
        return $this->paginate($filters);
    }

    public function updateRole(Role $role, array $data)
    {
        $role->update($data);
        return $role->refresh();
    }

    private function paginate(array $filters = [], int $perPage = 5): LengthAwarePaginator
    {
        return $this->role->query()
            ->withCount(['policies','users'])
            ->with(['policies:id,name', 'parents:id,name'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    // ILIKE is PostgreSQL's built-in case-insensitive search
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('display_name', 'ILIKE', "%{$search}%"); // Recommended to include email

                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();


    }


}
