@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('head')
<style>
    .dashboard-shell .dashboard-container {
        width: 100%;
    }

    .dashboard-shell {
        align-self: flex-start;
        width: calc(100% + 1rem);
        min-height: calc(100vh - 2rem);
        margin: -1rem -0.5rem;
        padding: clamp(1rem, 2vw, 2rem);
        border: 1px solid #dfe3ea;
        border-radius: 16px;
        background: #ffffff;
        box-sizing: border-box;
        justify-content: flex-start;
    }

    .dashboard-layout {
        display: block;
    }

    .dashboard-main {
        min-width: 0;
        width: 100%;
        padding-left: 1rem;
    }

    .dashboard-main .dashboard-header,
    .dashboard-main .dashboard-toolbar,
    .dashboard-main .dashboard-users-section {
        width: 100%;
    }

    .dashboard-main .dashboard-title {
        font-weight: 700;
        font-size: clamp(2.2rem, 4vw, 3.5rem);
    }

    .dashboard-users-section {
        width: 100%;
        margin: 2rem 0 0;
    }

    .dashboard-users-heading,
    .dashboard-users-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .dashboard-users-heading {
        margin-bottom: 1.75rem;
    }

    .dashboard-users-heading h2 {
        margin: 0;
        font-size: clamp(1.8rem, 3vw, 2.5rem);
        font-weight: 800;
    }

    .dashboard-users-actions {
        display: flex;
        gap: 0.5rem;
    }

    .dashboard-users-actions button,
    .dashboard-users-actions a {
        min-height: 44px;
        border: 1px solid #d9dce3;
        border-radius: 7px;
        background: #fff;
        color: #20232b;
    }

    .dashboard-users-actions a {
        border-color: #16a34a;
        color: #15803d;
        font-size: 1.15rem;
    }

    .dashboard-users-toolbar {
        margin-bottom: 1.4rem;
    }

    .dashboard-users-field label {
        display: block;
        margin-bottom: 0.4rem;
        color: #525762;
        font-size: 0.78rem;
    }

    .dashboard-users-search,
    .dashboard-users-select,
    .dashboard-users-status {
        height: 44px;
    }

    .dashboard-users-search-wrap {
        position: relative;
    }

    .dashboard-users-search,
    .dashboard-users-select {
        width: 100%;
        border: 1px solid #d9dce3;
        border-radius: 6px;
        background: #fff;
        padding: 0.45rem 0.7rem;
    }

    .dashboard-users-search {
        padding-right: 2.2rem;
    }

    .dashboard-users-search-wrap button {
        position: absolute;
        right: 0.45rem;
        bottom: 0.35rem;
        border: 0;
        background: transparent;
        color: #111827;
        font-size: 1rem;
    }

    .dashboard-users-status {
        display: flex;
    }

    .dashboard-users-status button {
        flex: 1;
        border: 1px solid #d9dce3;
        background: #fff;
        font-weight: 600;
    }

    .dashboard-users-status button:first-child { border-radius: 6px 0 0 6px; }
    .dashboard-users-status button:last-child { border-radius: 0 6px 6px 0; }
    .dashboard-users-status button + button { border-left: 0; }
    .dashboard-users-status button.active { background: #202532; color: #fff; }

    .dashboard-section-heading {
        display: flex;
        align-items: end;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .dashboard-section-title {
        margin: 0;
        font-size: 1.35rem;
        font-weight: 800;
    }

    .dashboard-users-table {
        max-width: none;
        padding: 0;
        text-align: left;
        width: 100%;
        font-size: 1rem;
    }

    .dashboard-users-table table {
        width: 100%;
        min-width: 760px;
    }

    .dashboard-users-table th,
    .dashboard-users-table td {
        padding: 1rem;
    }

    .dashboard-user-status {
        display: inline-block;
        padding: 0.4rem 0.8rem;
        border-radius: 999px;
        background: #c9f7e2;
        color: #159568;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .dashboard-users-password {
        color: #6b7280;
        letter-spacing: 0.12em;
    }

    .add-admin-modal-footer {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 0.75rem;
    }

    .add-admin-modal-footer button {
        width: 100%;
        min-height: 42px;
    }

    .required-mark {
        color: #dc2626;
        font-weight: 700;
    }

    .password-field {
        position: relative;
    }

    .password-field input {
        padding-right: 2.75rem;
    }

    .password-toggle {
        position: absolute;
        right: 0.65rem;
        bottom: 0.45rem;
        border: 0;
        background: transparent;
        color: #6b7280;
        padding: 0;
    }

    @media (max-width: 760px) {
        .dashboard-main {
            padding-left: 0;
        }

        .dashboard-users-heading {
            align-items: flex-start;
            flex-direction: column;
        }

        .dashboard-users-toolbar {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }

</style>
@endpush

@section('content')
<div class="dashboard-shell">
    <div class="dashboard-container">
        <div class="dashboard-layout">
            <div class="dashboard-main">
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">Admin Dashboard</h1>
                <p class="dashboard-subtitle">Quick access to management and document workflows</p>
            </div>
        </div>

        <div class="dashboard-toolbar">
            <div class="toolbar-item">
                <i class="bi bi-speedometer2"></i>
                <span>You are signed in as</span>
                <strong>{{ auth()->user()->username ?? 'Administrator' }}</strong>
            </div>
            <div class="toolbar-item muted">
                <i class="bi bi-shield-lock"></i>
                <span>Secure Area</span>
            </div>
        </div>

        <section class="dashboard-users-section">
            <div class="container-xl px-0">
            <div class="dashboard-users-heading">
                <h2>Users List</h2>
                <div class="dashboard-users-actions">
                    <button type="button" id="dashboard-export-users"><i class="bi bi-download"></i> Export Excel</button>
                    <button type="button" class="btn" data-bs-toggle="modal" data-bs-target="#addAdminModal" aria-label="Add admin" title="Add admin"><i class="bi bi-plus-lg"></i></button>
                </div>
            </div>

            <div class="row g-3 dashboard-users-toolbar">
                <div class="col-12 col-lg-5 dashboard-users-field dashboard-users-search-wrap">
                    <label for="dashboard-user-search">Search</label>
                    <input id="dashboard-user-search" class="dashboard-users-search" type="search" placeholder="Search by name or email">
                    <button type="button" aria-label="Search"><i class="bi bi-search"></i></button>
                </div>
                <div class="col-12 col-lg-4 dashboard-users-field">
                    <label>Status</label>
                    <div class="dashboard-users-status">
                        <button type="button" class="active" data-dashboard-status="all">All</button>
                        <button type="button" data-dashboard-status="active">Active</button>
                        <button type="button" data-dashboard-status="inactive">Inactive</button>
                    </div>
                </div>
                <div class="col-12 col-lg-3 dashboard-users-field">
                    <label for="dashboard-user-role">Role</label>
                    <select id="dashboard-user-role" class="dashboard-users-select">
                        <option value="all">All</option>
                        <option value="Admin">Admin</option>
                        <option value="Staff">Staff</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive card-surface dashboard-users-table">
                <table class="table table-clean align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                            <th>Role</th>
                            <th>Password</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr data-dashboard-status="active" data-dashboard-role="{{ $user->role->roleName ?? 'Admin' }}" data-dashboard-search="{{ strtolower($user->firstName . ' ' . $user->lastName . ' ' . $user->email) }}">
                                <td>{{ $user->firstName }} {{ $user->lastName }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="dashboard-user-status">Active</span></td>
                                <td>{{ $user->role->roleName ?? 'Admin' }}</td>
                                <td><span class="dashboard-users-password">********</span></td>
                                <td>
                                    <a href="{{ route('admin.userManagement.editForm', ['type' => strtolower($user->role->roleName ?? 'Admin') . 's', 'user' => $user]) }}" class="btn btn-sm btn-outline-secondary" aria-label="Edit user" title="Edit user">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.userManagement.delete', ['type' => strtolower($user->role->roleName ?? 'Admin') . 's', 'user' => $user]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Delete user" title="Delete user">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No admin users found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
        </section>

        <div class="modal fade" id="addAdminModal" tabindex="-1" aria-labelledby="addAdminModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title fs-4" id="addAdminModalLabel">Add Admin</h2>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="{{ route('admin.userManagement.add', ['type' => 'admins']) }}">
                        @csrf
                        <div class="modal-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    @foreach($errors->all() as $error)
                                        <div>{{ $error }}</div>
                                    @endforeach
                                </div>
                            @endif
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label" for="modal-username">Username <span class="required-mark">*</span></label>
                                    <input class="form-control" type="text" id="modal-username" name="username" value="{{ old('username') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="modal-email">Email <span class="required-mark">*</span></label>
                                    <input class="form-control" type="email" id="modal-email" name="email" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="modal-first-name">First Name <span class="required-mark">*</span></label>
                                    <input class="form-control" type="text" id="modal-first-name" name="firstName" value="{{ old('firstName') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="modal-last-name">Last Name <span class="required-mark">*</span></label>
                                    <input class="form-control" type="text" id="modal-last-name" name="lastName" value="{{ old('lastName') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label" for="modal-role">Role <span class="required-mark">*</span></label>
                                    <select class="form-select" id="modal-role" name="roleID" required>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->roleID }}" @selected(old('roleID', 1) == $role->roleID)>{{ $role->roleName }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="password-field">
                                        <label class="form-label" for="modal-password">Password <span class="required-mark">*</span></label>
                                        <input class="form-control" type="password" id="modal-password" name="password" required>
                                        <button type="button" class="password-toggle" data-password-target="modal-password" aria-label="Show password"><i class="bi bi-eye"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="password-field">
                                        <label class="form-label" for="modal-password-confirmation">Confirm Password <span class="required-mark">*</span></label>
                                        <input class="form-control" type="password" id="modal-password-confirmation" name="password_confirmation" required>
                                        <button type="button" class="password-toggle" data-password-target="modal-password-confirmation" aria-label="Show password"><i class="bi bi-eye"></i></button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer add-admin-modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-brand"><i class="bi bi-person-plus"></i> Add</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @if($errors->any())
            @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        bootstrap.Modal.getOrCreateInstance(document.getElementById('addAdminModal')).show();
                    });
                </script>
            @endpush
        @endif
        @push('scripts')
            <script>
                (function () {
                    const search = document.getElementById('dashboard-user-search');
                    const role = document.getElementById('dashboard-user-role');
                    const statusButtons = document.querySelectorAll('button[data-dashboard-status]');
                    const rows = document.querySelectorAll('tbody tr[data-dashboard-role]');
                    let selectedStatus = 'all';

                    function filterUsers() {
                        const searchTerm = search.value.trim().toLowerCase();
                        rows.forEach(function (row) {
                            const matchesSearch = row.dataset.dashboardSearch.includes(searchTerm);
                            const matchesStatus = selectedStatus === 'all' || row.dataset.dashboardStatus === selectedStatus;
                            const matchesRole = role.value === 'all' || row.dataset.dashboardRole === role.value;
                            row.hidden = !(matchesSearch && matchesStatus && matchesRole);
                        });
                    }

                    search.addEventListener('input', filterUsers);
                    role.addEventListener('change', filterUsers);
                    statusButtons.forEach(function (button) {
                        button.addEventListener('click', function () {
                            selectedStatus = button.dataset.dashboardStatus;
                            statusButtons.forEach(function (item) { item.classList.remove('active'); });
                            button.classList.add('active');
                            filterUsers();
                        });
                    });
                })();

                document.getElementById('dashboard-export-users').addEventListener('click', function () {
                    const rows = [['Name', 'Email', 'Status', 'Role', 'Password']];
                    document.querySelectorAll('tbody tr[data-dashboard-role]:not([hidden])').forEach(function (row) {
                        rows.push(Array.from(row.children).slice(0, 5).map(function (cell) {
                            return cell.innerText.trim();
                        }));
                    });
                    const csv = rows.map(function (row) {
                        return row.map(function (cell) {
                            return '"' + cell.replaceAll('"', '""') + '"';
                        }).join(',');
                    }).join('\n');
                    const link = document.createElement('a');
                    link.href = URL.createObjectURL(new Blob([csv], { type: 'text/csv;charset=utf-8;' }));
                    link.download = 'admin-users.csv';
                    link.click();
                    URL.revokeObjectURL(link.href);
                });

                document.querySelectorAll('[data-password-target]').forEach(function (button) {
                    button.addEventListener('click', function () {
                        const input = document.getElementById(button.dataset.passwordTarget);
                        const visible = input.type === 'text';
                        input.type = visible ? 'password' : 'text';
                        button.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
                        button.innerHTML = visible ? '<i class="bi bi-eye"></i>' : '<i class="bi bi-eye-slash"></i>';
                    });
                });
            </script>
        @endpush
            </div>
        </div>
    </div>
</div>
@endsection
