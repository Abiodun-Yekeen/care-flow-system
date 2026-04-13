<?php

namespace App\Modules\Core\Iam\Services;


use App\Modules\Core\Iam\Models\Role;
use App\Modules\Core\Iam\Models\User;
use App\Modules\Organization\Department\Models\Department;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UserImportService implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        /** * $row['role'] and $row['department'] now refer to the
         * column headers in your Excel, regardless of their position.
         */

        $dept = Department::where('name', 'ILIKE', trim($row['department']))->first();
        $role = Role::where('name', 'ILIKE', trim($row['role']))->first();

        $user = new User([
            'name'          => $row['name'],
            'email'         => $row['email']?? null,
            'staff_id'      => $row['staff_id'],
            'mobile_no'     => $row['mobile_no'] ?? null,
            'department_id' => $dept?->id,
            'password'      => Hash::make('password123'),
        ]);

        $user->save();

        if ($role) {
            $user->roles()->sync([$role->id]);
        }

        return $user;
    }

    public function rules(): array
    {
        return [
            'name'       => 'required|string',
            'email'      => 'nullable',
            'staff_id'   => 'required|unique:users,staff_id',
            'mobile_no'   => 'required|unique:users,mobile_no',
            // Flexible validation: ensure the name exists in the DB
            'department' => 'required|exists:departments,name',
            'role'       => 'required|exists:roles,name',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'role.exists' => 'The role ":input" in your file does not exist in the system.',
            'department.exists' => 'The department ":input" was not found.',
        ];
    }
}
