@extends('layouts.app')
@section('content')
<h2>Staff Dashboard</h2>
@if(session('success'))
    <div>{{ session('success') }}</div>
@endif
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
@foreach($documents as $doc)
    <div style="border:1px solid #ccc; margin-bottom:16px; padding:8px;">
        <strong>{{ $doc->title }}</strong> ({{ $doc->documentNo }})<br>
        Status: {{ $doc->status->statusName ?? 'Unknown' }}<br>
        <a href="{{ asset('storage/' . $doc->filePath) }}" download>Download</a>
        <form method="POST" action="{{ route('staff.processAndRouteDocument', $doc->documentId) }}" enctype="multipart/form-data" style="margin-top:8px;">
            @csrf
            <label>Reupload Processed Document</label>
            <input type="file" name="file" required>
            <select name="statusID" required>
                <option value="2">Approved</option>
                <option value="3">Rejected</option>
                <option value="4">In Review</option>
            </select>
            <label>Send To:</label>
            <select name="departmentID" required>
                <option value="1">Admin</option>
                @foreach($departments as $dep)
                    <option value="{{ $dep->depID }}">{{ $dep->depName }}</option>
                @endforeach
            </select>
            <button type="submit">Send & Upload</button>
        </form>
    </div>
@endforeach
@endsection