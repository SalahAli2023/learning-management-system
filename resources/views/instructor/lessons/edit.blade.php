<x-app-layout>
    <lessons-edit 
        :lesson="{{ json_encode($lesson) }}"
        update-route="{{ route('instructor.courses.lessons.update', [$course, $lesson]) }}"
        index-route="{{ route('instructor.courses.lessons.index', $course) }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-edit>
</x-app-layout> 