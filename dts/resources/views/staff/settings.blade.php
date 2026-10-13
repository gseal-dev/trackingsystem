@extends('layouts.app')
@section('title', 'Account Settings')

@push('head')
<style>
  .set-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin: 1.4rem 0 0;
    padding-bottom: 0.6rem;
    border-bottom: 1px solid #cbd5e1;
    color: #0f172a;
    font-size: 0.9rem;
    font-weight: 700;
  }
  .set-section i { color: rgb(0, 18, 83); }

  .set-panel {
    margin-top: 0.9rem;
    padding: 1.25rem 1.25rem 1.4rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 4px;
  }

  .set-label {
    display: block;
    margin-bottom: 0.3rem;
    color: #475569;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.03em;
  }
  .set-label.upper { text-transform: uppercase; }

  .set-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.9rem;
    color: #1f2937;
    background: #ffffff;
    border: 1px solid #d5dbe2;
    border-radius: 4px;
    box-shadow: none;
  }
  .set-input:focus {
    border-color: rgb(0, 18, 83);
    box-shadow: 0 0 0 3px rgba(0, 18, 83, 0.12);
    outline: 0;
  }
  .set-input[readonly] { background: #f3f4f6; color: #374151; cursor: default; }
  .set-input.is-invalid { border-color: #dc2626; }
  .set-error { margin-top: 0.25rem; color: #dc2626; font-size: 0.78rem; font-weight: 600; }

  .set-pw { position: relative; }
  .set-pw .set-input { padding-right: 2.6rem; }
  .set-pw-toggle {
    position: absolute;
    top: 50%;
    right: 0.5rem;
    transform: translateY(-50%);
    padding: 0.25rem 0.4rem;
    color: #64748b;
    background: transparent;
    border: 0;
    cursor: pointer;
  }
  .set-pw-toggle:hover { color: rgb(0, 18, 83); }

  .set-note {
    margin-top: 1.1rem;
    padding: 0.8rem 1rem;
    color: rgb(0, 18, 83);
    font-size: 0.85rem;
    background: #e8f1fb;
    border: 1px solid #bcd3ee;
    border-radius: 4px;
  }

  .set-hint { margin-top: 0.25rem; color: #6b7280; font-size: 0.72rem; }

  .set-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    margin-top: 0.4rem;
    padding: 0.55rem 1.2rem;
    font-size: 0.88rem;
    font-weight: 700;
    color: #ffffff;
    background: rgb(0, 18, 83);
    border: 0;
    border-radius: 4px;
    cursor: pointer;
  }
  .set-btn:hover { background: #0a2a8a; }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
    <div class="page-card">
      <div class="page-card-header"><i class="bi bi-gear-fill"></i> Account Settings</div>

      {{-- Read-only account details --}}
      <div class="set-section"><i class="bi bi-person-fill"></i> Account Information</div>
      <div class="set-panel">
        <div class="row g-3">
          @foreach($account as $label => $value)
            <div class="col-12 col-md-6">
              <label class="set-label upper">{{ $label }}</label>
              <input type="text" class="set-input" value="{{ $value ?: 'N/A' }}" readonly tabindex="-1">
            </div>
          @endforeach
        </div>

        <div class="set-note">
          <i class="bi bi-info-circle-fill me-2"></i><strong>Note:</strong>
          Username, name, email, department and role changes must be requested through the system administrator.
        </div>
      </div>

      {{-- The only thing staff can change --}}
      <div class="set-section"><i class="bi bi-key-fill"></i> Change Password</div>
      <div class="set-panel">
        <form method="POST" action="{{ route('staff.settings.password') }}" autocomplete="off" id="passwordForm">
          @csrf

          <div class="mb-3">
            <label class="set-label" for="current_password">Current Password</label>
            <div class="set-pw">
              <input type="password" id="current_password" name="current_password"
                     class="set-input @error('current_password') is-invalid @enderror"
                     placeholder="Enter your current password" autocomplete="current-password" required>
              <button type="button" class="set-pw-toggle" data-target="current_password" aria-label="Show or hide password">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
            @error('current_password') <div class="set-error">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="set-label" for="password">New Password</label>
            <div class="set-pw">
              <input type="password" id="password" name="password"
                     class="set-input @error('password') is-invalid @enderror"
                     placeholder="Enter your new password" autocomplete="new-password" minlength="8" required>
              <button type="button" class="set-pw-toggle" data-target="password" aria-label="Show or hide password">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
            <div class="set-hint">At least 8 characters.</div>
            @error('password') <div class="set-error">{{ $message }}</div> @enderror
          </div>

          <div class="mb-3">
            <label class="set-label" for="password_confirmation">Confirm New Password</label>
            <div class="set-pw">
              <input type="password" id="password_confirmation" name="password_confirmation"
                     class="set-input" placeholder="Confirm your new password" autocomplete="new-password" required>
              <button type="button" class="set-pw-toggle" data-target="password_confirmation" aria-label="Show or hide password">
                <i class="bi bi-eye-slash"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="set-btn"><i class="bi bi-lock-fill"></i> Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  // show / hide password
  document.querySelectorAll('.set-pw-toggle').forEach(function (btn) {
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
@endsection
