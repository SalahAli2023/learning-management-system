<x-app-layout>
    <courses-index
        :courses='@json($courses)'
        :user='@json(auth()->user())'
        create-route="{{ route('instructor.courses.create') }}"
    ></courses-index>
</x-app-layout>
