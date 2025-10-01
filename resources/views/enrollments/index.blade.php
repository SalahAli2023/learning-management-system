<x-app-layout>
    <enrollments-index
        :enrollments="{{ json_encode($enrollments)}}"
        :course="{{ json_encode($course) }}"
        course-route="{{ route('instructor.courses.show', $course) }}"
        csrf-token="{{ csrf_token() }}"
    ></enrollments-index>
</x-app-layout>