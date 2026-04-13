<?php

namespace App\Modules\Core\Iam\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequestUpload extends FormRequest
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
            'excel_file' => 'required|mimes:xlsx,csv,xls',
        ];

    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [

            // Add other custom messages as needed
        ];
    }
}
