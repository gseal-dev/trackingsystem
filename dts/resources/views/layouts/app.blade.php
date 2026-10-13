<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Document Tracking System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght\@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
      :root {
        --brand-dark: #000000;
        --brand-muted: #64748b;
        --bg-main: #f1f5f9;
        --card-bg: #ffffff;
        --input-bg: #ffffff;
        --border-color: #cbd5e1;
        --btn-dark: #000000;
        --btn-dark-hover: #000a33;
        --accent-orange: #ea3a14;
        --page-max: 1800px;
        --page-gutter: 1rem;
      }
      body {
        min-height: 100vh;
        background:
          radial-gradient(circle at 10% 15%, rgba(0, 18, 83, 0.10) 0%, transparent 45%),
          radial-gradient(circle at 90% 85%, rgba(234, 58, 20, 0.08) 0%, transparent 45%),
          radial-gradient(circle at 50% 50%, rgba(100, 116, 139, 0.06) 0%, transparent 65%),
          linear-gradient(135deg, #f4f7fb 0%, #e5ecf6 100%);
        color: var(--brand-dark);
        font-family: 'Inter', system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        display: flex;
        flex-direction: column;
        margin: 0;
        padding-top: 70px;
        position: relative;
        overflow-x: hidden;
      }
      body.login-page {
        background: #001253 !important;
        padding-top: 0 !important;
        overflow: hidden !important;
        height: 100vh !important;
      }
      body.login-page::after {
        display: none !important;
      }
      body.login-page main.main-content {
        padding: 0 !important;
        margin: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        max-height: 100vh !important;
        display: flex !important;
        align-items: stretch !important;
        justify-content: flex-end !important;
      }
      body.login-page .login-card {
        margin: auto !important;
        box-shadow: none !important;
      }
      .official-dpwh-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.06);
        z-index: 1100;
      }
      .header-inner {
        max-width: var(--page-max);
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.4rem var(--page-gutter);
      }
      .header-dept-name {
        font-size: clamp(1.2rem, 2.1vw, 1.7rem);
        font-weight: 900;
        color: #001253;
        letter-spacing: -0.02em;
        text-transform: uppercase;
        line-height: 1.15;
      }
      .header-slogan {
        font-size: 0.85rem;
        font-style: italic;
        color: #64748b;
        margin-top: 0.1rem;
        letter-spacing: 0.03em;
        font-weight: 600;
      }
      .header-banner-stripe {
        height: 4px;
        background: linear-gradient(90deg, #001253 0%, #ea3a14 50%, #001253 100%);
        width: 100%;
      }
      .app-horizontal-navbar {
        position: fixed;
        top: 105px;
        left: 0;
        width: 100%;
        background: #001253;
        color: #ffffff;
        z-index: 1090;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
      }
      .nav-inner {
        max-width: var(--page-max);
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.55rem var(--page-gutter);
      }
      .nav-links {
        display: flex;
        align-items: center;
        gap: 1.5rem;
      }
      .nav-links a {
        color: #cbd5e1;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        padding: 0.35rem 0.85rem;
        border-radius: 50px;
        transition: all 0.2s ease;
      }
      .nav-links a:hover,
      .nav-links a.active {
        background-color: #ea3a14;
        color: #ffffff;
      }
      .nav-user-area {
        display: flex;
        align-items: center;
        gap: 1rem;
        color: #e2e8f0;
      }
      .table {
        margin-bottom: 0;
        vertical-align: middle;
      }
      .table th {
        text-transform: uppercase;
        font-size: 0.82rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        color: #374151;
        background-color: #f8fafc !important;
        padding: 1.15rem 1rem !important;
        border-bottom: 2px solid #e5e7eb !important;
        border-top: none !important;
      }
      .table td {
        padding: 1.15rem 1rem !important;
        font-size: 0.88rem;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9 !important;
        vertical-align: middle;
      }
      main.main-content {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem var(--page-gutter);
        position: relative;
      }
      body:not(.login-page) main.main-content {
        align-items: flex-start;
      }

      /* Pagination links (document lists) in the system's navy */
      .pagination {
        --bs-pagination-color: rgb(0, 18, 83);
        --bs-pagination-hover-color: rgb(0, 18, 83);
        --bs-pagination-focus-color: rgb(0, 18, 83);
        --bs-pagination-active-bg: rgb(0, 18, 83);
        --bs-pagination-active-border-color: rgb(0, 18, 83);
        --bs-pagination-focus-box-shadow: 0 0 0 0.2rem rgba(0, 18, 83, 0.15);
      }
      .login-card, .card-surface {
        background-color: var(--card-bg);
        border: none;
        border-radius: 28px;
        padding: 3.5rem 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        width: 100%;
        max-width: 420px;
        margin: auto;
        text-align: center;
        position: relative;
        z-index: 1;
      }
      .dashboard-shell {
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 0;
        position: relative;
      }
      .page-card {
        --pc-pad: 1.75rem;
        background: #ffffff;
        border-radius: 6px;
        overflow: hidden;
        box-shadow: 0 6px 24px rgba(15, 23, 42, 0.12);
        color: #1f2937;
        width: 100%;
        margin: 0 auto;
        padding: 0 var(--pc-pad) var(--pc-pad);
      }
      .page-card-header {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        margin: 0 calc(var(--pc-pad) * -1) 1.5rem;
        padding: 0.95rem 1.5rem;
        color: #ffffff;
        font-weight: 700;
        font-size: 0.95rem;
        background: rgb(0, 18, 83);
      }
      @media (max-width: 575.98px) {
        .page-card { --pc-pad: 1rem; }
      }
      .modal-backdrop { z-index: 1190; }
      .modal { z-index: 1200; }
      .app-header {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 58px;
        background: #ffffff;
        border-bottom: 1px solid #e5e7eb;
      }
      .app-brand {
        display: inline-flex;
        align-items: center;
        gap: 0.55rem;
        color: var(--brand-dark);
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: -0.03em;
        text-decoration: none;
      }
      .app-brand-mark {
        color: var(--accent-orange);
        font-size: 1.6rem;
        line-height: 1;
      }
      .app-nav {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        margin-left: 2rem;
        margin-right: auto;
      }
      .app-nav a {
        color: #374151;
        font-size: 0.9rem;
        text-decoration: none;
      }
      .app-nav a.active,
      .app-nav a:hover {
        color: var(--accent-orange);
      }
      .app-user {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        color: #374151;
        font-size: 0.9rem;
      }
      .app-header .logout-button {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        width: auto;
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.8rem;
      }
      @media (max-width: 640px) {
        .app-header {
          flex-wrap: wrap;
          gap: 0.65rem;
        }
        .app-nav {
          order: 3;
          width: 100%;
          margin: 0;
        }
        .app-user {
          margin-left: auto;
        }
      }
      .dashboard-container,
      .dashboard-container.wide {
        width: 100%;
        max-width: var(--page-max);
        margin: 0 auto;
        padding: 0;
      }
      .dashboard-header {
        margin-bottom: 1.5rem;
      }
      .dashboard-title {
        margin: 0;
        font-size: clamp(2rem, 3vw, 2.7rem);
        font-weight: 800;
        letter-spacing: -0.05em;
        color: var(--brand-dark);
      }
      .dashboard-subtitle {
        margin: 0.5rem 0 0;
        color: var(--brand-muted);
      }
      .dashboard-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        padding: 1rem 1.25rem;
        margin-bottom: 1.5rem;
        border-radius: 20px;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
      }
      .toolbar-item {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        color: var(--brand-dark);
        font-size: 0.92rem;
      }
      .toolbar-item.muted {
        color: var(--brand-muted);
      }
      .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
      }
      .dashboard-card {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
        padding: 1.5rem;
        border-radius: 24px;
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.03);
        color: var(--brand-dark);
        text-decoration: none;
        transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
      }
      .dashboard-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.06);
        border-color: #cbd5e1;
        color: var(--brand-dark);
        text-decoration: none;
      }
      .dash-icon {
        width: 48px;
        height: 48px;
        display: grid;
        place-items: center;
        border-radius: 14px;
        font-size: 1.35rem;
        background-color: var(--brand-dark);
        color: #ffffff;
      }
      .dash-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--brand-dark);
      }
      .dash-subtitle {
        color: var(--brand-muted);
        line-height: 1.6;
      }
      .dash-action {
        margin-top: auto;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        width: fit-content;
        padding: 0.7rem 1rem;
        border-radius: 999px;
        background-color: var(--brand-dark);
        color: #ffffff;
        font-weight: 600;
      }
      .dashboard-panel {
        background-color: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 20px;
      }
      .dashboard-alert {
        margin-bottom: 1.5rem;
        border-radius: 16px;
        border: none;
        color: #0f5132;
        background-color: #eefaf3;
      }
      .audit-search {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        width: 100%;
        max-width: 650px;
      }
      .audit-search .form-control {
        border-radius: 50px;
      }
      .export-btn {
        width: auto;
      }
      .login-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--brand-dark);
        letter-spacing: -0.5px;
        margin-bottom: 0.25rem;
      }
      .login-subtitle {
        color: var(--brand-muted);
        font-size: 0.875rem;
        margin-bottom: 2.5rem;
      }
      .input-group-custom {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        margin-bottom: 1.25rem;
      }
      .input-group-custom .icon-wrapper {
        width: 32px;
        display: flex;
        justify-content: center;
        font-size: 1.6rem;
        color: var(--brand-dark);
      }
      .form-control, .form-select, .input-pill {
        border-radius: 50px !important;
        padding: 0.65rem 1.25rem;
        background-color: var(--input-bg);
        border: 1px solid var(--border-color);
        color: var(--brand-dark);
        font-size: 0.925rem;
        width: 100%;
      }
      textarea.form-control {
        border-radius: 20px !important;
      }
      .form-control::placeholder, .input-pill::placeholder {
        color: #a0aec0;
      }
      .form-control:focus, .form-select:focus, .input-pill:focus {
        border-color: var(--btn-dark);
        box-shadow: none;
        outline: none;
      }
      .btn-brand, .btn-custom-dark, .btn-login-submit {
        background-color: var(--btn-dark);
        color: #ffffff;
        border-radius: 50px;
        padding: 0.65rem 1.5rem;
        font-weight: 600;
        border: none;
        width: 100%;
        transition: background-color 0.2s ease;
      }
      .btn-brand:hover, .btn-custom-dark:hover, .btn-login-submit:hover {
        background-color: var(--btn-dark-hover);
        color: #ffffff;
      }
      .footer-text {
        font-size: 0.875rem;
        color: var(--brand-muted);
        margin-top: 2rem;
      }
      .footer-link {
        color: var(--brand-dark);
        font-weight: 700;
        text-decoration: none;
      }
      .footer-link:hover {
        text-decoration: underline;
      }
      .table-clean {
        background-color: var(--card-bg);
        border-radius: 20px;
        overflow: hidden;
      }
      .table-clean thead th {
        background-color: #eaeaea;
        border-bottom: 1px solid var(--border-color);
        color: var(--brand-dark);
        font-weight: 700;
      }
      body.theme-dark .btn-brand,
      body.theme-dark .btn-custom-dark,
      body.theme-dark .btn-login-submit {
        color: #ffffff;
      }
      body.theme-dark .btn-brand:hover,
      body.theme-dark .btn-custom-dark:hover,
      body.theme-dark .btn-login-submit:hover {
        color: #0f172a;
      }
      body.theme-dark .table-clean thead th {
        background-color: #111827;
      }
      .dashboard-logout {
        position: absolute;
        top: 3rem;
        right: 1.5rem;
        z-index: 10;
      }
      :root {
        --sidebar-collapsed-width: 72px;
        --sidebar-expanded-width: 250px;
        --sidebar-top: 0px;
      }
      .app-sidebar {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: var(--sidebar-collapsed-width);
        display: flex;
        flex-direction: column;
        overflow-x: hidden;
        overflow-y: auto;
        background: #001253;
        color: #ffffff;
        z-index: 1080;
        box-shadow: 4px 0 15px rgba(0, 0, 0, 0.08);
        transition: width 0.25s ease;
      }
      body.sidebar-expanded .app-sidebar {
        width: var(--sidebar-expanded-width);
      }
      body:not(.login-page) .official-dpwh-header {
        left: var(--sidebar-collapsed-width);
        width: calc(100% - var(--sidebar-collapsed-width));
        transition: left 0.25s ease, width 0.25s ease;
      }
      body.sidebar-expanded:not(.login-page) .official-dpwh-header {
        left: var(--sidebar-expanded-width);
        width: calc(100% - var(--sidebar-expanded-width));
      }
      .app-sidebar i.bi,
      .app-sidebar i[class^="bi-"],
      .app-sidebar i[class*=" bi-"] {
        display: inline-block;
        font-family: "bootstrap-icons" !important;
        font-style: normal;
        font-weight: normal !important;
        line-height: 1;
        speak: none;
        -webkit-font-smoothing: antialiased;
      }
      .sidebar-top {
        display: flex;
        align-items: center;
        min-height: 92px;
        padding: 0 16px;
        gap: 14px;
        border-bottom: 1px solid rgba(255,255,255,0.12);
      }
      .sidebar-toggle {
        flex: 0 0 40px;
        width: 40px;
        height: 40px;
        display: grid;
        place-items: center;
        background: transparent;
        border: 0;
        border-radius: 8px;
        color: #ffffff;
        font-size: 1.5rem;
        cursor: pointer;
      }
      .sidebar-toggle:hover { background: rgba(255,255,255,0.12); }
      .sidebar-heading,
      .sidebar-label {
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.15s ease;
      }
      body.sidebar-expanded .sidebar-heading,
      body.sidebar-expanded .sidebar-label {
        opacity: 1;
        visibility: visible;
      }
      .sidebar-heading {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.04em;
      }
      .sidebar-menu {
        display: flex;
        flex-direction: column;
        gap: 6px;
        padding: 16px 10px;
      }
      .sidebar-link {
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 48px;
        width: 100%;
        padding: 0 15px;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #cbd5e1;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 600;
        text-align: left;
        white-space: nowrap;
        transition: background 0.2s ease, color 0.2s ease;
      }
      .sidebar-link > i,
      .sidebar-user > i {
        flex: 0 0 22px;
        width: 22px;
        text-align: center;
        font-size: 1.2rem;
      }
      .sidebar-link:hover,
      .sidebar-link.active {
        color: #ffffff;
        background: #ea3a14;
      }
      .sidebar-footer {
        margin-top: auto;
        padding: 12px 10px 18px;
        border-top: 1px solid rgba(255,255,255,0.12);
      }
      .sidebar-user {
        display: flex;
        align-items: center;
        gap: 16px;
        min-height: 45px;
        padding: 0 15px;
        color: #e2e8f0;
        font-size: 0.85rem;
        overflow: hidden;
      }
      /* profile row (staff): clickable, opens Account Settings */
      a.sidebar-profile {
        margin-bottom: 0;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        transition: background 0.2s ease, color 0.2s ease;
      }
      a.sidebar-profile:hover,
      a.sidebar-profile.active {
        color: #ffffff;
        background: #ea3a14;
      }
      .sidebar-footer form { margin: 6px 0 0; }
      .sidebar-logout { cursor: pointer; font-family: inherit; }
      body:not(.login-page) main.main-content {
        margin-left: var(--sidebar-collapsed-width);
        width: calc(100% - var(--sidebar-collapsed-width));
        padding: 1rem;
        transition: margin-left 0.25s ease, width 0.25s ease;
      }
      body.sidebar-expanded:not(.login-page) main.main-content {
        margin-left: var(--sidebar-expanded-width);
        width: calc(100% - var(--sidebar-expanded-width));
      }
      .dashboard-shell,
      .dashboard-container,
      .dashboard-container.wide,
      .page-card { min-width: 0; }
      .sidebar-overlay { display: none; }
      @media (max-width: 767.98px) {
        body:not(.login-page) .official-dpwh-header,
        body.sidebar-expanded:not(.login-page) .official-dpwh-header {
          left: 0;
          width: 100%;
        }
        .app-sidebar {
          width: var(--sidebar-expanded-width);
          top: 0;
          transform: translateX(calc(-1 * var(--sidebar-expanded-width)));
          transition: transform 0.25s ease;
        }
        body.sidebar-expanded .app-sidebar {
          width: var(--sidebar-expanded-width);
          transform: translateX(0);
        }
        .app-sidebar .sidebar-heading,
        .app-sidebar .sidebar-label {
          opacity: 1;
          visibility: visible;
        }
        body:not(.login-page) main.main-content,
        body.sidebar-expanded:not(.login-page) main.main-content {
          margin-left: 0;
          width: 100%;
          padding: 1rem;
        }
        body.sidebar-expanded .sidebar-overlay {
          display: block;
          position: fixed;
          inset: var(--sidebar-top) 0 0;
          background: rgba(15, 23, 42, 0.45);
          z-index: 1070;
        }
      }
    </style>
    @stack('head')
  </head>
  <body class="{{ (request()->is('/') || request()->routeIs('login')) ? 'login-page' : '' }}">
    @if(!request()->is('/') && !request()->routeIs('login'))
    <header class="official-dpwh-header">
      <div class="header-inner">
        <div class="header-logo-left">
          <img src="{{ asset('images/dpwh-logo.png') }}" alt="DPWH Seal" style="height: 52px; width: 52px; object-fit: contain; mix-blend-mode: multiply;">
        </div>
        <div class="header-titles text-center">
          <div class="header-dept-name">RECORDS MANAGEMENT</div>
          <div class="header-slogan">We love others as we love ourselves</div>
        </div>
        <div class="header-logo-right">
          <img src="{{ asset('images/bagong-pilipinas.webp') }}" alt="Bagong Pilipinas" style="height: 52px; width: 52px; object-fit: contain; mix-blend-mode: multiply;">
        </div>
      </div>
      <div class="header-banner-stripe"></div>
    </header>
    @auth
    @php $role = auth()->user()->role?->roleName; @endphp
    <aside class="app-sidebar" id="appSidebar">
      <div class="sidebar-top">
        <button type="button" class="sidebar-toggle" id="sidebarToggle"
                aria-label="Expand sidebar" aria-expanded="false" aria-controls="appSidebar">
          <i class="bi bi-list"></i>
        </button>
        <span class="sidebar-heading">Navigation</span>
      </div>
      <div class="sidebar-menu">
        @if($role === 'Admin')
          <a href="{{ route('dashboard') }}"
             class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="bi bi-grid-fill"></i><span class="sidebar-label">Dashboard</span>
          </a>
          <a href="{{ route('admin.userManagement.admins') }}"
             class="sidebar-link {{ request()->routeIs('admin.userManagement.*') ? 'active' : '' }}" title="Users List">
            <i class="bi bi-people-fill"></i><span class="sidebar-label">Users List</span>
          </a>
        @elseif($role === 'Staff')
          <a href="{{ route('dashboard') }}"
             class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" title="Dashboard">
            <i class="bi bi-grid-fill"></i><span class="sidebar-label">Dashboard</span>
          </a>
          <a href="{{ route('staff.documents') }}"
             class="sidebar-link {{ request()->routeIs('staff.documents') ? 'active' : '' }}" title="Documents List">
            <i class="bi bi-folder2-open"></i><span class="sidebar-label">Documents List</span>
          </a>
          <a href="{{ route('staff.document.create') }}"
             class="sidebar-link {{ request()->routeIs('staff.document.create') ? 'active' : '' }}" title="Create Document">
            <i class="bi bi-file-earmark-plus"></i><span class="sidebar-label">Create Document</span>
          </a>
          <a href="{{ route('staff.history') }}"
             class="sidebar-link {{ request()->routeIs('staff.history') ? 'active' : '' }}" title="History">
            <i class="bi bi-clock-history"></i><span class="sidebar-label">History</span>
          </a>
        @endif
      </div>
      <div class="sidebar-footer">
        @if($role === 'Staff')
          <a href="{{ route('staff.settings') }}"
             class="sidebar-user sidebar-profile {{ request()->routeIs('staff.settings*') ? 'active' : '' }}" title="Account Settings">
            <i class="bi bi-person-circle"></i>
            <span class="sidebar-label">{{ auth()->user()->username ?? auth()->user()->firstName }}</span>
          </a>
        @else
          <div class="sidebar-user">
            <i class="bi bi-person-circle"></i>
            <span class="sidebar-label">{{ auth()->user()->username ?? auth()->user()->firstName }}</span>
          </div>
        @endif
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button type="submit" class="sidebar-link sidebar-logout" title="Logout">
            <i class="bi bi-box-arrow-right"></i><span class="sidebar-label">Logout</span>
          </button>
        </form>
      </div>
    </aside>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    @endauth
    @endif
    <main class="main-content">
      @yield('content')
    </main>
    @php
      $flashSuccess = session('success');
      $flashError = session('error');
      $flashMessage = $flashSuccess ?: $flashError;
      $flashIsError = ! $flashSuccess && $flashError;
    @endphp
    @if($flashMessage)
    <div class="modal fade" id="flashModal" tabindex="-1" aria-labelledby="flashModalMessage" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content border-0 shadow-lg text-center" style="border-radius: 20px; background-color: #ffffff; color: #1e293b;">
          <div class="modal-body p-4">
            <div class="mb-2" style="font-size: 2.75rem; line-height: 1; color: {{ $flashIsError ? '#dc2626' : '#16a34a' }};">
              <i class="bi {{ $flashIsError ? 'bi-x-circle-fill' : 'bi-check-circle-fill' }}"></i>
            </div>
            <p id="flashModalMessage" class="fw-semibold mb-1">{{ $flashMessage }}</p>
            @if(session('undo_delete_title'))
              <p class="text-muted small mb-0">{{ session('undo_delete_title') }}</p>
            @endif
            <div class="d-flex justify-content-center gap-2 mt-4">
              @if(session('undo_delete_id') && \Illuminate\Support\Facades\Route::has('staff.document.undo'))
                <form method="POST" action="{{ route('staff.document.undo', session('undo_delete_id')) }}" class="m-0">
                  @csrf
                  <button type="submit" class="btn btn-outline-dark rounded-pill px-4">Undo</button>
                </form>
              @endif
              <button type="button" class="btn rounded-pill px-4 text-white" style="background-color: #001253;" data-bs-dismiss="modal">OK</button>
            </div>
          </div>
        </div>
      </div>
    </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @if($flashMessage)
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        new bootstrap.Modal(document.getElementById('flashModal')).show();
      });
    </script>
    @endif
    <script>
      (function(){
        const root = document.body;
        const key = 'dts-theme';
        function applyTheme(theme){
          if(theme === 'dark'){ root.classList.add('theme-dark'); }
          else { root.classList.remove('theme-dark'); }
        }
        const saved = localStorage.getItem(key);
        const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
        applyTheme(saved ? saved : (prefersDark ? 'dark' : 'light'));
      })();
    </script>
    <script>
      (function () {
        const INTERVAL_MS = 5000;
        const targets = () => document.querySelectorAll('[data-live-refresh][id]');
        if (!targets().length) return;
        let timer = null, busy = false, lastHtml = {};
        function userIsBusy() {
          if (document.querySelector('.modal.show')) return true;
          const el = document.activeElement;
          return !!(el && el.closest && el.closest('[data-live-refresh]') &&
                    /^(INPUT|TEXTAREA|SELECT)$/.test(el.tagName));
        }
        async function refresh() {
          if (busy || document.hidden || userIsBusy()) return;
          busy = true;
          try {
            const res = await fetch(window.location.href, {
              headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' },
              credentials: 'same-origin',
              cache: 'no-store'
            });
            if (!res.ok || res.redirected && new URL(res.url).pathname.startsWith('/login')) {
              if (res.status === 401 || res.status === 419 || res.redirected) window.location.reload();
              return;
            }
            const doc = new DOMParser().parseFromString(await res.text(), 'text/html');
            let changed = false;
            targets().forEach(function (el) {
              const fresh = doc.getElementById(el.id);
              if (!fresh) return;
              const html = fresh.innerHTML;
              if (lastHtml[el.id] === undefined) lastHtml[el.id] = el.innerHTML;
              if (html !== lastHtml[el.id]) {
                el.innerHTML = html;
                lastHtml[el.id] = html;
                changed = true;
              }
            });
            if (changed) document.dispatchEvent(new CustomEvent('live-refreshed'));
          } catch (e) {
            /* network hiccup: try again next tick */
          } finally {
            busy = false;
          }
        }
        function start() { if (!timer) timer = setInterval(refresh, INTERVAL_MS); }
        function stop() { clearInterval(timer); timer = null; }
        document.addEventListener('visibilitychange', function () {
          if (document.hidden) { stop(); } else { refresh(); start(); }
        });
        start();
      })();
    </script>
    <script>
      (function () {
        const body = document.body;
        const sidebar = document.getElementById('appSidebar');
        const toggle = document.getElementById('sidebarToggle');
        const overlay = document.getElementById('sidebarOverlay');

        if (!sidebar || !toggle) return;

        function setSidebarExpanded(expanded, save = true) {
          body.classList.toggle('sidebar-expanded', expanded);
          toggle.setAttribute('aria-expanded', String(expanded));
          toggle.setAttribute('aria-label', expanded ? 'Collapse sidebar' : 'Expand sidebar');

          const icon = toggle.querySelector('i');
          if (icon) {
            icon.className = expanded ? 'bi bi-layout-sidebar-inset' : 'bi bi-list';
          }

          if (save) {
            localStorage.setItem('sidebar-expanded', String(expanded));
          }
        }

        // Check localStorage on load; default to false (collapsed) if not set
        const savedState = localStorage.getItem('sidebar-expanded') === 'true';
        setSidebarExpanded(savedState, false);

        toggle.addEventListener('click', function () {
          const willExpand = !body.classList.contains('sidebar-expanded');
          setSidebarExpanded(willExpand, true);
        });

        if (overlay) {
          overlay.addEventListener('click', function () {
            setSidebarExpanded(false, true);
          });
        }

        document.addEventListener('keydown', function (event) {
          if (event.key === 'Escape') setSidebarExpanded(false, true);
        });
      })();
    </script>
    @stack('scripts')
  </body>
</html>