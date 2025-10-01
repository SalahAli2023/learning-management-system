<x-app-layout>
    <enrollments-edit 
        :enrollment="{{ $enrollment->load(['student','course'])->toJson() }}"
        update-route="{{ route('enrollments.update', $enrollment->id) }}"
        index-route="{{ route('enrollments.index') }}"
        csrf-token="{{ csrf_token() }}"
    ></enrollments-edit>
</x-app-layout>
