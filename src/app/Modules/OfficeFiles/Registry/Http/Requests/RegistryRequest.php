<?php

namespace App\Modules\OfficeFiles\Registry\Http\Requests;

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
            'subject' => ['required', 'string', 'max:500'],
            'is_draft' => ['nullable'],
            'deadline_at'=>['nullable','date','after:date_received'],
            'receive_department_id' => ['nullable'],
            'source_reference_no' => ['nullable'], // Who sent the letter?
            'received_from' => ['nullable', 'string'],
            'priority' => ['required'],
            'date_received' => ['required', 'date', 'before_or_equal:today'],
            'remark' => ['nullable', 'string'],
            // Validation for the base64 images/documents
            'scanned_file' => ['nullable', 'array'],
            'source_name' => [
                'required',
                Rule::unique('files')->where(function ($query) {
                    return $query->where('source_name', $this->source_name)
                        ->where('date_received', $this->date_received)
                       ->where('source_reference_no', $this->source_reference_no);
                })->ignore($this->route('register')?->file_id, 'id')
            ],


        ];
    }

    public function messages(): array
    {
        return [
            'subject.required' => 'A file subject is required for registration.',
            'receive_department_id.required' => 'Please select the destination department.',
            'file_path.required' => 'You must upload at least one scanned document.',
        ];
    }
}
