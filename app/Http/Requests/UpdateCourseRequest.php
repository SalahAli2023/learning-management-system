<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;

class UpdateCourseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $course = $this->route('course'); // جاي من المسار instructor/courses/{course}
        return Gate::allows('update', $course);    
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|min:4|max:255',
            'category' => 'required|string|min:4|max:100',
            'description' => 'nullable|string',
            'cover_url' => 'nullable|url',
            'is_published' => 'boolean',
        ];
    }
    
    public function messages(): array
    {
        return [
            'title.required' => 'Course title is required.',
            'title.min' => 'Course title must be more than 4 character.',
            'category.required' => 'Category is required.',
            'category.min' => 'Category must be more than 4 character.',
        ];
    }
}
