@extends('layouts.app')

@section('title', 'Users List')

@push('head')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
  .dashboard-shell {
    width: 100%;
    min-height: calc(100vh - 100px);
    padding: clamp(1rem, 2vw, 2rem);
  }

  .dashboard-container {
    width: 100%;
    max-width: 1350px;
    margin: 0 auto;
    padding: 0 1rem;
  }

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

  .staff-main-content {
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
    padding: 0;
    min-height: 600px;
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
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
      <main class="staff-main-content" style="background: transparent; border: none; border-radius: 0; padding: 0; min-height: 600px; width: 100%;">
        <div class="section-header">
          <div>
            <h1 class="section-title">Users List</h1>
            <p class="text-muted">Manage and track all system users</p>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="table-toolbar">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="admin-user-search" class="form-control" placeholder="Search users...">
          </div>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary px-3 py-2" id="export-users" style="border-radius: 50px;"><i class="bi bi-download me-1"></i> Export Excel</button>
            <button type="button" class="btn btn-dark px-4 py-2" style="background-color: #ea3a14; color: #ffffff; border-radius: 50px; font-weight: 600;" data-bs-toggle="modal" data-bs-target="#addUserModal" title="Add user"><i class="bi bi-person-plus me-1"></i> Add User</button>
          </div>
        </div>

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
                <tr data-user-search="{{ strtolower(($user->firstName ?? '') . ' ' . ($user->lastName ?? '') . ' ' . $user->email . ' ' . ($user->role->roleName ?? 'Admin')) }}">
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
      </main>
    </div>
  </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
      <div class="modal-header border-0 pb-0 px-4 pt-4">
        <h5 class="modal-title fw-bold" id="addUserModalLabel">Add New User</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('admin.userManagement.add', ['type' => 'admins']) }}">
        @csrf
        <div class="modal-body px-4 py-3">
          @if($errors->any())
            <div class="alert alert-danger mb-3">
              @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
              @endforeach
            </div>
          @endif
          <div class="mb-3">
            <label for="username" class="form-label fw-bold small text-muted">Username <span class="text-danger">*</span></label>
            <input type="text" id="username" name="username" class="form-control" value="{{ old('username') }}" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label fw-bold small text-muted">Email <span class="text-danger">*</span></label>
            <input type="email" id="email" name="email" class="form-control" value="{{ old('email') }}" required>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="firstName" class="form-label fw-bold small text-muted">First Name <span class="text-danger">*</span></label>
              <input type="text" id="firstName" name="firstName" class="form-control" value="{{ old('firstName') }}" required>
            </div>
            <div class="col-md-6 mb-3">
              <label for="lastName" class="form-label fw-bold small text-muted">Last Name <span class="text-danger">*</span></label>
              <input type="text" id="lastName" name="lastName" class="form-control" value="{{ old('lastName') }}" required>
            </div>
          </div>
          <div class="mb-3">
            <label for="roleID" class="form-label fw-bold small text-muted">Role <span class="text-danger">*</span></label>
            <select id="roleID" name="roleID" class="form-select" required>
              <option value="" disabled {{ old('roleID') ? '' : 'selected' }}>Select Role</option>
              @foreach($roles ?? [] as $role)
                <option value="{{ $role->roleID }}" @selected(old('roleID') == $role->roleID)>{{ $role->roleName }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="password" class="form-label fw-bold small text-muted">Password <span class="text-danger">*</span></label>
            <input type="password" id="password" name="password" class="form-control" required minlength="8">
          </div>
          <div class="mb-3">
            <label for="password_confirmation" class="form-label fw-bold small text-muted">Confirm Password <span class="text-danger">*</span></label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required minlength="8">
          </div>
        </div>
        <div class="modal-footer border-0 px-4 pb-4">
          <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal" style="border-radius: 50px;">Cancel</button>
          <button type="submit" class="btn btn-dark px-4" style="background-color: #000; color: #fff; border-radius: 50px;">Add User</button>
        </div>
      </form>
    </div>
  </div>
</div>

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
  document.getElementById('admin-user-search').addEventListener('input', function () {
    const search = this.value.trim().toLowerCase();
    document.querySelectorAll('#admin-users-body tr[data-user-search]').forEach(function (row) {
      row.hidden = !row.dataset.userSearch.includes(search);
    });
  });

  document.getElementById('export-users').addEventListener('click', function () {
    const rows = [['Name', 'Email', 'Role']];
    document.querySelectorAll('#admin-users-body tr').forEach(function (row) {
      if (!row.hidden && row.querySelectorAll('td').length >= 3) {
        rows.push(Array.from(row.querySelectorAll('td')).slice(0, 3).map(function (cell) { return cell.innerText.trim(); }));
      }
    });
    const csv = rows.map(function (row) { return row.map(function (cell) { return '"' + cell.replaceAll('"', '""') + '"'; }).join(','); }).join('\n');
    const link = document.createElement('a');
    link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
    link.download = 'admin-users.csv';
    link.click();
    URL.revokeObjectURL(link.href);
  });
</script>
@endpush
@endsection
