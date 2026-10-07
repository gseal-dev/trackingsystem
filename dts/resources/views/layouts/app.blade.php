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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
      :root {
        --brand-dark: #001253;
        --brand-muted: #64748b;
        --bg-main: #f1f5f9;
        --card-bg: #ffffff;
        --input-bg: #ffffff;
        --border-color: #cbd5e1;
        --btn-dark: #001253;
        --btn-dark-hover: #000a33;
        --accent-orange: #ea3a14;
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
        padding-top: 150px;
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

      /* Official DPWH Header Styles */
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
        max-width: 1350px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.9rem 2rem;
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
        margin-top: 0.2rem;
        letter-spacing: 0.03em;
        font-weight: 600;
      }

      .header-banner-stripe {
        height: 6px;
        background: linear-gradient(90deg, #001253 0%, #ea3a14 50%, #001253 100%);
        width: 100%;
      }

      /* Horizontal Navbar Below Header */
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
        max-width: 1350px;
        margin: 0 auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.55rem 2rem;
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

      /* Centered Layout Wrapper */
      main.main-content {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
        position: relative;

      }

      /* Card Styling */
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
        padding: 1rem 0 2rem;
        position: relative;
      }

      /* Keep modals above the fixed header (1100) and navbar (1090) */
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
        padding: 0.7rem max(1rem, calc((100% - 1200px) / 2));
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

      .dashboard-container {
        width: 100%;
        max-width: 1350px;
        margin: 0 auto;
        padding: 0 1rem;
      }

      .dashboard-container.wide {
        width: 100%;
        max-width: 1350px;
        margin: 0 auto;
        padding: 0 1rem;
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

      /* Typography */
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

      /* Custom Input with Side Icon */
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

      /* Pill Input Fields */
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

      /* Action Buttons */
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

      /* Card Footer Links */
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

      /* Tables */
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

      /* Dark Mode Overrides */
      body.theme-dark {
        --brand-dark: #f3f4f6;
        --brand-muted: #9ca3af;
        --bg-main: #0f172a;
        --card-bg: #1e293b;
        --input-bg: #0f172a;
        --border-color: #334155;
        --btn-dark: #ffffff;
        --btn-dark-hover: #e2e8f0;
      }

      body.theme-dark .btn-brand,
      body.theme-dark .btn-custom-dark,
      body.theme-dark .btn-login-submit {
        color: #0f172a;
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
    </style>
    @stack('head')
  </head>
  <body class="{{ (request()->is('/') || request()->routeIs('login')) ? 'login-page' : '' }}">

    @if(!request()->is('/') && !request()->routeIs('login'))
    <header class="official-dpwh-header">
      <div class="header-inner">
        <div class="header-logo-left">
          <img src="{{ asset('images/dpwh-logo.png') }}" alt="DPWH Seal" style="height: 64px; width: 64px; object-fit: contain; mix-blend-mode: multiply;">
        </div>
        <div class="header-titles text-center">
          <div class="header-dept-name">RECORDS MANAGEMENT</div>
          <div class="header-slogan">We love others as we love ourselves</div>
        </div>
        <div class="header-logo-right">
          <img src="{{ asset('images/bagong-pilipinas.webp') }}" alt="Bagong Pilipinas" style="height: 64px; width: 64px; object-fit: contain; mix-blend-mode: multiply;">
        </div>
      </div>
      <div class="header-banner-stripe"></div>
    </header>

    @auth
    <nav class="app-horizontal-navbar">
      <div class="nav-inner">
        <div class="nav-links">
          @php $role = auth()->user()->role?->roleName; @endphp
          @if($role === 'Admin')
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill me-1"></i> Dashboard</a>
            <a href="{{ route('admin.userManagement.admins') }}" class="{{ request()->routeIs('admin.userManagement.*') ? 'active' : '' }}"><i class="bi bi-people-fill me-1"></i> Users List</a>
          @elseif($role === 'Staff')
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-grid-fill me-1"></i> Dashboard</a>
            <a href="{{ route('staff.documents') }}" class="{{ request()->routeIs('staff.documents') ? 'active' : '' }}"><i class="bi bi-folder2-open me-1"></i> Documents List</a>
            <a href="{{ route('staff.history') }}" class="{{ request()->routeIs('staff.history') ? 'active' : '' }}"><i class="bi bi-clock-history me-1"></i> History</a>
          @endif
        </div>
        <div class="nav-user-area">
          <span class="text-muted small me-2" style="color: #cbd5e1 !important;"><i class="bi bi-person-circle me-1"></i> {{ auth()->user()->username ?? auth()->user()->firstName }}</span>
          <form method="POST" action="{{ route('logout') }}" class="m-0 d-inline">
            @csrf
            <button type="submit" class="btn btn-sm px-3 py-1 text-white fw-bold" style="background-color: #ea3a14; border-radius: 50px; font-size: 0.8rem;">
              <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
          </form>
        </div>
      </div>
    </nav>
    @endauth
    @endif

    <main class="main-content">
      @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
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
      // Live list refresh: every few seconds, re-fetch the current page and swap
      // in any element marked with data-live-refresh (it must also have an id).
      (function () {
        const INTERVAL_MS = 5000;
        const targets = () => document.querySelectorAll('[data-live-refresh][id]');
        if (!targets().length) return;

        let timer = null, busy = false, lastHtml = {};

        // Don't swap content out from under the user
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
            // Session expired -> redirected to login; stop polling
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
    @stack('scripts')
  </body>
</html>