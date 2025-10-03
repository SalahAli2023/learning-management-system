<x-app-layout>
    <student-assignments-show
        :course='@json($course)'
        :assignment='@json($assignment)'
        :submission='@json($submission)'
        submit-route="{{ route('student.assignments.submit', [$course->id, $assignment->id]) }}"
        assignments-route="{{ route('student.assignments.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></student-assignments-show>
</x-app-layout>