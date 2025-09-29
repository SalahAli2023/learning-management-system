<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use App\Models\Course;
use App\Models\Lesson;

class UpdateLessonRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $lesson = $this->route('lesson');
        
        if (!$lesson instanceof Lesson) {
            $lesson = Lesson::find($lesson);
        }

        if (!$lesson) {
            return false;
        }

        return Gate::allows('update', $lesson);
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'duration' => ['required', 'integer', 'min:1', 'max:480'],
            'is_free' => ['required', 'boolean'],
            'file' => ['required', 'file', 'max:102400', 'mimes:pdf,doc,docx,mp4,mov,avi,jpg,jpeg,png'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Lesson title is required.',
            'title.max' => 'Lesson title may not be greater than 255 characters.',
            'description.max' => 'Description may not exceed 1000 characters.',
            'duration.required' => 'Duration is required.',
            'duration.integer' => 'Duration must be a valid number.',
            'duration.min' => 'Duration must be at least 1 minute.',
            'duration.max' => 'Duration may not exceed 480 minutes (8 hours).',
            'is_free.required' => 'Please specify if the lesson is free or not.',
            'is_free.boolean' => 'Invalid value for free flag.',
            'file.file' => 'The uploaded file is not valid.',
            'file.max' => 'File size must be less than 50MB.',
            'file.mimes' => 'Supported file types: PDF, DOC, MP4, MOV, AVI, JPG, PNG.',
        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'lesson title',
            'description' => 'description',
            'duration' => 'duration',
            'is_free' => 'free lesson',
            'file' => 'file',
        ];
    }

    
}
