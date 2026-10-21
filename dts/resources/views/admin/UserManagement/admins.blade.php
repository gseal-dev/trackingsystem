@extends('layouts.app')

@section('title', 'Users List')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>


  .staff-layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 2rem;
    align-items: start;
  }

  .staff-sidebar {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 1.5rem;
    position: sticky;
    top: 2rem;
  }

  .staff-nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .staff-nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    color: #4b5563;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
  }

  .staff-nav-link:hover {
    background: #f3f4f6;
    color: #111827;
  }

  .staff-nav-link.active {
    background: #001253;
    color: #fff;
  }


  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* margin-bottom: 2rem; */
    flex-wrap: wrap;
    gap: 1rem;
  }

  .section-title {
    font-size: 1.875rem;
    font-weight: 800;
    letter-spacing: -0.025em;
    margin: 0;
  }

  .table-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .search-box {
    position: relative;
    max-width: 320px;
    width: 100%;
  }

  .search-box input {
    padding-left: 2.5rem;
    height: 44px;
  }

  .search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
  }

  .actions-cell {
    display: flex;
    gap: 0.5rem;
  }

  .btn-action {
    width: 36px;
    height: 36px;
    padding: 0;
    display: grid;
    place-items: center;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #4b5563;
    transition: all 0.2s;
  }

  .btn-action:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
  }

  .table th {
    text-transform: uppercase;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    color: #646771;
  }

  @media (max-width: 1024px) {
    .staff-layout {
      grid-template-columns: 1fr;
    }
    .staff-sidebar {
      position: static;
    }
    .staff-nav-list {
      flex-direction: row;
      overflow-x: auto;
    }
  }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
      <div class="page-card">
        <div class="page-card-header"><i class="bi bi-people-fill"></i> Users List</div>
        <div class="section-header">
          <div>
            <p class="text-muted">Manage and track all system users</p>
          </div>
        </div>

        <div class="table-toolbar">
          <form method="GET" action="{{ route('admin.userManagement.admins') }}" class="d-flex align-items-center gap-2 flex-wrap flex-grow-1 me-3">
            <div class="search-box">
              <i class="bi bi-search"></i>
              <input type="text" id="admin-user-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search users..." autocomplete="off">
            </div>
            @if(request('q'))
              <a href="{{ route('admin.userManagement.admins') }}" class="btn btn-outline-secondary px-3 py-2" style="border-radius: 50px;">Reset</a>
            @endif
            @include('partials.per-page-select', ['paginator' => $users, 'id' => 'users-per-page'])
          </form>
          <div class="d-flex gap-2">
            <a href="{{ route('admin.userManagement.export', request()->only('q')) }}" class="btn btn-outline-secondary px-3 py-2" id="export-users" style="border-radius: 50px;" title="Exports every user matching the search"><i class="bi bi-download me-1"></i> Export Excel</a>
            <button type="button" class="btn btn-dark px-4 py-2" style="background-color: #ea3a14; color: #ffffff; border-radius: 50px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#addUserModal" title="Add user"><i class="bi bi-person-plus me-1"></i> Add User</button>
          </div>
        </div>

        <div class="border rounded-2 overflow-hidden">
          <div class="table-responsive">
          <table class="table table-hover table-sm align-middle">
            <thead class="bg-light">
              <tr>
                <th class="border-0">Name</th>
                <th class="border-0">Email</th>
                <th class="border-0">Role</th>
                <th class="border-0">Password</th>
                <th class="border-0 text-end">Actions</th>
              </tr>
            </thead>
            <tbody id="admin-users-body">
              @forelse($users as $user)
                <tr>
                  <td class="fw-bold">{{ $user->firstName }} {{ $user->lastName }}</td>
                  <td>{{ $user->email }}</td>
                  <td><span class="badge bg-light text-dark border">{{ $user->role->roleName ?? 'Admin' }}</span></td>
                  <td><span class="text-muted" style="letter-spacing: 0.12em;">********</span></td>
                  <td>
                    <div class="actions-cell justify-content-end">
                      <a class="btn-action" href="{{ route('admin.userManagement.editForm', ['type' => 'admins', 'user' => $user]) }}" title="Edit user"><i class="bi bi-pencil"></i></a>
                      <form action="{{ route('admin.userManagement.delete', ['type' => 'admins', 'user' => $user]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                        @csrf
                        <button type="submit" class="btn-action text-danger border-0 bg-transparent" title="Delete user"><i class="bi bi-trash"></i></button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center py-5 text-muted">
                    <i class="bi bi-people display-4 mb-3 d-block"></i>
                    No users found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
          </div>
        </div>

        @include('partials.list-pager', ['paginator' => $users, 'noun' => 'users'])
      </div>
    </div>
  </div>

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

@push('scripts')
<script>
  // Search runs on the server (it has to look through every page, not just the rows on screen)
  (function () {
    const input = document.getElementById('admin-user-search');
    let timer = null;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(function () { input.form.submit(); }, 450);
    });
    if (input.value) {
      input.focus({ preventScroll: true });
      input.setSelectionRange(input.value.length, input.value.length);
    }
  })();
</script>
@endpush
@endsection
