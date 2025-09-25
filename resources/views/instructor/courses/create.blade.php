<x-app-layout>
    <courses-create
        store-route="{{ route('instructor.courses.store') }}"
        index-route="{{ route('instructor.courses.index') }}"
        csrf-token="{{ csrf_token() }}"
    ></courses-create>
</x-app-layout>