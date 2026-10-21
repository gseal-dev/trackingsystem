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
              <label for="password" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">Password</label>
              <input type="password" id="password" name="password" class="form-control add-user-input" required minlength="8" style="width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #000; padding: 0.5rem 0.75rem; font-size: 0.925rem;">
            </div>
            <div style="border-bottom: 1px solid #cbd5e1; padding-bottom: 0.75rem; grid-column: 1 / -1;">
              <label for="password_confirmation" style="display: block; margin-bottom: 0.45rem; color: #64748b; font-size: 0.875rem; font-weight: 600;">Confirm Password</label>
              <input type="password" id="password_confirmation" name="password_confirmation" class="form-control add-user-input" required minlength="8" style="width: 100%; min-height: 44px; border: 1px solid #cbd5e1; border-radius: 6px; background: #fff; color: #000; padding: 0.5rem 0.75rem; font-size: 0.925rem;">
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
  #addUserModal .modal-content .form-control:focus,
  #addUserModal .modal-content .form-select:focus,
  #addUserModal .modal-content .add-user-input:focus {
    border-color: #000 !important;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.12) !important;
    outline: none !important;
    background: #fff !important;
  }
</style>

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
