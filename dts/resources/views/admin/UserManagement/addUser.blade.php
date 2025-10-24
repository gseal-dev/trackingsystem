@extends('layouts.app')

@section('title', 'Add User')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 760px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Add User</h1>
        <div class="text-muted">Create an account and assign role and department</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="ms-auto">
        @csrf
        <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>

    <div class="card-surface p-4">
      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <form method="POST" action="{{ route('admin.userManagement.add') }}">
        @csrf
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
            <label class="form-label fw-semibold" for="roleID">Role</label>
            <select id="roleID" name="roleID" class="form-select" required>
              <option value="">Select Role</option>
              <option value="1">Admin</option>
              <option value="2">DocumentOwner</option>
              <option value="3">Staff</option>
              <option value="4">Auditor</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="departmentID">Department</label>
            <select id="departmentID" name="departmentID" class="form-select" required>
              <option value="">Select Department</option>
              <option value="1">Admin</option>
              <option value="2">Office of the Chancellor</option>
              <option value="3">Campus Student Body Organization</option>
              <option value="4">Student Affairs and Services</option>
              <option value="5">COT - College of Technology</option>
              <option value="6">CIT - College of Information Technology</option>
              <option value="7">COM - College of Management</option>
              <option value="8">COE - College of Engineering</option>
              <option value="9">CE - College of Education</option>
              <option value="10">ICJE - Institute of Criminal Justice Education</option>
              <option value="11">CAS - College of Arts and Sciences</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label fw-semibold" for="phoneNo">Phone Number</label>
            <input type="text" id="phoneNo" name="phoneNo" class="form-control">
          </div>
          <div class="col-md-6"></div>
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
          <button type="submit" class="btn btn-brand"><i class="bi bi-person-plus"></i> Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
