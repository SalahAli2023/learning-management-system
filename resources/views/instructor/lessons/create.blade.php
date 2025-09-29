<x-app-layout>
    <lessons-create 
        store-route="{{ route('instructor.courses.lessons.store', $course->id) }}"
        index-route="{{ route('instructor.courses.lessons.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
        course-id="{{ $course->id }}"
    ></lessons-create>
</x-app-layout>
