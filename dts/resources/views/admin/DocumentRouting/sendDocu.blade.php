@extends('layouts.app')
@section('content')
<h2>Send Document: {{ $document->title }}</h2>
<form method="POST" action="{{ route('admin.sendDocument', $document->documentId) }}">
    @csrf
    <label for="departmentID">Select Department</label>
    <select id="departmentID" name="departmentID" required>
        @foreach($departments as $dep)
            <option value="{{ $dep->depID }}">{{ $dep->depName }}</option>
        @endforeach
    </select>
    <button type="submit">Send</button>
</form>
@endsection