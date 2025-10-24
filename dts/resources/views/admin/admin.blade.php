@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<style>
    :root {
        --brand-dark: #1f2937; /* gray-800 */
        --brand: #0f5132; /* bootstrap success dark */
        --brand-600: #198754; /* bootstrap success */
        --text-muted: #6b7280; /* gray-500 */
        --surface: #ffffff;
        --surface-2: #f8fafc; /* slate-50 */
        --ring: rgba(25, 135, 84, 0.25);
    }

    .page-bg {
        background: radial-gradient(1200px 600px at 10% -10%, #e8f6ef 0%, transparent 60%),
                    radial-gradient(1200px 600px at 110% 10%, #eef6ff 0%, transparent 60%);
        padding-top: 32px;
        padding-bottom: 48px;
    }

    .page-header h1 {
        color: var(--brand-dark);
        font-weight: 700;
        letter-spacing: .2px;
    }

    .toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 14px 18px;
        background: var(--surface);
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 6px 20px rgba(0,0,0,.04);
    }

    .logout-btn {
        background: var(--brand-600);
        color: #fff;
        border: none;
        padding: 10px 16px;
        border-radius: 10px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: transform .06s ease, box-shadow .2s ease, background .2s ease;
        box-shadow: 0 2px 6px var(--ring);
    }
    .logout-btn:hover { background: #157347; }
    .logout-btn:active { transform: translateY(1px); }

    .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 18px;
    }

    .dash-card {
        background: var(--surface);
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
        background: linear-gradient(135deg, var(--brand-600), #20c997);
        box-shadow: inset 0 0 0 1px rgba(255,255,255,.35);
        margin-bottom: 12px;
    }

    .dash-title { font-weight: 700; color: var(--brand-dark); }
    .dash-subtitle { color: var(--text-muted); font-size: .925rem; }

    .dash-action {
        margin-top: 14px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 10px;
        color: var(--brand-600);
        border: 1px solid rgba(25, 135, 84, .25);
        background: #f0fff5;
        text-decoration: none;
        font-weight: 600;
    }
    .dash-action:hover { background: #e7fff0; }
</style>

<div class="page-bg">
    <div class="container" style="max-width: 1100px;">
        <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
            <div>
                <h1 class="h3 mb-1">Admin Dashboard</h1>
                <div class="text-muted">Quick access to management and document workflows</div>
            </div>
            <form method="POST" action="{{ route('logout') }}" class="ms-auto">
                @csrf
                <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
            </form>
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
