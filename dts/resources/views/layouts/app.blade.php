<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Document Tracking System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
      :root{
        --brand-dark:#1f2937;
        --brand:#0f5132;
        --brand-600:#198754;
        --text-muted:#6b7280;
        --surface:#ffffff;
        --surface-2:#f8fafc;
        --ring:rgba(25,135,84,.25);
      }
      body {
        min-height: 100vh;
        background: linear-gradient(120deg, #e8f6ef 0%, #eef6ff 100%);
        color: var(--brand-dark);
      }
      .navbar-brand { font-weight: 700; color: var(--brand-600) !important; }
      .navbar-nav .nav-link { color: var(--brand-dark) !important; font-weight: 500; }
      .navbar-nav .nav-link.active { color: var(--brand-600) !important; }
      .navbar-text { color: var(--text-muted); }
      .dropdown-item i { margin-right: 6px; }
      .page-container{padding-top:32px;padding-bottom:48px;}
      .page-header h1{color:var(--brand-dark);font-weight:700;letter-spacing:.2px;}
      .toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 18px;background:var(--surface);border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,.04);} 
      .card-grid{display:grid;grid-template-columns:repeat(auto-fit, minmax(240px, 1fr));gap:18px;} 
      .dash-card{background:var(--surface);border:1px solid #e5e7eb;border-radius:14px;padding:20px;box-shadow:0 10px 24px rgba(0,0,0,.05);transition:transform .18s ease, box-shadow .18s ease, border-color .18s ease;height:100%;}
      .dash-card:hover{transform:translateY(-3px);box-shadow:0 14px 28px rgba(0,0,0,.07);border-color:#d1d5db;} 
      .dash-icon{width:44px;height:44px;border-radius:10px;display:grid;place-items:center;color:#fff;background:linear-gradient(135deg, var(--brand-600), #20c997);box-shadow:inset 0 0 0 1px rgba(255,255,255,.35);margin-bottom:12px;} 
      .dash-title{font-weight:700;color:var(--brand-dark);} .dash-subtitle{color:var(--text-muted);font-size:.925rem;} 
      .dash-action{margin-top:14px;display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:10px;color:var(--brand-600);border:1px solid rgba(25,135,84,.25);background:#f0fff5;text-decoration:none;font-weight:600;} .dash-action:hover{background:#e7fff0;} 
      .card-surface{background:var(--surface);border:1px solid #e5e7eb;border-radius:14px;box-shadow:0 10px 24px rgba(0,0,0,.05);} 
      .table-clean thead th{background:#f8fafc;border-bottom:1px solid #e5e7eb;color:#334155;} .table-clean td{vertical-align:middle;} 
      .form-control, .form-select{border-radius:10px;border-color:#d1d5db;} .btn-brand{background:var(--brand-600);color:#fff;border:none;border-radius:10px;font-weight:600;box-shadow:0 2px 6px var(--ring);} .btn-brand:hover{background:#157347;}

      /* Dark mode overrides */
      body.theme-dark{
        --brand-dark:#e5e7eb;
        --text-muted:#9ca3af;
        --surface:#111827;
        --surface-2:#0b1726;
        --ring:rgba(16,185,129,.25);
      }
      body.theme-dark {
        background: linear-gradient(120deg, #0b2a20 0%, #0b1a2a 100%);
      }
      body.theme-dark .navbar,
      body.theme-dark .navbar-brand,
      body.theme-dark .navbar-nav .nav-link,
      body.theme-dark .navbar-toggler,
      body.theme-dark .navbar-toggler-icon,
      body.theme-dark .navbar-text {
        background: #111827 !important;
        color: #e5e7eb !important;
        border-color: #374151 !important;
      }
      body.theme-dark .navbar-toggler {
        border-color: #374151 !important;
      }
      body.theme-dark .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(229,231,235,1)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
      }
      body.theme-dark .form-control,
      body.theme-dark .form-select {
        background: #0f172a !important;
        color: #e5e7eb !important;
        border-color: #374151 !important;
      }
      body.theme-dark .form-control::placeholder,
      body.theme-dark input::placeholder,
      body.theme-dark textarea::placeholder {
        color: #9ca3af !important;
        opacity: 1;
      }
      body.theme-dark .table,
      body.theme-dark .table-clean,
      body.theme-dark .table td,
      body.theme-dark .table th {
        background: #111827 !important;
        color: #e5e7eb !important;
        border-color: #374151 !important;
      }
      body.theme-dark .table-clean thead th {
        background: #1e293b !important;
        color: #e5e7eb !important;
        border-bottom: 1px solid #374151 !important;
      }
      body.theme-dark .table-clean tbody td {
        background: #111827 !important;
        color: #e5e7eb !important;
        border-color: #374151 !important;
      }
      body.theme-dark .page-header h1{color:var(--brand-dark);} 
      body.theme-dark .text-muted{color:var(--text-muted) !important;}
      body.theme-dark .dash-action{background:#0b2a20;color:#86efac;border-color:rgba(34,197,94,.35);} 
      body.theme-dark .toolbar{background:var(--surface);border-color:#374151;box-shadow:0 8px 24px rgba(0,0,0,.35);} 
      body.theme-dark .card-surface{background:var(--surface);border-color:#374151;box-shadow:0 12px 28px rgba(0,0,0,.35);} 
      body.theme-dark .btn-outline-success{color:#22c55e;border-color:#22c55e;} 
      body.theme-dark .btn-outline-success:hover{background:#16a34a;color:#fff;border-color:#16a34a;}
      body.theme-dark .navbar-toggler {
        border-color: #e5e7eb !important;
        background: #1e293b !important;
      }
      body.theme-dark .navbar-toggler-icon {
        background-image: url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 30 30' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(229,231,235,1)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 7h22M4 15h22M4 23h22'/%3E%3C/svg%3E");
        filter: none !important;
      }
      .navbar {
        position: sticky;
        top: 0;
        z-index: 1040;
        margin-bottom: 0 !important;
      }
      @media (max-width: 991.98px) {
        .navbar-collapse {
          position: static !important;
          min-height: auto !important;
          box-shadow: none !important;
          background: transparent !important;
          padding: 0 !important;
        }
      }

      /* .card-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 18px;
        justify-items: stretch;
      }

      .dash-card {
        width: 100%;
        max-width: 340px;
        margin-left: 0;
        margin-right: 0;
      } */

      @media (max-width: 1200px) {
        .card-grid {
          grid-template-columns: repeat(2, 1fr);
        }
      }
      @media (max-width: 800px) {
        .card-grid {
          grid-template-columns: 1fr;
        }
        .dash-card {
          width: 100%;
          max-width: 95vw;
          margin-left: auto;
          margin-right: auto;
        }
      }
    </style>
    @stack('head')
  </head>
  <body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
          <i class="bi bi-files"></i> Document Tracking System
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain" aria-controls="navbarMain" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarMain">
          <ul class="navbar-nav me-auto mb-2 mb-lg-0">
            @auth
              @php
                $role = auth()->user()->role->roleName ?? '';
              @endphp
              @if($role === 'Admin')
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/user-management*') ? 'active' : '' }}" href="{{ route('admin.userManagement') }}">User Management</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/document-registration*') ? 'active' : '' }}" href="{{ route('admin.documentRegistration') }}">Document Registration</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/send-document*') ? 'active' : '' }}" href="{{ route('admin.sendDocumentList') }}">Send Documents</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('admin/processed-documents*') ? 'active' : '' }}" href="{{ route('admin.processedDocuments') }}">Processed Documents</a></li>
              @elseif($role === 'Staff')
                <li class="nav-item"><a class="nav-link {{ request()->is('staff/documents*') ? 'active' : '' }}" href="{{ route('staff.documents') }}">Documents</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('staff/history*') ? 'active' : '' }}" href="{{ route('staff.history') }}">Processed Documents</a></li>
              @elseif($role === 'DocumentOwner')
                <li class="nav-item"><a class="nav-link {{ request()->is('document-owner/submitted-documents*') ? 'active' : '' }}" href="{{ route('documentOwner.submittedDocuments') }}">Submitted</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('document-owner/pending-documents*') ? 'active' : '' }}" href="{{ route('documentOwner.pendingDocuments') }}">Pending</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->is('document-owner/completed-documents*') ? 'active' : '' }}" href="{{ route('documentOwner.completedDocuments') }}">Completed</a></li>
              @elseif($role === 'Auditor')
                
              @endif
            @endauth
          </ul>
          <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
            @auth
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle navbar-text" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <strong>{{ auth()->user()->username ?? 'Unknown' }}</strong>
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item"><i class="bi bi-box-arrow-right"></i> Logout</button>
                    </form>
                  </li>
                  <li>
                    <button id="themeToggle" class="dropdown-item" type="button">
                      <i class="bi bi-moon-stars" id="themeIcon"></i>
                      <span id="themeLabel">Dark</span>
                    </button>
                  </li>
                </ul>
              </li>
            @endauth
            @guest
              <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
              <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
            @endguest
          </ul>
        </div>
      </div>
    </nav>
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      (function(){
        const root = document.body;
        const key = 'dts-theme';
        const btn = document.getElementById('themeToggle');
        const icon = document.getElementById('themeIcon');
        const label = document.getElementById('themeLabel');

        function applyTheme(theme){
          if(theme === 'dark'){ root.classList.add('theme-dark'); }
          else { root.classList.remove('theme-dark'); }
          if(icon && label){
            const isDark = root.classList.contains('theme-dark');
            icon.className = isDark ? 'bi bi-sun' : 'bi bi-moon-stars';
            label.textContent = isDark ? 'Light' : 'Dark';
          }
        }

        // Initialize
        const saved = localStorage.getItem(key);
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(saved ? saved : (prefersDark ? 'dark' : 'light'));

        // Toggle
        if(btn){
          btn.addEventListener('click', function(){
            const next = root.classList.contains('theme-dark') ? 'light' : 'dark';
            localStorage.setItem(key, next);
            applyTheme(next);
          });
        }

        // React to OS changes if user hasn't explicitly chosen
        if(!saved && window.matchMedia){
          window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e)=>{
            applyTheme(e.matches ? 'dark' : 'light');
          });
        }
      })();
    </script>
    @stack('scripts')
  </body>
</html>