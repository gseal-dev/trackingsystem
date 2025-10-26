@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">User Management</h1>
        <div class="text-muted">Manage users across roles and departments</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('admin.userManagement.addForm') }}" class="btn btn-brand"><i class="bi bi-person-plus"></i> Add User</a>
          <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success card-surface border-0">{{ session('success') }}</div>
    @endif

    <div class="row g-3">
      <div class="col-12">
        <div class="card-surface p-3">
          <h5 class="mb-3">Admins</h5>
          @forelse($admins as $user)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <div>
                <strong>{{ $user->username }}</strong>
                <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
                <small class="text-muted">{{ $user->email }}</small>
              </div>
              <div class="d-flex gap-2">
                <span class="badge bg-primary">Admin</span>
                <a href="{{ route('admin.userManagement.editForm', $user) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                <form action="{{ route('admin.userManagement.delete', $user) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
              </div>
            </div>
          @empty
            <div class="alert alert-info mb-0">No admins found.</div>
          @endforelse
        </div>
      </div>

      <div class="col-12">
        <div class="card-surface p-3">
          <h5 class="mb-3">Document Owners</h5>
          @forelse($owners as $user)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <div>
                <strong>{{ $user->username }}</strong>
                <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
                <small class="text-muted">{{ $user->email }}</small>
              </div>
              <div class="d-flex gap-2">
                <span class="badge bg-success">Owner</span>
                <a href="{{ route('admin.userManagement.editForm', $user) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                <form action="{{ route('admin.userManagement.delete', $user) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
              </div>
            </div>
          @empty
            <div class="alert alert-info mb-0">No owners found.</div>
          @endforelse
        </div>
      </div>

      <div class="col-12">
        <div class="card-surface p-3">
          <h5 class="mb-3">Staff</h5>
          @forelse($staffs as $user)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <div>
                <strong>{{ $user->username }}</strong>
                <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
                <small class="text-muted">{{ $user->email }}</small>
              </div>
              <div class="d-flex gap-2">
                <span class="badge bg-warning text-dark">Staff</span>
                <a href="{{ route('admin.userManagement.editForm', $user) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                <form action="{{ route('admin.userManagement.delete', $user) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
              </div>
            </div>
          @empty
            <div class="alert alert-info mb-0">No staff found.</div>
          @endforelse
        </div>
      </div>

      <div class="col-12">
        <div class="card-surface p-3">
          <h5 class="mb-3">Auditors</h5>
          @forelse($auditors as $user)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <div>
                <strong>{{ $user->username }}</strong>
                <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
                <small class="text-muted">{{ $user->email }}</small>
              </div>
              <div class="d-flex gap-2">
                <span class="badge bg-info text-dark">Auditor</span>
                <a href="{{ route('admin.userManagement.editForm', $user) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                <form action="{{ route('admin.userManagement.delete', $user) }}" method="POST" class="d-inline">
                  @csrf
                  <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
              </div>
            </div>
          @empty
            <div class="alert alert-info mb-0">No auditors found.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
