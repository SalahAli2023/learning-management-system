<x-app-layout>
    <instructor-assignments-index
        :course='@json($course)'
        :assignments='@json($assignments)'
        create-route="{{ route('instructor.assignments.create', $course->id) }}"
        course-route="{{ route('instructor.courses.show', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></instructor-assignments-index>
</x-app-layout>