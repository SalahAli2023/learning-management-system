<x-app-layout>
    <lessons-show
        :course="{{ $course->toJson() }}"
        :lesson="{{ $lesson->toJson() }}"
        index-route="{{ route('instructor.courses.lessons.index', $course->id) }}"
        edit-route="{{ route('instructor.courses.lessons.edit', [$course->id, $lesson->id]) }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-show>
</x-app-layout>