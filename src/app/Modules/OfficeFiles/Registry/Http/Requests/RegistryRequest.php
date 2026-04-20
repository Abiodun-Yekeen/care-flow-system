<?php


use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'staff_id' => [
                'required',
                Rule::unique('users', 'staff_id')->ignore($this->user),
            ],
            'mobile_no' => [
                'required',
                Rule::unique('users', 'mobile_no')->ignore($this->user),
            ],
            'email' => [
                'nullable',
                'email',
            ],
            'department' => ['required'],
        ];

    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'The  name is required.',
            'staff_id.required' => 'The Staff id is required.',
            // Add other custom messages as needed
        ];
    }
}
