<x-app-layout>
    <students-show
        :course='@json($course)'
        :enrollment='@json($enrollment)'
        :is-enrolled='@json($isEnrolled)'
        enroll-route="{{ route('student.courses.enroll', $course->id) }}"
        progress-route="{{ route('student.courses.progress', $course->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></students-show>
</x-app-layout>