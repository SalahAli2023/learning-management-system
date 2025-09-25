<x-app-layout>
    <div id="app">
        <courses-show 
            :course='@json($course)' 
            index-route="{{ route('instructor.courses.index') }}"
        ></courses-show>
    </div>
</x-app-layout>