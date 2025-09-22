@extends('layouts.app')
@section('content')
<h2>Admin Dashboard</h2>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
<div style="margin-top: 16px;">
    <a href="{{ route('admin.userManagement') }}">
        <button type="button">User Management</button>
    </a>
</div>
<div style="margin-top: 16px;">
    <a href="{{ route('admin.documentRegistration') }}">
        <button type="button">Document Registration</button>
    </a>
</div>
<div style="margin-top: 16px;">
    <a href="{{ route('admin.sendDocumentList') }}">
        <button type="button">Send Documents</button>
    </a>
</div>
<div style="margin-top: 16px;">
    <a href="{{ route('admin.processedDocuments') }}">
        <button type="button">Processed Documents</button>
    </a>
</div>
@endsection