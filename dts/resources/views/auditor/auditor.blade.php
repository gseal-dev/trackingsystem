@extends('layouts.app')
@section('title', 'Auditor Dashboard')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 720px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Auditor Dashboard</h1>
        <div class="text-muted">Review and validate processed documents</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="ms-auto">
        @csrf
        <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>

    <div class="dash-card text-center">
      <div class="dash-icon mx-auto"><i class="bi bi-shield-check"></i></div>
      <div class="dash-title mb-1">You're all set</div>
      <div class="dash-subtitle">Use the navigation to access your tasks.</div>
    </div>
  </div>
</div>
@endsection
