@extends('layouts.app')

@section('title', 'Document Owner Dashboard')

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-container">
        <div class="dashboard-header d-flex align-items-center justify-content-between">
            <div>
                <h1 class="dashboard-title">Welcome, Document Owner</h1>
                <p class="dashboard-subtitle">Track your documents through the workflow</p>
            </div>
            <div>
                <form method="POST" action="{{ route('logout') }}" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-dark px-4 py-2" style="background-color: #000000; color: #ffffff; border-radius: 50px; font-weight: 600;">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </div>
        </div>

        <div class="dashboard-grid">
            <a href="{{ route('documentOwner.submittedDocuments') }}" class="dashboard-card">
                <div class="dash-icon"><i class="bi bi-box-seam"></i></div>
                <div class="dash-title">Submitted Documents</div>
                <div class="dash-subtitle">Track your recently submitted documents and view their status.</div>
                <div class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</div>
            </a>

            <a href="{{ route('documentOwner.pendingDocuments') }}" class="dashboard-card">
                <div class="dash-icon"><i class="bi bi-hourglass-split"></i></div>
                <div class="dash-title">Pending Documents</div>
                <div class="dash-subtitle">View documents that are currently pending.</div>
                <div class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</div>
            </a>

            <a href="{{ route('documentOwner.completedDocuments') }}" class="dashboard-card">
                <div class="dash-icon"><i class="bi bi-check2-circle"></i></div>
                <div class="dash-title">Completed Documents</div>
                <div class="dash-subtitle">View documents that are fully processed and approved.</div>
                <div class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</div>
            </a>
        </div>
    </div>
</div>
@endsection