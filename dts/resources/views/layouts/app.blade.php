<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Document Tracking System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <style>
      :root {
        --brand-dark: #111111;
        --brand-muted: #666666;
        --bg-main: #ffffff;
        --card-bg: #f5f5f5;
        --input-bg: #ffffff;
        --border-color: #e2e8f0;
        --btn-dark: #000000;
        --btn-dark-hover: #222222;
      }

      body {
        min-height: 100vh;
        background-color: var(--bg-main);
        color: var(--brand-dark);
        font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        display: flex;
        flex-direction: column;
        margin: 0;
      }

      /* Centered Layout Wrapper */
      main.main-content {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem 1rem;
      }

      /* Card Styling */
      .login-card, .card-surface {
        background-color: var(--card-bg);
        border: none;
        border-radius: 28px;
        padding: 3.5rem 2.5rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
        width: 100%;
        max-width: 420px;
        margin: auto;
        text-align: center;
      }

      .dashboard-shell {
        width: 100%;
        display: flex;
        justify-content: center;
        padding: 1rem 0 2rem;
      }

      .app-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        width: 100%;
        min-height: 58px;
        margin: 0 auto;
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
        color: #16a34a;
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
        color: #16a34a;
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
        width: min(1100px, 100%);
      }

      .dashboard-container.wide {
        width: min(1200px, 100%);
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
  <body>

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
    @stack('scripts')
  </body>
</html>