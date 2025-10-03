<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GradeSubmissionRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $submission = $this->route('submission');
        return $this->user()->role === 'instructor' && 
                $this->user()->id === $submission->assignment->course->instructor_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'grade' => 'required|numeric|min:0|max:100',
            'feedback' => 'nullable|string|max:2000',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'grade.required' => 'Grade is required.',
            'grade.numeric' => 'Grade must be a number.',
            'grade.min' => 'Grade must be at least 0.',
            'grade.max' => 'Grade may not be greater than 100.',
            'feedback.max' => 'Feedback may not be greater than 2000 characters.',
        ];
    }
}
