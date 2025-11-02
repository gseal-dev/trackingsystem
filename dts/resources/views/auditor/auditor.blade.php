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

    <div class="dash-card">
      <div class="dash-icon"><i class="bi bi-folder2-open"></i></div>
      <div class="dash-title">Document Transactions</div>
      <div class="dash-subtitle">View all document transactions except pending ones.</div>
      <a href="{{ route('auditor.documentTransactions') }}" class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</a>
    </div>
  </div>
</div>
@endsection
