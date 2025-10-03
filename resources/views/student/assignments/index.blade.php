<x-app-layout>
    <student-assignments-index
        :course='@json($course)'
        :assignments='@json($assignments)'
        course-route="{{ route('student.courses.show', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></student-assignments-index>
</x-app-layout>