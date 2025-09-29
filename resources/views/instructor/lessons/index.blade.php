<x-app-layout>
    <lessons-index
        :lessons="{{ json_encode(['data' => $lessons]) }}"
        create-route="{{ route('instructor.courses.lessons.create', $course) }}"
        csrf-token="{{ csrf_token() }}"
        :course="{{ json_encode($course) }}"
    ></lessons-index>
</x-app-layout>
