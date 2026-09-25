@extends('layouts.app')
@section('title', 'Edit User')
@section('content')
<div class="login-card" style="max-width: 580px;">
  <h1 class="login-title">Edit User</h1>
  <p class="login-subtitle">Update the user account details</p>

  @if($errors->any())
    <div class="alert alert-danger text-start py-2 px-3 mb-4 rounded-4" style="font-size: 0.85rem;">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('admin.userManagement.edit', ['type' => $type, 'user' => $user]) }}">
    @csrf

    <div class="input-group-custom">
      <div class="icon-wrapper"><i class="bi bi-person"></i></div>
      <input type="text" name="username" id="username" class="input-pill" placeholder="username" value="{{ old('username', $user->username) }}" required autofocus>
    </div>

    <div class="input-group-custom">
      <div class="icon-wrapper"><i class="bi bi-envelope"></i></div>
      <input type="email" name="email" id="email" class="input-pill" placeholder="email address" value="{{ old('email', $user->email) }}" required>
    </div>

    <div class="row g-2 mb-3 ms-md-4 ps-md-2">
      <div class="col-md-6">
        <input type="text" name="firstName" id="firstName" class="input-pill" placeholder="first name" value="{{ old('firstName', $user->firstName) }}" required>
      </div>
      <div class="col-md-6">
        <input type="text" name="lastName" id="lastName" class="input-pill" placeholder="last name" value="{{ old('lastName', $user->lastName) }}" required>
      </div>
    </div>

    <div class="input-group-custom">
      <div class="icon-wrapper"><i class="bi bi-person-badge"></i></div>
      <select name="roleID" id="roleID" class="input-pill" required>
        @foreach($roles as $role)
          <option value="{{ $role->roleID }}" @selected(old('roleID', $user->roleID) == $role->roleID)>{{ $role->roleName }}</option>
        @endforeach
      </select>
    </div>

    <div class="input-group-custom">
      <div class="icon-wrapper"><i class="bi bi-key"></i></div>
      <input type="password" name="password" id="password" class="input-pill" placeholder="new password (optional)">
    </div>

    <div class="input-group-custom">
      <div class="icon-wrapper"><i class="bi bi-shield-lock"></i></div>
      <input type="password" name="password_confirmation" id="password_confirmation" class="input-pill" placeholder="confirm new password">
    </div>

    <div class="mt-4 d-flex flex-column gap-2">
      <button type="submit" class="btn-login-submit">Save Changes</button>
      <a href="{{ route('dashboard') }}" class="footer-link">Back to Dashboard</a>
    </div>
  </form>
</div>
@endsection
