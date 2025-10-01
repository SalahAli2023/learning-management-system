<x-app-layout>
    <enrollments-create 
        :students="{{ $students->toJson() }}"
        :courses="{{ $courses->toJson() }}"
        store-route="{{ route('enrollments.store') }}"
        index-route="{{ route('enrollments.index') }}"
        csrf-token="{{ csrf_token() }}"
    ></enrollments-create>
</x-app-layout>
