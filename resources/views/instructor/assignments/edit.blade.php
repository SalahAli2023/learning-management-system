<x-app-layout>
    <instructor-assignments-edit
        :course='@json($course)'
        :assignment='@json($assignment)'
        update-route="{{ route('instructor.assignments.update', [$course->id, $assignment->id]) }}"
        show-route="{{ route('instructor.assignments.show', [$course->id, $assignment->id]) }}"
        csrf-token="{{ csrf_token() }}"
    ></instructor-assignments-edit>
</x-app-layout>