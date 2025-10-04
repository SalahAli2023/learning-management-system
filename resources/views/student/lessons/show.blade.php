<x-app-layout>
    <students-lesson-show
        :lesson="{{ $lesson->toJson() }}"
        :course="{{ $course->toJson() }}"
        index-route="{{ route('student.courses.show', $course->id) }}"
        enroll-route="{{ route('student.courses.enroll', $course->id) }}"
        :is-instructor="false"
        :is-student="true"
        :has-access="{{ 
            ($lesson->is_free || auth()->user()->isEnrolledIn($course->id)) 
            ? 'true' : 'false' 
        }}"
        :course-lessons="{{ $course->lessons->toJson() }}"
        :current-lesson-id="{{ $lesson->id }}"
        csrf-token="{{ csrf_token() }}"
    ></students-lesson-show>
</x-app-layout>