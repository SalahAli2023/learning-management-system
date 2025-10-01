<x-app-layout>
    <lessons-index
        :lessons="{{ json_encode(['data' => $lessons]) }}"
        create-route="{{ route('instructor.courses.lessons.create', $course) }}"
        course-route="{{ route('instructor.courses.show', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
        :course="{{ json_encode($course) }}"
    ></lessons-index>
</x-app-layout>
