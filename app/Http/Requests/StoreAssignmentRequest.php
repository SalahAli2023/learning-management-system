<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssignmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
        // return $this->user()->role === 'instructor' && 
        //         $this->user()->id === $this->route('course')->instructor_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'due_date' => 'nullable|date|after:now',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Assignment title is required.',
            'title.max' => 'Assignment title may not be greater than 255 characters.',
            'description.max' => 'Description may not be greater than 1000 characters.',
            'due_date.after' => 'Due date must be a future date.',
        ];
    }
}
