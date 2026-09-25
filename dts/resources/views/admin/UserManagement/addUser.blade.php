@extends('layouts.app')
@section('title', 'Add User')
@php($roleLabel = 'Admin')
@push('head')
<style>
  .add-user-page {
    width: min(960px, 100%);
    padding: 2rem 0 3rem;
  }

  .add-user-heading {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 1rem;
    margin-bottom: 1.5rem;
  }

  .add-user-heading h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 800;
    letter-spacing: -0.04em;
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
    padding: 2rem;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 12px 30px rgba(15, 23, 42, 0.06);
    text-align: left;
  }

  .add-user-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 1.25rem 1rem;
  }

  .add-user-field label {
    display: block;
    margin-bottom: 0.45rem;
    color: #334155;
    font-size: 0.875rem;
    font-weight: 700;
  }

  .add-user-field .form-control {
    min-height: 44px;
    border-radius: 8px !important;
    border-color: #dbe2ea;
  }

  .add-user-submit {
    width: 100%;
    min-height: 46px;
    margin-top: 1.5rem;
    border-radius: 8px;
  }

  @media (max-width: 640px) {
    .add-user-heading {
      align-items: flex-start;
      flex-direction: column;
    }

    .add-user-card {
      padding: 1.25rem;
    }

    .add-user-grid {
      grid-template-columns: 1fr;
    }
  }
</style>
@endpush
@section('content')
<div class="add-user-page">
  <div class="add-user-heading">
    <h1>Add {{ $roleLabel }}</h1>
    <a href="{{ route('dashboard') }}" class="btn add-user-back">
      <i class="bi bi-arrow-left"></i> Back to Admin Dashboard
    </a>
  </div>

  <div class="add-user-card">
    @if($errors->any())
      <div class="alert alert-danger mb-4">
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
            @foreach($roles as $role)
              <option value="{{ $role->roleID }}" @selected(old('roleID', 1) == $role->roleID)>{{ $role->roleName }}</option>
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
      <button type="submit" class="btn btn-brand add-user-submit">
        <i class="bi bi-person-plus"></i> Add {{ $roleLabel }}
      </button>
    </form>
  </div>
</div>
@endsection