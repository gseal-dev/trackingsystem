@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="login-card" style="max-width: 580px;">
  <h1 class="login-title">Register</h1>
  <p class="login-subtitle">Create an account to manage and track your documents</p>

  @if($errors->any())
    <div class="alert alert-danger text-start py-2 px-3 mb-4 rounded-4" style="font-size: 0.85rem;">
      @foreach($errors->all() as $error)
        <div>{{ $error }}</div>
      @endforeach
    </div>
  @endif

  <form method="POST" action="{{ route('register') }}">
    @csrf

    {{-- Username --}}
    <div class="input-group-custom">
      <div class="icon-wrapper">
        <i class="bi bi-person"></i>
      </div>
      <input 
        type="text" 
        name="username" 
        id="username" 
        class="input-pill" 
        placeholder="username" 
        value="{{ old('username') }}" 
        required 
        autofocus
      >
    </div>

    {{-- Email --}}
    <div class="input-group-custom">
      <div class="icon-wrapper">
        <i class="bi bi-envelope"></i>
      </div>
      <input 
        type="email" 
        name="email" 
        id="email" 
        class="input-pill" 
        placeholder="email address" 
        value="{{ old('email') }}" 
        required
      >
    </div>

    {{-- First and Last Name --}}
    <div class="row g-2 mb-3 ms-md-4 ps-md-2">
      <div class="col-md-6">
        <input 
          type="text" 
          name="firstName" 
          id="firstName" 
          class="input-pill" 
          placeholder="first name" 
          value="{{ old('firstName') }}" 
          required
        >
      </div>
      <div class="col-md-6">
        <input 
          type="text" 
          name="lastName" 
          id="lastName" 
          class="input-pill" 
          placeholder="last name" 
          value="{{ old('lastName') }}" 
          required
        >
      </div>
    </div>

    {{-- Password --}}
    <div class="input-group-custom">
      <div class="icon-wrapper">
        <i class="bi bi-key"></i>
      </div>
      <input 
        type="password" 
        name="password" 
        id="password" 
        class="input-pill" 
        placeholder="password" 
        required
      >
    </div>

    {{-- Confirm Password --}}
    <div class="input-group-custom">
      <div class="icon-wrapper">
        <i class="bi bi-shield-lock"></i>
      </div>
      <input 
        type="password" 
        name="password_confirmation" 
        id="password_confirmation" 
        class="input-pill" 
        placeholder="confirm password" 
        required
      >
    </div>

    {{-- Register Button --}}
    <div class="mt-4">
      <button type="submit" class="btn-login-submit">Register</button>
    </div>
  </form>

  <div class="footer-text">
    Already have an account? <a href="{{ route('login') }}" class="footer-link">Log in</a>
  </div>
</div>
@endsection