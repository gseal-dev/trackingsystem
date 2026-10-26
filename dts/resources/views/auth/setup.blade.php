@extends('layouts.app')
@section('title', 'Create Admin Account')

@section('content')
<div class="login-right-panel d-flex align-items-center justify-content-center p-4 p-md-5" style="width: 100%; min-height: 100vh; background-color: var(--card-bg); overflow-y: auto;">
  <div class="login-card w-100" style="max-width: 520px; margin: auto; box-shadow: none !important; padding: 2.5rem 2rem; text-align: left;">
    <div class="mb-4 text-center">
      <img src="{{ asset('images/dpwh-logo.png') }}" alt="DPWH Logo" style="height: 48px; width: 48px; object-fit: contain; background: white; border-radius: 50%; padding: 4px;">
      <div class="mt-3">
        <h2 class="fw-extrabold text-uppercase" style="font-size: 1.5rem; color: var(--brand-dark); letter-spacing: -0.02em; font-weight: 800; margin-bottom: 0.3rem;">Create Admin Account</h2>
        <p class="text-muted" style="font-size: 0.9rem; margin: 0;">No accounts exist yet. Set up the first administrator.</p>
      </div>
    </div>

    @if($errors->any())
      <div class="alert alert-danger text-start py-2 px-3 mb-4 rounded-4" style="font-size: 0.85rem;">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('setup.store') }}">
      @csrf

      {{-- Username Field --}}
      <div class="input-group-custom mb-3">
        <div class="icon-wrapper">
          <i class="bi bi-person"></i>
        </div>
        <input type="text" name="username" id="username" class="input-pill" placeholder="Username" value="{{ old('username') }}" required autofocus>
      </div>

      {{-- Email Field --}}
      <div class="input-group-custom mb-3">
        <div class="icon-wrapper">
          <i class="bi bi-envelope"></i>
        </div>
        <input type="email" name="email" id="email" class="input-pill" placeholder="Email Address" value="{{ old('email') }}" required>
      </div>

      {{-- First Name --}}
      <div class="input-group-custom mb-3">
        <div class="icon-wrapper">
          <i class="bi bi-person-fill"></i>
        </div>
        <input type="text" name="firstName" id="firstName" class="input-pill" placeholder="First Name" value="{{ old('firstName') }}" required>
      </div>

      {{-- Last Name --}}
      <div class="input-group-custom mb-3">
        <div class="icon-wrapper">
          <i class="bi bi-person-fill"></i>
        </div>
        <input type="text" name="lastName" id="lastName" class="input-pill" placeholder="Last Name" value="{{ old('lastName') }}" required>
      </div>

      {{-- Password Field --}}
      <div class="input-group-custom position-relative mb-3">
        <div class="icon-wrapper">
          <i class="bi bi-key"></i>
        </div>
        <div class="w-100 position-relative">
          <input type="password" name="password" id="password" class="input-pill pe-5" placeholder="Password" required>
          <button type="button" id="togglePassword" class="btn position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent text-muted p-0" style="z-index: 10;">
            <i class="bi bi-eye" id="toggleIcon"></i>
          </button>
        </div>
      </div>

      {{-- Confirm Password --}}
      <div class="input-group-custom position-relative mb-4">
        <div class="icon-wrapper">
          <i class="bi bi-key"></i>
        </div>
        <div class="w-100 position-relative">
          <input type="password" name="password_confirmation" id="password_confirmation" class="input-pill pe-5" placeholder="Confirm Password" required>
        </div>
      </div>

      {{-- Create Account Submit Button --}}
      <div class="mt-4">
        <button type="submit" class="btn-login-submit py-3 fw-bold shadow-sm">Create Account</button>
      </div>
    </form>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password');
    const toggleIcon = document.querySelector('#toggleIcon');

    togglePassword.addEventListener('click', function () {
      const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
      password.setAttribute('type', type);

      toggleIcon.classList.toggle('bi-eye');
      toggleIcon.classList.toggle('bi-eye-slash');
    });
  });
</script>
@endpush
