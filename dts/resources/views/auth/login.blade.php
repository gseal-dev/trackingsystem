@extends('layouts.app')

@section('title', 'Login')

@section('content')
<div class="page-container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
  <div class="container" style="max-width: 520px;">
    <div class="card-surface p-4">
      <h2 class="h4 text-center mb-1">Welcome Back 👋</h2>
      <p class="text-muted text-center mb-4">Login to continue tracking your documents.</p>
      @if($errors->any())
        <div class="alert alert-danger">
          @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
          @endforeach
        </div>
      @endif

      <form method="POST" action="{{ route('login') }}">
        @csrf
        <div class="mb-3">
          <label for="login" class="form-label fw-semibold">Username or Email</label>
          <input type="text" name="login" id="login" class="form-control" required placeholder="Enter your username or email">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label fw-semibold">Password</label>
          <input type="password" name="password" id="password" class="form-control" required placeholder="Enter your password">
        </div>
        <button type="submit" class="btn btn-brand w-100">Login</button>
      </form>

      <div class="text-center mt-3">
        <a href="{{ route('register') }}" class="text-decoration-none">Don’t have an account? Register</a>
      </div>
    </div>
  </div>
</div>
@endsection