@extends('layouts.app')
@section('title', 'Add User')
@php($roleLabel = 'Admin')
@push('head')
<style>
  .add-user-modal-wrap {
    width: min(960px, 100%);
    padding: 2rem 0 3rem;
  }

  .add-user-modal {
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    background: #ffffff;
    color: #1e293b;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25);
  }

  .add-user-modal-header {
    background: #fff;
    border-bottom: 1px solid #cbd5e1;
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }

  .add-user-modal-header i.bi-file-earmark-person,
  .add-user-modal-header .modal-doc-icon {
    color: #ea3a14;
    font-size: 1.15rem;
    margin-right: 0.6rem;
  }

  .add-user-modal-title {
    margin: 0;
    font-size: 1.05rem;
    font-weight: 700;
    color: #000;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }

  .add-user-modal-body {
    padding: 1.5rem;
  }

  .add-user-back {
    border-color: #cbd5e1;
    color: #475569;
    background: #ffffff;
  }

  .add-user-back:hover {
    border-color: #111111;
    color: #111111;
    background: #ffffff;
  }

  .add-user-card {
    max-width: none;
    padding: 0;
    border: none;
    border-radius: 0;
    background: transparent;
    box-shadow: none;
    text-align: left;
  }

  .add-user-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1rem;
  }

  .add-user-field {
    border-bottom: 1px solid #cbd5e1;
  }

  .add-user-field label {
    display: block;
    margin-bottom: 0.45rem;
    color: #64748b;
    font-size: 0.875rem;
    font-weight: 600;
  }

  .add-user-field .form-control {
    min-height: 44px;
    border: 1px solid #cbd5e1 !important;
    border-radius: 6px !important;
    background: #fff !important;
    color: #000 !important;
    padding: 0.5rem 0.75rem;
    font-size: 0.925rem;
  }

  .add-user-field .form-control:focus {
    border-color: #000 !important;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.12) !important;
    outline: none !important;
    background: #fff !important;
  }

  .add-user-submit {
    width: 100%;
    min-height: 46px;
    margin-top: 1.5rem;
    border-radius: 6px;
    background-color: #000;
    color: #fff;
    font-weight: 600;
    border: none;
  }

  .add-user-submit:hover {
    background-color: #000a33;
    color: #fff;
  }

  @media (max-width: 640px) {
    .add-user-heading { flex-direction: column; align-items: flex-start; gap: 0.5rem; }
    .add-user-card { padding: 1rem; }
    .add-user-grid { grid-template-columns: 1fr; }
  }
</style>
@endpush
@section('content')
<div class="add-user-modal-wrap">
  <div class="add-user-modal">
    <div class="add-user-modal-header">
      <h5 class="add-user-modal-title"><i class="bi bi-file-earmark-person-fill modal-doc-icon"></i> Add {{ $roleLabel }}</h5>
      <a href="{{ route('dashboard') }}" class="btn add-user-back"><i class="bi bi-arrow-left me-1"></i> Back to Admin Dashboard</a>
    </div>

    <div class="add-user-modal-body">
      <div class="add-user-card">
        @if($errors->any())
          <div class="alert alert-danger mb-4 rounded-4" style="border: 1px solid #fecaca; background: #fffafa;">
            @foreach($errors->all() as $error)
              <div>{{ $error }}</div>
            @endforeach
          </div>
        @endif

        <form method="POST" action="{{ route('admin.userManagement.add', ['type' => $type]) }}">
          @csrf
          <div class="add-user-grid">
            <div class="add-user-field">
              <label for="username">Username</label>
              <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" required autofocus>
            </div>
            <div class="add-user-field">
              <label for="email">Email</label>
              <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>
            <div class="add-user-field">
              <label for="firstName">First Name</label>
              <input type="text" id="firstName" name="firstName" class="form-control" value="{{ old('firstName') }}" required>
            </div>
            <div class="add-user-field">
              <label for="lastName">Last Name</label>
              <input type="text" id="lastName" name="lastName" class="form-control" value="{{ old('lastName') }}" required>
            </div>
            <div class="add-user-field">
              <label for="roleID">Role</label>
              <select id="roleID" name="roleID" class="form-control" required>
                <option value="" disabled {{ old('roleID') ? '' : 'selected' }}>Select Role</option>
                @foreach($roles as $role)
                  <option value="{{ $role->roleID }}" @selected(old('roleID') == $role->roleID)>{{ $role->roleName }}</option>
                @endforeach
              </select>
            </div>
            <div class="add-user-field">
              <label for="password">Password</label>
              <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="add-user-field">
              <label for="password_confirmation">Confirm Password</label>
              <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
            </div>
          </div>
          <button type="submit" class="btn add-user-submit">
            <i class="bi bi-person-plus me-1"></i> Add {{ $roleLabel }}
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
