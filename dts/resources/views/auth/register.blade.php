@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="page-container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
  <div class="container" style="max-width: 640px;">
    <div class="card-surface p-4">
      <h2 class="h4 text-center mb-1">Register as Document Owner 🗂️</h2>
      <p class="text-muted text-center mb-4">Create an account to manage and track your documents.</p>

      <form method="POST" action="{{ route('register') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label for="username" class="form-label fw-semibold">Username</label>
            <input type="text" name="username" id="username" class="form-control" required placeholder="Enter your username">
          </div>
          <div class="col-md-6">
            <label for="email" class="form-label fw-semibold">Email</label>
            <input type="email" name="email" id="email" class="form-control" required placeholder="Enter your email address">
          </div>
          <div class="col-md-4">
            <label for="firstName" class="form-label fw-semibold">First Name</label>
            <input type="text" name="firstName" id="firstName" class="form-control" required>
          </div>
          <div class="col-md-4">
            <label for="middleName" class="form-label fw-semibold">Middle Name</label>
            <input type="text" name="middleName" id="middleName" class="form-control">
          </div>
          <div class="col-md-4">
            <label for="lastName" class="form-label fw-semibold">Last Name</label>
            <input type="text" name="lastName" id="lastName" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label for="departmentID" class="form-label fw-semibold">Department</label>
            <select name="departmentID" id="departmentID" class="form-select" required>
              <option value="">Select Department</option>
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
            <label for="phoneNo" class="form-label fw-semibold">Phone Number</label>
            <input type="text" name="phoneNo" id="phoneNo" class="form-control" placeholder="Enter your phone number">
          </div>
          <div class="col-md-6">
            <label for="password" class="form-label fw-semibold">Password</label>
            <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your password">
          </div>
          <div class="col-md-6">
            <label for="password_confirmation" class="form-label fw-semibold">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required placeholder="Confirm your password">
          </div>
        </div>
        <button type="submit" class="btn btn-brand w-100 mt-3">Register</button>
      </form>

      <div class="text-center mt-3">
        <a href="{{ route('login') }}" class="text-decoration-none">Already have an account? Login</a>
      </div>
    </div>
  </div>
</div>
@endsection