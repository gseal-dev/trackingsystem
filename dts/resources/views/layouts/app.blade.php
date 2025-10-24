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
        --brand-dark:#1f2937;/* gray-800 */
        --brand:#0f5132;/* success dark */
        --brand-600:#198754;/* success */
        --text-muted:#6b7280;/* gray-500 */
        --surface:#ffffff;
        --surface-2:#f8fafc;/* light */
        --ring:rgba(25,135,84,.25);
      }
      body{background:radial-gradient(1200px 600px at 10% -10%, #e8f6ef 0%, transparent 60%), radial-gradient(1200px 600px at 110% 10%, #eef6ff 0%, transparent 60%); color:var(--brand-dark);} 
      .page-container{padding-top:32px;padding-bottom:48px;}
      .page-header h1{color:var(--brand-dark);font-weight:700;letter-spacing:.2px;}
      .toolbar{display:flex;align-items:center;justify-content:space-between;gap:16px;padding:14px 18px;background:var(--surface);border:1px solid #e5e7eb;border-radius:12px;box-shadow:0 6px 20px rgba(0,0,0,.04);} 
      .logout-btn{background:var(--brand-600);color:#fff;border:none;padding:10px 16px;border-radius:10px;font-weight:600;display:inline-flex;align-items:center;gap:8px;transition:transform .06s ease, box-shadow .2s ease, background .2s ease;box-shadow:0 2px 6px var(--ring);} 
      .logout-btn:hover{background:#157347;} .logout-btn:active{transform:translateY(1px);} 
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
        --brand-dark:#e5e7eb; /* gray-200 */
        --text-muted:#9ca3af; /* gray-400 */
        --surface:#111827; /* gray-900 */
        --surface-2:#0b1726; /* deep slate */
        --ring:rgba(16,185,129,.25);
      }
      body.theme-dark{background:radial-gradient(1200px 600px at 10% -10%, #0b2a20 0%, transparent 60%), radial-gradient(1200px 600px at 110% 10%, #0b1a2a 0%, transparent 60%);} 
      body.theme-dark .page-header h1{color:var(--brand-dark);} 
      body.theme-dark .text-muted{color:var(--text-muted) !important;}
      body.theme-dark .table, body.theme-dark .table td, body.theme-dark .table th{color:var(--brand-dark);} 
      body.theme-dark .table-clean tbody td, body.theme-dark .table-clean thead th{border-color:#374151;} 
      body.theme-dark .dash-action{background:#0b2a20;color:#86efac;border-color:rgba(34,197,94,.35);} 
      body.theme-dark .toolbar{background:var(--surface);border-color:#374151;box-shadow:0 8px 24px rgba(0,0,0,.35);} 
      body.theme-dark .card-surface{background:var(--surface);border-color:#374151;box-shadow:0 12px 28px rgba(0,0,0,.35);} 
      body.theme-dark .table-clean thead th{background:#111827;border-bottom:1px solid #374151;color:#e5e7eb;} 
      body.theme-dark .form-control, body.theme-dark .form-select{background:#0f172a;border-color:#374151;color:#e5e7eb;} 
      body.theme-dark .btn-outline-success{color:#22c55e;border-color:#22c55e;} 
      body.theme-dark .btn-outline-success:hover{background:#16a34a;color:#fff;border-color:#16a34a;} 

      /* Theme toggle button */
      .theme-toggle{position:fixed;right:16px;top:16px;z-index:1050;background:var(--surface);border:1px solid #e5e7eb;border-radius:999px;padding:8px 12px;display:inline-flex;align-items:center;gap:8px;color:var(--brand-dark);box-shadow:0 8px 24px rgba(0,0,0,.08);} 
      .theme-toggle:hover{border-color:#d1d5db;} 
      body.theme-dark .theme-toggle{background:var(--surface);border-color:#374151;color:#e5e7eb;box-shadow:0 10px 28px rgba(0,0,0,.35);} 
    </style>
    @stack('head')
  </head>
  <body>
    <button id="themeToggle" class="theme-toggle" type="button" aria-label="Toggle dark mode">
      <i class="bi bi-moon-stars" id="themeIcon"></i>
      <span class="d-none d-sm-inline" id="themeLabel">Dark</span>
    </button>
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