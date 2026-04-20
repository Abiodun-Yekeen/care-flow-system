<?php

namespace App\Modules\Core\Iam\Services;

use App\Modules\Core\Iam\DTO\UserDTO;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Core\Iam\Repository\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;

class RoleService
{
    public function __construct(
        Private UserRepositoryInterface $userRepo
    )
    {}

    public function createUser(UserDTO $dto)
    {
        return DB::transaction(function () use ($dto) {
            $password = Hash::make( 'password123');
            $user = $this->userRepo->create([
                'name' => $dto->name,
                'staff_id' => $dto->staff_id,
                'mobile_no' => $dto->mobile_no,
                'email' => $dto->email,
                'department_id' => $dto->department_id,
                'password' => $password,
            ]);

            if (isset($dto->role_id)) {
                $user->roles()->sync($dto->role_id);
            }

            return $user;
        });
    }

    public function listUsers(array $filters)
    {
        return $this->userRepo->paginate($filters);
    }

    public function updateUser(User $user, UserDTO $dto)
    {
        // Fill the model with the new data from DTO
        $user->fill($dto->toArray());
        // Check if any database columns changed
        if ($user->isDirty()) {
            // This will only update the columns that are different
            $this->userRepo->update($user, $user->getDirty());
        }
        // Handle Roles separately (Sync only if provided)
        if (!empty($dto->role_id)) {
            $user->roles()->sync($dto->role_id);
        }

        return $user->refresh();
    }

    /**
     * Handle the excel import logic
     */
    public function importUsers($file)
    {
        // This will trigger the ToModel and WithValidation logic in your Import class
        return Excel::import(new UserImportService(), $file);
    }



}
