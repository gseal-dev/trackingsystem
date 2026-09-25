@extends('layouts.app')
@section('title', 'Document Owners')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 900px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Document Owners</h1>
        <div class="text-muted">Manage document owners</div>
      </div>
      <a href="{{ route('admin.userManagement.addForm', ['type' => 'owners']) }}" class="btn btn-brand"><i class="bi bi-person-plus"></i> Add Owner</a>
    </div>
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary mb-3">
      <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
    @if(session('success'))
      <div class="alert alert-success card-surface border-0">{{ session('success') }}</div>
    @endif
    <div class="card-surface p-3">
      @forelse($users as $user)
        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
          <div>
            <strong>{{ $user->username }}</strong>
            <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
            <small class="text-muted">{{ $user->email }}</small>
          </div>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.userManagement.editForm', ['type' => 'owners', 'user' => $user]) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
            <form action="{{ route('admin.userManagement.delete', ['type' => 'owners', 'user' => $user]) }}" method="POST" class="d-inline"
                onsubmit="return confirm('Are you sure you want to delete this user?');">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
            </form>
          </div>
        </div>
      @empty
        <div class="alert alert-info mb-0">No owners found.</div>
      @endforelse
    </div>
  </div>
</div>
@endsection