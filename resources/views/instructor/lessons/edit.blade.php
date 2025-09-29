{{-- <x-app-layout>
    <lessons-edit
        :course='@json($course)'
        :lesson='@json($lesson)'
        update-route="{{ route('instructor.courses.lessons.update', [$course->id, $lesson->id]) }}"
        index-route="{{ route('instructor.courses.lessons.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-edit>
</x-app-layout> --}}

{{-- <x-app-layout>
    <lessons-edit 
        :lesson="{{ $lesson->toJson() }}"
        update-route="{{ route('instructor.lessons.update', [$course->id, $lesson->id]) }}"
        show-route="{{ route('instructor.lessons.show', [$course->id, $lesson->id]) }}"
        index-route="{{ route('instructor.lessons.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-edit>
</x-app-layout> --}}

{{-- <x-app-layout>
    <lessons-edit 
        :lesson="{{ $lesson->toJson() }}"
        course-id="{{ $course->id }}"
        update-route="{{ route('instructor.courses.lessons.update', [$course->id, $lesson->id]) }}"
        show-route="{{ route('instructor.courses.lessons.show', [$course->id, $lesson->id]) }}"
        index-route="{{ route('instructor.courses.lessons.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-edit>
</x-app-layout> --}}

{{-- <x-app-layout>
    <lessons-edit 
        :lesson='@json($lesson)'
        :course-id="{{ $course->id }}"
        update-route="{{ route('instructor.courses.lessons.update', [$course->id, $lesson->id]) }}"
        show-route="{{ route('instructor.courses.lessons.show', [$course->id, $lesson->id]) }}"
        index-route="{{ route('instructor.courses.lessons.index', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-edit>
</x-app-layout> --}}

{{-- <x-app-layout>
    <lessons-edit
        :lesson='@json($lesson)'
        update-route="{{ route('instructor.courses.lessons.update', [$course->id, $lesson->id]) }}"
        index-route="'{{ route('instructor.courses.lessons.index', $course->id) }}'"
        csrf-token="{{ csrf_token() }}"
    ></lessons-edit>
</x-app-layout> --}}
<x-app-layout>
    <lessons-edit 
        :lesson="{{ json_encode($lesson) }}"
        update-route="{{ route('instructor.courses.lessons.update', [$course, $lesson]) }}"
        index-route="{{ route('instructor.courses.lessons.index', $course) }}"
        csrf-token="{{ csrf_token() }}"
    ></lessons-edit>
</x-app-layout> 