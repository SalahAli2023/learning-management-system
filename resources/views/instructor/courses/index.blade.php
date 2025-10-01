{{-- <x-app-layout>
    <courses-index
        :courses='@json($courses)'
        :user='@json(auth()->user())'
        create-route="{{ route('instructor.courses.create') }}"
    ></courses-index>
</x-app-layout> --}}

<x-app-layout>
    <courses-index
        :courses='@json($courses)'
        :user-role='@json(auth()->user()->role)'
        :user='@json(auth()->user())'
        :enrollments='@json($enrollments ?? [])'
        create-route="{{ route('instructor.courses.create') }}"
        csrf-token="{{ csrf_token() }}"
    ></courses-index>
</x-app-layout>
