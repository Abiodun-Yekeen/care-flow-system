<?php
namespace App\Modules\Core\Iam\Repository\Contracts;
use App\Modules\Core\Iam\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function paginate(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findById(int $id): ?User;

    public function create(array $data): User;

    public function update(User $user, array $data): bool;

    public function delete(int $id): bool;
}
