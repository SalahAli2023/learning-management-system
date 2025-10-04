<x-app-layout>
    <instructor-assignments-show
        :course='@json($course)'
        :assignment='@json($assignment)'
        :submissions='@json($submissions)'
        submissions-route="{{ route('instructor.submissions.index', [$course->id, $assignment->id]) }}"
        edit-route="{{ route('instructor.assignments.edit', [$course->id, $assignment->id]) }}"
        assignments-route="{{ route('instructor.assignments.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></instructor-assignments-show>
</x-app-layout>