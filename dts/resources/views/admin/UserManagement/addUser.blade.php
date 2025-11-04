@extends('layouts.app')
@section('title', 'Add User')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 760px;">
    <div class="page-header mb-3">
      <h1 class="h3 mb-1">Add {{ ucfirst($type) }}</h1>
      <a href="{{ route('admin.userManagement') }}" class="btn btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to User Management
      </a>
    </div>
    <div class="card-surface p-4">
      <form method="POST" action="{{ route('admin.userManagement.add', ['type' => $type]) }}">
        @csrf
        <input type="hidden" name="roleID" value="{{ \App\Models\Role::where('roleName', ucfirst($type))->value('roleID') }}">
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="username">Username</label>
            <input type="text" id="username" name="username" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="email">Email</label>
            <input type="email" id="email" name="email" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold" for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold" for="middleName">Middle Name</label>
            <input type="text" id="middleName" name="middleName" class="form-control">
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold" for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="departmentID">Department</label>
            <select id="departmentID" name="departmentID" class="form-select" required>
              <option value="">Select Department</option>
              @foreach($departments as $dep)
                <option value="{{ $dep->depID }}">{{ $dep->depName }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="phoneNo">Phone Number</label>
            <input type="text" id="phoneNo" name="phoneNo" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="password">Password</label>
            <input type="password" id="password" name="password" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-brand"><i class="bi bi-person-plus"></i> Add {{ ucfirst($type) }}</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection