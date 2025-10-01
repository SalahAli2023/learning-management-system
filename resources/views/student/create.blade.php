<x-app-layout>
<div id="app">
  <student-create
    store-route="{{ route('students.store') }}"
    index-route="{{ route('students.index') }}"
    csrf-token="{{ csrf_token() }}"
  ></student-create>
</div>
</x-app-layout>
