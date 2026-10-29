@php
  $currentDefault = \App\Models\Setting::value('default_password') ?? '';
@endphp

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; background: #ffffff; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
      <div class="modal-header" style="background: #fff; border-bottom: 1px solid #cbd5e1; padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <h5 class="modal-title" id="resetPasswordModalLabel" style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #000; display: flex; align-items: center; gap: 0.6rem;">
          <i class="bi bi-key" style="color: #ea3a14; font-size: 1.15rem;"></i> Reset Password
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form id="resetPasswordForm" method="POST">
        @csrf
        <input type="hidden" name="userID" id="resetUserID">
        <div class="modal-body" style="padding: 1.5rem; background: #fff;">
          @if(session('success'))
            <div class="alert alert-success mb-4 rounded-4" style="font-size: 0.875rem;">{{ session('success') }}</div>
          @endif
          <p class="text-muted small mb-3" style="font-size: 0.875rem;">Set a new password for the user. Leave blank to apply the default password (if one is set).</p>
          <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem;">
            <label for="resetPassword" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">New Password @if(!empty($currentDefault))<span style="font-weight: 400; font-size: 0.875rem; color: #64748b;">(optional — uses default if blank)</span>@endif</label>
            <div class="position-relative">
              <input type="password" id="resetPassword" name="password" class="form-control add-user-input" minlength="8" maxlength="72" placeholder="{{ ! empty($currentDefault) ? 'Leave blank to use default' : 'At least 8 characters' }}" style="min-height: 44px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 0.5rem 0.75rem; font-size: 0.925rem; width: 100%;">
              <button type="button" id="toggleResetPasswordBtn" class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" style="z-index: 10;" title="Show or hide"><i class="bi bi-eye"></i></button>
            </div>
            <div class="form-text mt-1" style="font-size: 0.875rem; color: #64748b;">@if(! empty($currentDefault)) Leave blank to apply the default password saved in Default Password settings. Otherwise, enter at least 8 characters.@else Give this password to the user. At least 8 characters.@endif</div>
          </div>
        </div>
        <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e5e7eb; padding: 0.75rem 1.5rem; gap: 0.5rem;">
          <button type="button" class="btn" style="min-height: 46px; border-radius: 4px; border: 1px solid #cbd5e1; color: #475569; background: #ffffff; font-weight: 600;" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn" style="min-height: 46px; border-radius: 4px; background-color: #ea3a14; color: #fff; border: none; font-weight: 600;">Reset Password</button>
        </div>
      </form>
    </div>
  </div>
</div>
<style scoped>
  #resetPasswordModal .modal-content .form-control {
    border-radius: 4px !important;
    border: 1px solid #cbd5e1 !important;
    background: #fff !important;
    color: #000 !important;
  }
  #resetPasswordModal .modal-content .form-control:focus {
    border-color: #000 !important;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.12) !important;
    outline: none !important;
    background: #fff !important;
  }
</style>
<script>
  (function () {
    const modal = document.getElementById('resetPasswordModal');
    if (!modal) return;
    const form = document.getElementById('resetPasswordForm');
    const input = modal.querySelector('#resetPassword');
    const btn = modal.querySelector('#toggleResetPasswordBtn');
    const icon = btn.querySelector('i');
    btn.addEventListener('click', function () {
      const visible = input.type === 'text';
      input.type = visible ? 'password' : 'text';
      icon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
    // Wire the open button (with data-reset-password) to set the form action + show modal
    document.addEventListener('click', function (event) {
      const open = event.target.closest('[data-reset-password]');
      if (!open) return;
      const userID = open.dataset.resetPassword;
      const url = open.dataset.resetUrl || '#';
      if (form) form.action = url;
      const idEl = document.getElementById('resetUserID');
      if (idEl) idEl.value = userID;
      if (modal.parentElement !== document.body) document.body.appendChild(modal);
      new bootstrap.Modal(modal).show();
    });
  })();
</script>
