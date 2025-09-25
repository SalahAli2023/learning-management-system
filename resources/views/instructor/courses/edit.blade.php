<x-app-layout>
    <courses-edit
        :course='@json($course)'
        update-route="{{ route('instructor.courses.update', $course->id) }}"
        show-route="{{ route('instructor.courses.show', $course->id) }}"
        index-route="{{ route('instructor.courses.index') }}"
        csrf-token="{{ csrf_token() }}"
    ></courses-edit>
</x-app-layout>

