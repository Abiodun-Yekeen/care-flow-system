<?php

namespace App\Modules\Core\Iam\Repository\Eloquent;

use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Repository\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryInterface
{
    protected $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function paginate(array $filters = [], int $perPage = 5): LengthAwarePaginator
    {
        return $this->model->query()
            ->with(['department:id,name', 'roles:id,name'])
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    // ILIKE is PostgreSQL's built-in case-insensitive search
                    $q->where('name', 'ILIKE', "%{$search}%")
                        ->orWhere('email', 'ILIKE', "%{$search}%") // Recommended to include email
                        ->orWhere('mobile_no', 'LIKE', "%{$search}%")
                        ->orWhere('staff_id', 'LIKE', "%{$search}%");
                });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findById(int $id): ?User
    {
        return $this->model->with(['department', 'roles'])->findOrFail($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data): bool
    {
        return $user->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->destroy($id);
    }
}
