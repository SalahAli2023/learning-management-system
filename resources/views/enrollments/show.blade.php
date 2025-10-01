<x-app-layout>
    <enrollments-show 
        :enrollment="{{ $enrollment->load(['student','course'])->toJson() }}"
        index-route="{{ route('enrollments.index') }}"
        edit-route="{{ route('enrollments.edit', $enrollment->id) }}"
        csrf-token="{{ csrf_token() }}"
    ></enrollments-show>
</x-app-layout>

