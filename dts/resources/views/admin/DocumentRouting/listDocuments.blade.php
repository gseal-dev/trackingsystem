@extends('layouts.app')
@section('content')
<h2>Send Documents</h2>
@if(session('success'))
    <div>{{ session('success') }}</div>
@endif
<table border="1">
    <tr>
        <th>Document No</th>
        <th>Title</th>
        <th>Document Owner</th>
        <th>Action</th>
    </tr>
    @foreach($documents as $doc)
    <tr>
        <td>{{ $doc->documentNo }}</td>
        <td>{{ $doc->title }}</td>
        <td>
            {{ $doc->owner->username ?? 'Unknown' }} (ID: {{ $doc->ownerID }})
        </td>
        <td>
            <a href="{{ route('admin.sendDocumentForm', $doc->documentId) }}">
                <button type="button">Send</button>
            </a>
        </td>
    </tr>
    @endforeach
</table>
@endsection