@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 760px;">
    <div class="page-header mb-3">
      <h1 class="h3 mb-1">Edit {{ ucfirst($type) }}</h1>
      <a href="{{ route('admin.userManagement') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to User Management
      </a>
    </div>
    <div class="card-surface p-4">
      <form method="POST" action="{{ route('admin.userManagement.edit', ['type' => $type, 'user' => $user]) }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" value="{{ $user->username }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" value="{{ $user->email }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold" for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" class="form-control" value="{{ $user->firstName }}" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold" for="middleName">Middle Name</label>
            <input type="text" id="middleName" name="middleName" class="form-control" value="{{ $user->middleName }}">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold" for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" class="form-control" value="{{ $user->lastName }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="departmentID">Department</label>
            <select id="departmentID" name="departmentID" class="form-select" required>
              @foreach($departments as $dep)
                <option value="{{ $dep->depID }}" {{ $user->departmentID == $dep->depID ? 'selected' : '' }}>{{ $dep->depName }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="phoneNo">Phone Number</label>
            <input type="text" id="phoneNo" name="phoneNo" class="form-control" value="{{ $user->phoneNo }}">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="password">New Password (optional)</label>
            <input type="password" id="password" name="password" class="form-control" placeholder="New Password">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm New Password">
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-brand"><i class="bi bi-save"></i> Update {{ ucfirst($type) }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection