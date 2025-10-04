<x-app-layout>
    <student-submissions-show
        :course='@json($course)'
        :assignment='@json($assignment)'
        :submission='@json($submission)'
        assignments-route="{{ route('student.assignments.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></student-submissions-show>
</x-app-layout>