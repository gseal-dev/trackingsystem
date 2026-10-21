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
                      <button type="button" class="btn-action"
                              data-user-edit="{{ json_encode(['id' => $user->userID, 'username' => $user->username, 'email' => $user->email, 'firstName' => $user->firstName, 'lastName' => $user->lastName, 'roleID' => $user->roleID ?? '', 'editUrl' => route('admin.userManagement.edit', ['type' => 'admins', 'user' => $user->userID])]) }}"
                              title="Edit user"><i class="bi bi-pencil"></i></button>
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

@include('partials.admin-add-user-modal', ['roles' => $roles])
@include('partials.admin-edit-user-modal', ['roles' => $roles])

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
  // Prefill the Edit User modal from the row's data-user-edit attribute
  document.addEventListener('click', function (event) {
    const btn = event.target.closest('[data-user-edit]');
    if (!btn) return;

    const user = JSON.parse(btn.dataset.userEdit);
    const form = document.getElementById('editUserForm');
    const modalEl = document.getElementById('editUserModal');

    if (form) form.action = user.editUrl || '#';
    const idEl = document.getElementById('editUserId');
    if (idEl) idEl.value = user.id;

    const fill = (id, value) => {
      const el = document.getElementById(id);
      if (el) el.value = value === null || value === undefined ? '' : value;
    };

    fill('editUsername', user.username);
    fill('editEmail', user.email);
    fill('editFirstName', user.firstName);
    fill('editLastName', user.lastName);
    fill('editRoleID', user.roleID);
    fill('editPassword', '');
    fill('editPasswordConfirmation', '');

    if (modalEl) {
      if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
      new bootstrap.Modal(modalEl).show();
    }
  });

</script>
@endpush
@endsection
