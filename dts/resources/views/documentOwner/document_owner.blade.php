@extends('layouts.app')

@section('title', 'Document Owner Dashboard')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Welcome, Document Owner</h1>
        <div class="text-muted">Track your documents through the workflow</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="ms-auto">
        @csrf
        <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>

    <div class="card-grid">
      <div class="dash-card">
        <div class="dash-icon"><i class="bi bi-box-seam"></i></div>
        <div class="dash-title">Submitted Documents</div>
        <div class="dash-subtitle">Track your recently submitted documents and view their status.</div>
      </div>
      <div class="dash-card">
        <div class="dash-icon"><i class="bi bi-hourglass-split"></i></div>
        <div class="dash-title">Pending Approvals</div>
        <div class="dash-subtitle">Documents currently under review or awaiting signature.</div>
      </div>
      <div class="dash-card">
        <div class="dash-icon"><i class="bi bi-check2-circle"></i></div>
        <div class="dash-title">Completed</div>
        <div class="dash-subtitle">Access documents that are fully processed and approved.</div>
      </div>
    </div>
  </div>
</div>
@endsection
