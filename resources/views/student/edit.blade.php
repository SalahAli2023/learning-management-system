@extends('layouts.app')

@section('content')
<div id="app">
  <student-edit
    :student='@json($student)'
    update-route="{{ route('students.update', $student->id) }}"
    show-route="{{ route('students.show', $student->id) }}"
    index-route="{{ route('students.index') }}"
    csrf-token="{{ csrf_token() }}"
  ></student-edit>
</div>
@endsection
