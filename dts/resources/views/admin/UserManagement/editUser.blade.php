@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 760px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Edit User</h1>
        <div class="text-muted">Update account details, role, and department</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="ms-auto">
        @csrf
        <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>

    <div class="card-surface p-4">
      <form method="POST" action="{{ route('admin.userManagement.edit', $user) }}">
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
            <label class="form-label fw-semibold" for="roleID">Role</label>
            <select id="roleID" name="roleID" class="form-select" required>
              <option value="1" {{ $user->roleID == 1 ? 'selected' : '' }}>Admin</option>
              <option value="2" {{ $user->roleID == 2 ? 'selected' : '' }}>DocumentOwner</option>
              <option value="3" {{ $user->roleID == 3 ? 'selected' : '' }}>Staff</option>
              <option value="4" {{ $user->roleID == 4 ? 'selected' : '' }}>Auditor</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="departmentID">Department</label>
            <select id="departmentID" name="departmentID" class="form-select" required>
              <option value="1" {{ $user->departmentID == 1 ? 'selected' : '' }}>Admin</option>
              <option value="2" {{ $user->departmentID == 2 ? 'selected' : '' }}>Office of the Chancellor</option>
              <option value="3" {{ $user->departmentID == 3 ? 'selected' : '' }}>Campus Student Body Organization</option>
              <option value="4" {{ $user->departmentID == 4 ? 'selected' : '' }}>Student Affairs and Services</option>
              <option value="5" {{ $user->departmentID == 5 ? 'selected' : '' }}>COT - College of Technology</option>
              <option value="6" {{ $user->departmentID == 6 ? 'selected' : '' }}>CIT - College of Information Technology</option>
              <option value="7" {{ $user->departmentID == 7 ? 'selected' : '' }}>COM - College of Management</option>
              <option value="8" {{ $user->departmentID == 8 ? 'selected' : '' }}>COE - College of Engineering</option>
              <option value="9" {{ $user->departmentID == 9 ? 'selected' : '' }}>CE - College of Education</option>
              <option value="10" {{ $user->departmentID == 10 ? 'selected' : '' }}>ICJE - Institute of Crimial Justice Education</option>
              <option value="11" {{ $user->departmentID == 11 ? 'selected' : '' }}>CAS - College of Arts and Sciences</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="phoneNo">Phone Number</label>
            <input type="text" id="phoneNo" name="phoneNo" class="form-control" value="{{ $user->phoneNo }}">
          </div>
          <div class="col-md-6"></div>
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
          <button type="submit" class="btn btn-brand"><i class="bi bi-save"></i> Update User</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
