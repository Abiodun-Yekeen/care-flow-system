<?php

namespace App\Modules\Core\Iam\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true; // Add your authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name' => ['required',
                'string',
                'max:255',
                 Rule::unique('roles', 'name')->ignore($this->role)
            ],
            'description' => ['nullable',],
            'display_name' => [
                'required',
                Rule::unique('roles', 'display_name')->ignore($this->role),
            ],
            'metadata' => ['nullable',],

        ];

    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The  Name is required.',
            'display_name.required' => 'The Display Name  is required.',
            // Add other custom messages as needed
        ];
    }
}
