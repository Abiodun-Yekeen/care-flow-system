<?php

namespace App\Modules\Core\Iam\DTO;

use Illuminate\Support\Facades\Hash;

class UserDTO
{

    public function __construct(
        public readonly string $name,
        public readonly string $staff_id,
        public readonly string $mobile_no,
        public readonly ?int $department_id,
        public readonly ?string $email,
        public readonly string $password,


    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            staff_id: $data['staff_id'],
            mobile_no: $data['mobile_no'],
            department_id:  $data['department'],
            email: $data['email'],
            password: '',

        );
    }


        public function toArray(): array
    {
        $data = [
            'name'          => $this->name,
            'staff_id'      => $this->staff_id,
            'mobile_no'     => $this->mobile_no,
            'department_id' => $this->department_id,
            'email'         => $this->email,
        ];

        // Only add password to the array if a new one was actually typed
        if (!empty($this->password)) {
            $data['password'] = Hash::make($this->password);
        }

        return $data;
    }

}
