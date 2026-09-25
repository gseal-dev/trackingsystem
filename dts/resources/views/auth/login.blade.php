@extends('layouts.app')
@section('title', 'Login')

@section('content')
<div class="login-card">
  <h1 class="login-title">Log in</h1>
  <p class="login-subtitle">Log in to your account to continue</p>

  @if($errors->any())
    <div class="alert alert-danger text-start py-2 px-3 mb-4 rounded-4" style="font-size: 0.85rem;">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('login') }}">
    @csrf

    {{-- Username/Email Field with Left Icon --}}
    <div class="input-group-custom">
      <div class="icon-wrapper">
        <i class="bi bi-person"></i>
      </div>
      <input 
        type="text" 
        name="login" 
        id="login" 
        class="input-pill" 
        placeholder="username" 
        value="{{ old('login') }}" 
        required 
        autofocus
      >
    </div>

    {{-- Password Field with Toggle Eye Icon --}}
    <div class="input-group-custom position-relative">
      <div class="icon-wrapper">
        <i class="bi bi-key"></i>
      </div>
      <div class="w-100 position-relative">
        <input 
          type="password" 
          name="password" 
          id="password" 
          class="input-pill pe-5" 
          placeholder="password" 
          required
        >
        <button 
          type="button" 
          id="togglePassword" 
          class="btn position-absolute top-50 end-0 translate-middle-y me-2 border-0 bg-transparent text-muted p-0"
          style="z-index: 10;"
        >
          <i class="bi bi-eye" id="toggleIcon"></i>
        </button>
      </div>
    </div>

    {{-- Log in Submit Button --}}
    <div class="mt-4">
      <button type="submit" class="btn-login-submit">Log in</button>
    </div>
  </form>

  <div class="footer-text">
    Don’t have account? <a href="{{ route('register') }}" class="footer-link">Register</a>
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