<x-app-layout>
    <instructor-submissions-index
        :course='@json($course)'
        :assignment='@json($assignment)'
        :submissions='@json($submissions)'
        :stats='@json($stats)'
        assignments-route="{{ route('instructor.assignments.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></instructor-submissions-index>
</x-app-layout>