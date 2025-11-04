@extends('layouts.app')

@section('title', 'Document Owner Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
    }

    .dash-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        padding: 20px;
        box-shadow: 0 10px 24px rgba(0,0,0,.05);
        transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        height: 100%;
    }
    .dash-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px rgba(0,0,0,.07);
        border-color: #d1d5db;
    }

    .dash-icon {
        width: 44px;
        height: 44px;
        border-radius: 10px;
        display: grid;
        place-items: center;
        color: #fff;
        background: linear-gradient(135deg, #198754, #20c997);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.35);
        margin-bottom: 12px;
    }

    .dash-title { font-weight: 700; color: #1f2937; }
    .dash-subtitle { color: #6b7280; font-size: .925rem; }

    .dash-action {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 10px;
        color: #198754;
        border: 1px solid rgba(25, 135, 84, .25);
        background: #f0fff5;
        text-decoration: none;
        font-weight: 600;
    }
    .dash-action:hover { background: #e7fff0; }
</style>

<div class="page-container">
    <div class="container" style="max-width: 1100px;">
        <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1">Welcome, Document Owner</h1>
                <div class="text-muted">Track your documents through the workflow</div>
            </div>
        </div>

        <div class="card-grid">
            <div class="dash-card">
                <div class="dash-icon"><i class="bi bi-box-seam"></i></div>
                <div class="dash-title">Submitted Documents</div>
                <div class="dash-subtitle">Track your recently submitted documents and view their status.</div>
                <a href="{{ route('documentOwner.submittedDocuments') }}" class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</a>
            </div>

            <div class="dash-card">
              <div class="dash-icon"><i class="bi bi-hourglass-split"></i></div>
              <div class="dash-title">Pending Documents</div>
              <div class="dash-subtitle">View documents that are currently pending.</div>
              <a href="{{ route('documentOwner.pendingDocuments') }}" class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</a>
            </div>

            <div class="dash-card">
              <div class="dash-icon"><i class="bi bi-check2-circle"></i></div>
              <div class="dash-title">Completed Documents</div>
              <div class="dash-subtitle">View documents that are fully processed and approved.</div>
              <a href="{{ route('documentOwner.completedDocuments') }}" class="dash-action"><i class="bi bi-arrow-right-circle"></i> Open</a>
            </div>
        </div>
    </div>
</div>
@endsection