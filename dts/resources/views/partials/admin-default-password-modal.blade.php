@php
  $currentDefault = \App\Models\Setting::value('default_password') ?? '';
@endphp

<!-- Default Password Modal -->
<div class="modal fade" id="defaultPasswordModal" tabindex="-1" aria-labelledby="defaultPasswordModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border: 1px solid #cbd5e1; border-radius: 4px; overflow: hidden; background: #ffffff; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
      <div class="modal-header" style="background: #fff; border-bottom: 1px solid #cbd5e1; padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <h5 class="modal-title" id="defaultPasswordModalLabel" style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #000; display: flex; align-items: center; gap: 0.6rem;">
          <i class="bi bi-key" style="color: #ea3a14; font-size: 1.15rem;"></i> Default Password
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.userManagement.defaultPassword.save') }}">
        @csrf
        <div class="modal-body" style="padding: 1.5rem; background: #fff;">
          @if(session('success'))
            <div class="alert alert-success mb-4 rounded-4" style="font-size: 0.875rem;">{{ session('success') }}</div>
          @endif
          <p class="text-muted small mb-3" style="font-size: 0.875rem;">Set a default password that will be applied to all newly created users when no password is typed during creation.</p>
          <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem;">
            <label for="defaultPassword" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">Default Password</label>
            <div class="position-relative">
              <input type="password" id="defaultPassword" name="default_password" class="form-control add-user-input" minlength="8" maxlength="72" value="{{ old('default_password', $currentDefault) }}" placeholder="Leave blank to clear" style="min-height: 44px; border: 1px solid #cbd5e1; border-radius: 4px; padding: 0.5rem 0.75rem; font-size: 0.925rem; width: 100%;">
              <button type="button" id="toggleDefaultPasswordBtn" class="btn btn-outline-secondary position-absolute top-50 end-0 translate-middle-y me-2" style="z-index: 10;" title="Show or hide"><i class="bi bi-eye"></i></button>
            </div>
            <div class="form-text mt-1" style="font-size: 0.875rem; color: #64748b;">Leave blank to remove the default. Users created without a typed password will need a password set manually.</div>
          </div>
        </div>
        <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e5e7eb; padding: 0.75rem 1.5rem; gap: 0.5rem;">
          <button type="button" class="btn" style="min-height: 46px; border-radius: 2px; border: 1px solid #cbd5e1; color: #475569; background: #ffffff; font-weight: 600;" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn" style="min-height: 46px; border-radius: 2px; background-color: #ea3a14; color: #fff; border: none; font-weight: 600;">Save Default Password</button>
        </div>
      </form>
    </div>
  </div>
</div>
<style scoped>
  #defaultPasswordModal .modal-content .form-control {
    border-radius: 4px !important;
    border: 1px solid #cbd5e1 !important;
    background: #fff !important;
    color: #000 !important;
  }
  #defaultPasswordModal .modal-content .form-control:focus {
    border-color: #000 !important;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.12) !important;
    outline: none !important;
    background: #fff !important;
  }
</style>
<script>
  (function () {
    const modal = document.getElementById('defaultPasswordModal');
    if (!modal) return;
    const input = modal.querySelector('#defaultPassword');
    const btn = modal.querySelector('#toggleDefaultPasswordBtn');
    const icon = btn.querySelector('i');
    btn.addEventListener('click', function () {
      const visible = input.type === 'text';
      input.type = visible ? 'password' : 'text';
      icon.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
    });
  })();
</script>
