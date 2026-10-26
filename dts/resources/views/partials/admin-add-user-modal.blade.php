@php
  $hasDefaultPassword = ! empty($defaultPassword ?? '');
@endphp

<!-- Add User Modal (styled to match the document view modal) -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="border: 1px solid #cbd5e1; border-radius: 8px; overflow: hidden; background: #ffffff; box-shadow: 0 20px 50px rgba(0,0,0,0.25);">
      <div class="modal-header" style="background: #fff; border-bottom: 1px solid #cbd5e1; padding: 1rem 1.5rem; display: flex; align-items: center; justify-content: space-between;">
        <h5 class="modal-title" id="addUserModalLabel" style="margin: 0; font-size: 1.05rem; font-weight: 700; color: #000; display: flex; align-items: center; gap: 0.6rem;">
          <i class="bi bi-file-earmark-person-fill" style="color: #ea3a14; font-size: 1.15rem;"></i> Add New User
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.userManagement.add', ['type' => 'admins']) }}">
        @csrf
        <div class="modal-body" style="padding: 1.5rem; background: #fff;">
          @if($errors->any())
            <div class="alert alert-danger mb-4 rounded-4" style="border: 1px solid #fecaca; background: #fffafa;">
              @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          @endif
          <div style="display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 1rem;">
            <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem;">
              <label for="username" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">Username</label>
              <input type="text" id="username" name="username" class="form-control add-user-input" value="{{ old('username') }}" required autofocus style="width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #000; padding: 0.5rem 0.75rem; font-size: 0.925rem;">
            </div>
            <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem;">
              <label for="email" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">Email</label>
              <input type="email" id="email" name="email" class="form-control add-user-input" value="{{ old('email') }}" required style="width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #000; padding: 0.5rem 0.75rem; font-size: 0.925rem;">
            </div>
            <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem;">
              <label for="firstName" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">First Name</label>
              <input type="text" id="firstName" name="firstName" class="form-control add-user-input" value="{{ old('firstName') }}" required style="width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #000; padding: 0.5rem 0.75rem; font-size: 0.925rem;">
            </div>
            <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem;">
              <label for="lastName" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">Last Name</label>
              <input type="text" id="lastName" name="lastName" class="form-control add-user-input" value="{{ old('lastName') }}" required style="width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #000; padding: 0.5rem 0.75rem; font-size: 0.925rem;">
            </div>
            <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem; grid-column: 1 / -1;">
              <label for="roleID" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">Role</label>
              <select id="roleID" name="roleID" class="form-select add-user-input" required style="width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #000; padding: 0.5rem 0.75rem; font-size: 0.925rem; appearance: none; -webkit-appearance: none;">
                <option value="" disabled {{ old('roleID') ? '' : 'selected' }}>Select Role</option>
                @foreach($roles ?? [] as $role)
                  <option value="{{ $role->roleID }}" @selected(old('roleID') == $role->roleID)>{{ $role->roleName }}</option>
                @endforeach
              </select>
            </div>
            <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem; grid-column: 1 / -1;">
              <label for="password" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">
                Password @if($hasDefaultPassword)
                  <span style="font-weight: 400; font-size: 0.875rem; color: #64748b;">(optional — uses default if blank)</span>
                @endif
              </label>
              <div class="input-group">
                <input type="password" id="password" name="password" class="form-control add-user-input" @if(!$hasDefaultPassword) required @endif minlength="8" maxlength="72" autocomplete="new-password" placeholder="{{ $hasDefaultPassword ? 'Leave blank to use default' : 'At least 8 characters' }}" aria-describedby="defaultPasswordHint" style="min-height: 44px; border: 1px solid #cbd5e1; padding: 0.5rem 0.75rem; font-size: 0.925rem;">
                <button type="button" id="togglePasswordBtn" class="btn btn-outline-secondary" title="Show or hide the password" aria-label="Show or hide the password"><i class="bi bi-eye"></i></button>
                <button type="button" id="generatePasswordBtn" class="btn btn-outline-secondary" title="Make up a random password"><i class="bi bi-shuffle me-1"></i> Generate</button>
                <button type="button" id="copyPasswordBtn" class="btn btn-outline-secondary" title="Copy the password" aria-label="Copy the password"><i class="bi bi-clipboard"></i></button>
              </div>
              <div id="defaultPasswordHint" class="form-text" aria-live="polite">@if($hasDefaultPassword) Leave blank to apply the default password set in Default Password settings. Otherwise, enter at least 8 characters.@else Give this password to the new user. At least 8 characters.@endif</div>
            </div>
          </div>
        </div>
        <div class="modal-footer" style="background: #f8f9fa; border-top: 1px solid #e5e7eb; padding: 0.75rem 1.5rem; gap: 0.5rem;">
          <button type="button" class="btn" style="min-height: 46px; border-radius: 6px; border: 1px solid #cbd5e1; color: #475569; background: #ffffff; font-weight: 600;" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn" style="min-height: 46px; border-radius: 6px; background-color: #000; color: #fff; border: none; font-weight: 600;"><i class="bi bi-person-plus me-1"></i> Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>
<style scoped>
  /* Keep inputs pill-free and on-brand even where global CSS forces 50px */
  #addUserModal .modal-content .form-control,
  #addUserModal .modal-content .form-select,
  #addUserModal .modal-content .add-user-input {
    border-radius: 6px !important;
    border: 1px solid #cbd5e1 !important;
    background: #fff !important;
    color: #000 !important;
  }
  #addUserModal .modal-content .input-group > .form-control {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
  }
  #addUserModal .modal-content .input-group > .btn {
    border: 1px solid #cbd5e1;
    color: #475569;
    background: #fff;
    font-weight: 600;
  }
  #addUserModal .modal-content .input-group > .btn:hover { background: #f1f5f9; color: #000; }
  #addUserModal .modal-content .form-control:focus,
  #addUserModal .modal-content .form-select:focus,
  #addUserModal .modal-content .add-user-input:focus {
    border-color: #000 !important;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.12) !important;
    outline: none !important;
    background: #fff !important;
  }
</style>

<script>
  // Default password helpers for the "Add New User" form
  (function () {
    const modalEl = document.getElementById('addUserModal');
    if (!modalEl) return;

    const input = modalEl.querySelector('#password');
    const toggleBtn = modalEl.querySelector('#togglePasswordBtn');
    const generateBtn = modalEl.querySelector('#generatePasswordBtn');
    const copyBtn = modalEl.querySelector('#copyPasswordBtn');
    const hint = modalEl.querySelector('#defaultPasswordHint');
    const roleSelect = modalEl.querySelector('#roleID');

    // No look-alike characters (0/O, 1/l/I), so the password is easy to read out or type from a note
    const UPPER = 'ABCDEFGHJKLMNPQRSTUVWXYZ', LOWER = 'abcdefghijkmnpqrstuvwxyz', DIGITS = '23456789';
    const LENGTH = 12;

    // an unbiased random integer in [0, max) from the browser's secure random source
    function randomInt(max) {
      const limit = Math.floor(0x100000000 / max) * max;
      const buffer = new Uint32Array(1);
      do { crypto.getRandomValues(buffer); } while (buffer[0] >= limit);
      return buffer[0] % max;
    }
    const pick = (chars) => chars[randomInt(chars.length)];

    function generatePassword() {
      const all = UPPER + LOWER + DIGITS;
      const chars = [pick(UPPER), pick(LOWER), pick(DIGITS)];          // always one of each kind
      while (chars.length < LENGTH) chars.push(pick(all));
      for (let i = chars.length - 1; i > 0; i--) {                      // shuffle
        const j = randomInt(i + 1);
        [chars[i], chars[j]] = [chars[j], chars[i]];
      }
      return chars.join('');
    }

    function setVisible(visible) {
      input.type = visible ? 'text' : 'password';
      toggleBtn.querySelector('i').className = visible ? 'bi bi-eye-slash' : 'bi bi-eye';
    }

    function flash(button, iconClass, label) {
      const icon = button.querySelector('i');
      const original = icon.className;
      icon.className = iconClass;
      button.title = label;
      setTimeout(function () { icon.className = original; }, 1500);
    }

    toggleBtn.addEventListener('click', function () { setVisible(input.type === 'password'); });

    generateBtn.addEventListener('click', function () {
      input.value = generatePassword();
      setVisible(true);                  // the admin has to see it to pass it on
      input.focus();
      input.select();
    });

    copyBtn.addEventListener('click', function () {
      if (!input.value) return;
      const done = function () { flash(copyBtn, 'bi bi-clipboard-check', 'Copied'); };
      if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(input.value).then(done);
      } else {                           // plain http:// on the office network has no clipboard API
        const wasHidden = input.type === 'password';
        if (wasHidden) input.type = 'text';
        input.select();
        document.execCommand('copy');
        if (wasHidden) input.type = 'password';
        done();
      }
    });

    // Say what the new user can do with the password, depending on the chosen role
    const HINTS = {
      staff: 'Staff can change this password themselves after logging in (Settings).',
      admin: 'Admins cannot change their own password from Settings. To change it later, use Edit in Users List.',
      none: 'Give this password to the new user. At least 8 characters.',
    };
    function updateHint() {
      const role = roleSelect.selectedOptions[0] ? roleSelect.selectedOptions[0].textContent.trim().toLowerCase() : '';
      hint.textContent = HINTS[role] || HINTS.none;
    }
    roleSelect.addEventListener('change', updateHint);
    updateHint();

    // Do not leave a password lying around in the page after closing the form
    modalEl.addEventListener('hidden.bs.modal', function () {
      input.value = '';
      setVisible(false);
    });
  })();
</script>

@if($errors->any())
<script>
  function showAddUserModalOnError() {
    var modalEl = document.getElementById('addUserModal');
    if (modalEl) {
      var addUserModal = new bootstrap.Modal(modalEl);
      addUserModal.show();
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', showAddUserModalOnError);
  } else {
    showAddUserModalOnError();
  }
</script>
@endif
