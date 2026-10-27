<!-- Lock Confirmation Modal -->
<div class="modal fade" id="lockConfirmModal" tabindex="-1" aria-labelledby="lockConfirmModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content" style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #ffffff; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
      <div class="modal-header" style="background: #fff; border-bottom: 1px solid #cbd5e1; padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <h5 class="modal-title" id="lockConfirmModalLabel" style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #000; display: flex; align-items: center; gap: 0.6rem;">
          <i class="bi bi-lock-fill" style="color: #ea3a14; font-size: 1.15rem;"></i> Confirm Lock
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" id="lockConfirmForm">
        @csrf
        <input type="hidden" name="_lockUrl">
        <input type="password" name="confirm_password" style="display: none;">
        <div class="modal-body" style="padding: 1.5rem; background: #fff;">
          <p class="text-muted small mb-3">Enter your password to confirm locking this account.</p>
          <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem;">
            <label for="lockConfirmPassword" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">Your Password</label>
            <div class="position-relative">
              <input type="password" id="lockConfirmPassword" name="confirm_password" class="form-control" minlength="8" maxlength="72" required autocomplete="current-password" placeholder="Type your password" style="min-height: 44px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.5rem 0.75rem; font-size: 0.925rem; width: 100%;">
              <button type="button" id="toggleLockConfirmPasswordBtn" class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" style="z-index: 10; border-radius: 4px;" title="Show or hide"><i class="bi bi-eye"></i></button>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e5e7eb; padding: 0.75rem 1.5rem; display: flex; flex-wrap: nowrap; gap: 0.5rem; justify-content: flex-end;">
          <button type="button" class="btn" style="flex: 1; min-width: 0; min-height: 46px; height: 46px; border-radius: 4px; border: 1px solid #cbd5e1; color: #475569; background: #ffffff; font-weight: 600;" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn" id="lockConfirmOkBtn" style="flex: 1; min-width: 0; min-height: 46px; height: 46px; border-radius: 4px; background-color: #ea3a14; color: #fff; border: none; font-weight: 600; font-size: 0.85rem; padding: 0 0.5rem;"><i class="bi bi-lock-fill me-1"></i> Lock Account</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style scoped>
  #lockConfirmModal .modal-content .form-control {
    border-radius: 4px !important;
    border: 1px solid #cbd5e1 !important;
    background: #fff !important;
    color: #000 !important;
  }
  #lockConfirmModal .modal-content .form-control:focus {
    border-color: #000 !important;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.12) !important;
    outline: none !important;
    background: #fff !important;
  }
</style>

<script>
  (function () {
    const modalEl = document.getElementById('lockConfirmModal');
    if (!modalEl) return;

    const form = document.getElementById('lockConfirmForm');
    const lockUrlInput = modalEl.querySelector('[name="_lockUrl"]');
    const pwdInput = modalEl.querySelector('#lockConfirmPassword');
    const toggleBtn = modalEl.querySelector('#toggleLockConfirmPasswordBtn');
    const okBtn = document.getElementById('lockConfirmOkBtn');

    let pendingLockUrl = null;

    // Called from the table row: open modal and remember the lock URL
    window.openLockConfirm = function (lockUrl) {
      pendingLockUrl = lockUrl;
      if (lockUrlInput) lockUrlInput.value = lockUrl;
      if (pwdInput) pwdInput.value = '';
      new bootstrap.Modal(modalEl).show();
    };

    // Clear password and pending URL when modal closes
    modalEl.addEventListener('hidden.bs.modal', function () {
      if (pwdInput) pwdInput.value = '';
      pendingLockUrl = null;
    });

    toggleBtn.addEventListener('click', function () {
      const visible = pwdInput.type === 'text';
      pwdInput.type = visible ? 'password' : 'text';
      toggleBtn.querySelector('i').className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
    });

    form.addEventListener('submit', function (e) {
      if (!pendingLockUrl) {
        e.preventDefault();
        return;
      }
      // Submit to the lock URL with the confirmation password
      form.action = pendingLockUrl;
      okBtn.disabled = true;
      okBtn.textContent = 'Locking...';
    });
  })();
</script>