@extends('layouts.app')
@section('title', 'User Management')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-4">
      <h1 class="h3 mb-1">User Management</h1>
      <div class="text-muted">Select a user type to manage:</div>
    </div>
    <div class="card-grid mb-4">
      <div class="dash-card">
        <div class="dash-icon"><i class="bi bi-person-badge"></i></div>
        <div class="dash-title">Admins</div>
        <div class="dash-subtitle">Manage admin users</div>
        <div class="d-flex gap-2 mt-3">
          <a href="{{ route('admin.userManagement.addForm', ['type' => 'admins']) }}" class="btn btn-success"><i class="bi bi-person-plus"></i> Add</a>
        </div>
      </div>
      <div class="dash-card">
        <div class="dash-icon"><i class="bi bi-person"></i></div>
        <div class="dash-title">Document Owners</div>
        <div class="dash-subtitle">Manage document owners</div>
        <div class="d-flex gap-2 mt-3">
        </div>
      </div>
      <div class="dash-card">
        <div class="dash-icon"><i class="bi bi-people"></i></div>
        <div class="dash-title">Staff</div>
        <div class="dash-subtitle">Manage staff users</div>
        <div class="d-flex gap-2 mt-3">
          <a href="{{ route('admin.userManagement.staffs') }}" class="btn btn-brand"><i class="bi bi-arrow-right-circle"></i> View</a>
        </div>
      </div>
      <div class="dash-card">
        <div class="dash-icon"><i class="bi bi-person-check"></i></div>
        <div class="dash-title">Auditors</div>
        <div class="dash-subtitle">Manage auditor users</div>
        <div class="d-flex gap-2 mt-3">
          <a href="{{ route('admin.userManagement.auditors') }}" class="btn btn-brand"><i class="bi bi-arrow-right-circle"></i> View</a>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection