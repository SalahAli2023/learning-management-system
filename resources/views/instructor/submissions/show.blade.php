<x-app-layout>
    <instructor-submissions-show
        :course='@json($course)'
        :assignment='@json($assignment)'
        :submission='@json($submission)'
        submissions-route="{{ route('instructor.submissions.index', [$course->id, $assignment->id]) }}"
        grade-route="{{ route('instructor.submissions.grade', [$course->id, $assignment->id, $submission->id]) }}"
        csrf-token="{{ csrf_token() }}"
    ></instructor-submissions-show>
</x-app-layout>