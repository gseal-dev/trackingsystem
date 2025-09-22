@extends('layouts.app')
@section('content')
<h2>Processed Documents Sent Back to Admin</h2>
<table border="1">
    <tr>
        <th>Document No</th>
        <th>Title</th>
        <th>Document Owner</th>
        <th>From Department</th>
        <th>Status</th>
        <th>Download</th>
    </tr>
    @foreach($documents as $doc)
    @php
        $lastHistory = $doc->histories()->where('currentDepartmentID', 1)->orderByDesc('created_at')->first();
        $fromDepartment = $lastHistory ? \App\Models\Department::find($lastHistory->prevDepartmentID) : null;
    @endphp
    <tr>
        <td>{{ $doc->documentNo }}</td>
        <td>{{ $doc->title }}</td>
        <td>
            {{ $doc->owner->username ?? 'Unknown' }} (ID: {{ $doc->ownerID }})
        </td>
        <td>
            {{ $fromDepartment ? $fromDepartment->depName : 'Unknown' }}
        </td>
        <td>
            {{ $doc->status->statusName ?? 'Unknown' }}
        </td>
        <td>
            @if($doc->filePath)
                <a href="{{ asset('storage/' . $doc->filePath) }}" download>
                    <button type="button">Download</button>
                </a>
            @else
                No file
            @endif
        </td>
    </tr>
    @endforeach
</table>
@endsection