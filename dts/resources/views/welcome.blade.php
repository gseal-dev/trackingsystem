@extends('layouts.app')

@section('title', 'Welcome')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-4">
      <h1 class="h3 mb-1">Document Tracking System</h1>
      <div class="text-muted">Manage, monitor, and track documents across departments</div>
    </div>

    <div class="card-surface p-5 text-center">
      <h2 class="display-6 mb-2">Welcome</h2>
      <p class="text-muted mb-4">Effortlessly streamline your workflow and ensure every document reaches its destination.</p>
      <div class="d-flex gap-3 justify-content-center flex-wrap">
        <a href="{{ route('login') }}" class="btn btn-brand px-4">Login</a>
        <a href="{{ route('register') }}" class="btn btn-outline-success px-4">Register</a>
      </div>
    </div>
  </div>
</div>
@endsection
