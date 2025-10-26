@extends('layouts.app')
@section('title', 'Staff Dashboard')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Staff Dashboard</h1>
        <div class="text-muted">Process, upload, and route assigned documents</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="ms-auto">
        @csrf
        <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>

    @if(session('success'))
      <div class="alert alert-success card-surface border-0">{{ session('success') }}</div>
    @endif

   <div class="card-grid mb-4">
      <a href="{{ route('staff.documents') }}" class="dash-card text-decoration-none">
        <div class="dash-icon"><i class="bi bi-file-earmark-text"></i></div>
        <div class="dash-title">Documents</div>
        <div class="dash-subtitle">View and process assigned documents.</div>
        <div class="dash-action"><i class="bi bi-arrow-right"></i> Go to Documents</div>
      </a>
      <a href="{{ route('staff.history') }}" class="dash-card text-decoration-none">
        <div class="dash-icon"><i class="bi bi-check2-circle"></i></div>
        <div class="dash-title">Processed Documents</div>
        <div class="dash-subtitle">View documents you have processed and returned.</div>
        <div class="dash-action"><i class="bi bi-arrow-right"></i> View Processed</div>
      </a>
    </div>
  </div>
</div>
@endsection
