<x-app-layout>
    <students-enrollments-index
        :enrollments="{{ json_encode($enrollments)}}"
        :course="{{ json_encode($course) }}"
        csrf-token="{{ csrf_token() }}"
    ></students-enrollments-index>
</x-app-layout>