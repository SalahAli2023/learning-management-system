<x-app-layout>
    <instructor-assignments-create
        :course='@json($course)'
        store-route="{{ route('instructor.assignments.store', $course->id) }}"
        index-route="{{ route('instructor.assignments.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></instructor-assignments-create>
</x-app-layout>