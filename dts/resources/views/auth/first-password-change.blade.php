@extends('layouts.app')
@section('title', 'Change Your Password')
@section('bodyClass', 'first-pw-page')

@push('head')
<style>
  body.first-pw-page main.main-content { align-items: center; justify-content: center; height: auto; padding-top: 0; }
  .pw-card { max-width: 400px; margin: auto; background: #ffffff; border-radius: 10px; border: 1px solid #e2e8f0; box-shadow: 0 10px 30px rgba(15,23,42,0.10); padding: 1.75rem 1.5rem; text-align: left; }
  .pw-header { text-align: center; margin-bottom: 1.25rem; }
  .pw-header h2 { margin: 0 0 0.4rem; font-size: 1.35rem; font-weight: 800; color: #001253; }
  .pw-header p { margin: 0; color: #64748b; font-size: 0.85rem; }
  .pw-banner { background: #f8fafc; border-left: 4px solid #ea3a14; padding: 0.6rem 0.9rem; border-radius: 6px; margin-bottom: 1rem; font-size: 0.82rem; color: #475569; }
  .pw-banner i { color: #ea3a14; margin-right: 0.5rem; }
  .pw-label { display: block; margin-bottom: 0.35rem; font-weight: 600; font-size: 0.85rem; color: #374151; letter-spacing: 0.02em; }
  .pw-position { position: relative; }
  .pw-position .form-control { padding-right: 2.75rem; }
  .pw-toggle { position: absolute; top: 50%; right: 0.6rem; transform: translateY(-50%); background: transparent; border: 0; cursor: pointer; color: #64748b; padding: 0.25rem; line-height: 1; }
  .pw-toggle:hover { color: #001253; }
  .pw-hint { margin-top: 0.35rem; font-size: 0.75rem; color: #6b7280; }
  .pw-error { margin-top: 0.4rem; color: #dc2626; font-size: 0.78rem; font-weight: 600; }
  .pw-btn { display: block; width: 100%; padding: 0.7rem 1.25rem; background: #001253; color: #ffffff; border: 0; border-radius: 8px; font-weight: 700; font-size: 0.95rem; cursor: pointer; margin-top: 1.5rem; }
  .pw-btn:hover { background: #0a2a8a; }
  .pw-actions { margin-top: 1.25rem; text-align: center; }
  .pw-link { color: #001253; text-decoration: none; font-size: 0.88rem; font-weight: 600; }
  .pw-link:hover { text-decoration: underline; }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="pw-card">
    <div class="pw-header">
      <i class="bi bi-key-fill" style="font-size: 1.75rem; color: #001253; display: block; margin-bottom: 0.4rem;"></i>
      <h2>Change Your Password</h2>
      <p>Welcome, <strong>{{ $fullName ?: $username }}</strong>. Your account was created with a temporary password.</p>
    </div>

    <div class="pw-banner">
      <i class="bi bi-shield-exclamation"></i>
      You must change your password before you can access any other part of the system.
    </div>

    @if($errors->any())
      <div class="alert alert-danger text-start py-2 px-3 rounded-4 mb-3" style="font-size: 0.85rem;">
        @foreach($errors->all() as $error)
          <div>{{ $error }}</div>
        @endforeach
      </div>
    @endif

    <form method="POST" action="{{ route('staff.first-password-change.update') }}" autocomplete="off">
      @csrf

      <div class="mb-2">
        <label class="pw-label" for="current_password">Current Password</label>
        <div class="pw-position">
          <input type="password" id="current_password" name="current_password"
                 class="form-control @error('current_password') is-invalid @enderror"
                 placeholder="Enter your current password" autocomplete="current-password" required>
          <button type="button" class="pw-toggle" data-target="current_password" aria-label="Show or hide password">
            <i class="bi bi-eye-slash"></i>
          </button>
        </div>
        @error('current_password') <div class="pw-error">{{ $message }}</div> @enderror
      </div>

      <div class="mb-2">
        <label class="pw-label" for="password">New Password</label>
        <div class="pw-position">
          <input type="password" id="password" name="password"
                 class="form-control @error('password') is-invalid @enderror"
                 placeholder="Enter your new password" autocomplete="new-password" minlength="8" required>
          <button type="button" class="pw-toggle" data-target="password" aria-label="Show or hide password">
            <i class="bi bi-eye-slash"></i>
          </button>
        </div>
        <div class="pw-hint">At least 8 characters.</div>
        @error('password') <div class="pw-error">{{ $message }}</div> @enderror
      </div>

      <div class="mb-3">
        <label class="pw-label" for="password_confirmation">Confirm New Password</label>
        <div class="pw-position">
          <input type="password" id="password_confirmation" name="password_confirmation"
                 class="form-control" placeholder="Confirm your new password" autocomplete="new-password" required>
          <button type="button" class="pw-toggle" data-target="password_confirmation" aria-label="Show or hide password">
            <i class="bi bi-eye-slash"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="pw-btn"><i class="bi bi-lock-fill me-2"></i> Change Password</button>
    </form>

    <div class="pw-actions" style="margin-top: 0.75rem;">
      <a href="{{ route('logout') }}" class="pw-link">Or, sign out instead</a>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.querySelectorAll('.pw-toggle').forEach(function (btn) {
    btn.addEventListener('click', function () {
      var input = document.getElementById(btn.dataset.target);
      var icon = btn.querySelector('i');
      var show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      icon.className = show ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
  });
</script>
@endpush