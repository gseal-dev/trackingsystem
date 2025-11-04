@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

<div class="page-bg">
    <div class="container" style="max-width: 1100px;">
        <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1">Admin Dashboard</h1>
                <div class="text-muted">Quick access to management and document workflows</div>
            </div>
        </div>

        <div class="toolbar mb-4">
            <div class="d-flex align-items-center gap-2 text-muted">
                <i class="bi bi-speedometer2 text-success"></i>
                <span class="small">You are signed in as</span>
                <strong>{{ auth()->user()->name ?? 'Administrator' }}</strong>
            </div>
            <div class="d-flex align-items-center gap-2 text-muted small">
                <i class="bi bi-shield-lock"></i>
                <span>Secure Area</span>
            </div>
        </div>

        <div class="card-grid">
            <div class="dash-card">
                <div class="dash-icon"><i class="bi bi-people"></i></div>
                <div class="dash-title">User Management</div>
                <div class="dash-subtitle">Create, edit, and manage system users and roles.</div>
                <a href="{{ route('admin.userManagement') }}" class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</a>
            </div>

            <div class="dash-card">
                <div class="dash-icon"><i class="bi bi-journal-plus"></i></div>
                <div class="dash-title">Document Registration</div>
                <div class="dash-subtitle">Register incoming documents and assign metadata.</div>
                <a href="{{ route('admin.documentRegistration') }}" class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</a>
            </div>

            <div class="dash-card">
                <div class="dash-icon"><i class="bi bi-send"></i></div>
                <div class="dash-title">Send Documents</div>
                <div class="dash-subtitle">Route documents to departments or staff.</div>
                <a href="{{ route('admin.sendDocumentList') }}" class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</a>
            </div>

            <div class="dash-card">
                <div class="dash-icon"><i class="bi bi-archive"></i></div>
                <div class="dash-title">Processed Documents</div>
                <div class="dash-subtitle">Review completed and archived documents.</div>
                <a href="{{ route('admin.processedDocuments') }}" class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</a>
            </div>
        </div>
    </div>
</div>
@endsection
