<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnrollmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status.required' => 'Status is required.',
            'status.in'       => 'Status must be one of: pending, active, completed.',
        ];
    }

    public function messages(): array
    {
        return [
            'student_id.required' => 'Please select a student.',
            'student_id.exists'   => 'Invalid student selected.',
            'course_id.required'  => 'Please select a course.',
            'course_id.exists'    => 'Invalid course selected.',
            'status.required'     => 'Status is required.',
            'status.in'           => 'Status must be one of: pending, active, completed.',
        ];
    }
}
