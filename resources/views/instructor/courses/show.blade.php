<x-app-layout>
        <courses-show 
            :course='@json($course)' 
            index-route="{{ route('instructor.courses.index') }}"
            edit-route="{{ route('instructor.courses.edit', $course->id) }}"
        ></courses-show>
</x-app-layout>