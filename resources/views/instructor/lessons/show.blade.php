{{-- <x-app-layout>
    <lessons-show
        :course="{{ $course->toJson() }}"
        :lesson="{{ $lesson->toJson() }}"
        index-route="{{ route('instructor.courses.lessons.index', $course->id) }}"
        edit-route="{{ route('instructor.courses.lessons.edit', [$course->id, $lesson->id]) }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-show>
</x-app-layout> --}}

<x-app-layout>
    <lessons-show
        :lesson="{{ $lesson->toJson() }}"
        :course="{{ $course->toJson() }}"
        index-route="{{ route('instructor.courses.lessons.index', $course->id) }}"
        edit-route="{{ route('instructor.courses.lessons.edit', [$course->id, $lesson->id]) }}"
        enroll-route="{{ route('student.courses.enroll', $course->id) }}"
        :is-instructor="{{ auth()->user()->role === 'instructor' ? 'true' : 'false' }}"
        :is-student="{{ auth()->user()->role === 'student' ? 'true' : 'false' }}"
        :has-access="{{ 
            (auth()->user()->role === 'instructor' || 
            auth()->user()->role === 'admin' ||
            $lesson->is_free ||
            auth()->user()->isEnrolledIn($course->id)) 
            ? 'true' : 'false' 
        }}"
        :course-lessons="{{ $course->lessons->toJson() }}"
        :current-lesson-id="{{ $lesson->id }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-show>
</x-app-layout>