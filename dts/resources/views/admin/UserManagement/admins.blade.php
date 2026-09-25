@extends('layouts.app')
@section('title', 'Users List')
@section('content')
@push('head')
<style>
  .users-page {
    align-self: flex-start;
    width: min(1200px, 100%);
    padding: 1.4rem 0 3rem;
  }

  .users-heading,
  .users-toolbar,
  .users-table-header,
  .user-row {
    display: grid;
    grid-template-columns: 1.25fr 1.35fr 0.9fr 0.9fr 0.9fr 0.6fr;
    align-items: center;
  }

  .users-heading {
    grid-template-columns: 1fr auto;
    margin-bottom: 2.35rem;
  }

  .users-heading h1 {
    margin: 0;
    font-size: 1.75rem;
    font-weight: 800;
    letter-spacing: -0.03em;
  }

  .users-actions {
    display: flex;
    gap: 0.55rem;
  }

  .users-export,
  .users-add {
    min-height: 38px;
    border: 1px solid #d9dce3;
    border-radius: 7px;
    background: #fff;
    color: #20232b;
    font-weight: 600;
  }

  .users-add {
    border-color: #16a34a;
    color: #15803d;
    font-size: 1.2rem;
    line-height: 1;
  }

  .users-toolbar {
    grid-template-columns: minmax(240px, 1.1fr) 1fr 0.85fr;
    gap: 2.5rem;
    margin-bottom: 1.55rem;
  }

  .users-field label {
    display: block;
    margin-bottom: 0.45rem;
    color: #525762;
    font-size: 0.82rem;
  }

  .users-search {
    position: relative;
  }

  .users-search input,
  .users-select {
    width: 100%;
    height: 38px;
    border: 1px solid #d9dce3;
    border-radius: 6px;
    background: #fff;
    color: #20232b;
    padding: 0.45rem 0.75rem;
  }

  .users-search input {
    padding-right: 2.25rem;
  }

  .users-search button {
    position: absolute;
    right: 0.45rem;
    bottom: 0.35rem;
    border: 0;
    background: transparent;
    color: #111827;
    font-size: 1.05rem;
  }

  .users-status {
    display: flex;
    height: 38px;
  }

  .users-status button {
    flex: 1;
    border: 1px solid #d9dce3;
    background: #fff;
    color: #20232b;
    font-weight: 600;
  }

  .users-status button:first-child { border-radius: 6px 0 0 6px; }
  .users-status button:last-child { border-radius: 0 6px 6px 0; }
  .users-status button + button { border-left: 0; }
  .users-status button.active { background: #202532; color: #fff; }

  .users-table {
    overflow: hidden;
    border: 1px solid #dfe1e7;
    border-radius: 9px;
    background: #fff;
  }

  .users-table-header {
    min-height: 58px;
    padding: 0 1rem;
    background: #e9e9ef;
    color: #646771;
    font-size: 0.78rem;
    font-weight: 800;
    text-transform: uppercase;
  }

  .user-row {
    min-height: 72px;
    padding: 0 1rem;
    border-top: 1px solid #edf0f2;
    color: #20232b;
  }

  .user-name { font-weight: 600; }
  .user-email { color: #20232b; }

  .user-status,
  .user-role {
    display: inline-flex;
    width: fit-content;
    padding: 0.35rem 0.7rem;
    border-radius: 999px;
    font-size: 0.86rem;
    font-weight: 600;
  }

  .user-status { background: #c9f7e2; color: #159568; }
  .user-role { border: 1px solid #d9dce3; border-radius: 4px; background: #f5f5f7; }
  .user-password { color: #6b7280; letter-spacing: 0.12em; }

  .user-edit {
    width: 32px;
    height: 32px;
    border: 0;
    background: transparent;
    color: #111827;
    font-size: 1.15rem;
  }

  .users-empty { padding: 2rem 1rem; color: #6b7280; text-align: center; }

  @media (max-width: 760px) {
    .users-heading { align-items: start; gap: 1rem; }
    .users-toolbar { grid-template-columns: 1fr; gap: 1rem; }
    .users-table { overflow-x: auto; }
    .users-table-header,
    .user-row { min-width: 760px; }
  }
</style>
@endpush
<div class="users-page">
  <div class="users-heading">
    <h1>Users List</h1>
    <div class="users-actions">
      <button type="button" class="users-export" id="export-users"><i class="bi bi-download"></i> Export Excel</button>
      <a href="{{ route('admin.userManagement.addForm', ['type' => 'admins']) }}" class="users-add btn" aria-label="Add admin" title="Add admin"><i class="bi bi-plus-lg"></i></a>
    </div>
  </div>

  <form class="users-toolbar" method="GET" action="{{ route('admin.userManagement.admins') }}">
    <div class="users-field users-search">
      <label for="search">Search</label>
      <input id="search" name="search" type="search" value="{{ request('search') }}" placeholder="Search by name or email">
      <button type="submit" aria-label="Search"><i class="bi bi-search"></i></button>
    </div>
    <div class="users-field">
      <label>Status</label>
      <div class="users-status" role="group" aria-label="Filter by status">
        <button type="button" class="active" data-status="all">All</button>
        <button type="button" data-status="active">Active</button>
        <button type="button" data-status="inactive">Inactive</button>
      </div>
    </div>
    <div class="users-field">
      <label for="role">Role</label>
      <select id="role" class="users-select">
        <option value="all">All</option>
        <option value="admin">Admin</option>
      </select>
    </div>
  </form>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="users-table">
    <div class="users-table-header">
      <div>Name</div><div>Email</div><div>Status</div><div>Role</div><div>Password</div><div>Action</div>
    </div>
    @forelse($users as $user)
      <div class="user-row" data-status="active" data-role="admin">
        <div class="user-name">{{ $user->firstName }} {{ $user->lastName }}</div>
        <div class="user-email">{{ $user->email }}</div>
        <div><span class="user-status">Active</span></div>
        <div><span class="user-role">{{ $user->role->roleName ?? 'Admin' }}</span></div>
        <div><span class="user-password" aria-label="Password hidden">********</span></div>
        <div class="d-flex align-items-center gap-2">
          <a class="user-edit" href="{{ route('admin.userManagement.editForm', ['type' => 'admins', 'user' => $user]) }}" aria-label="Edit {{ $user->firstName }} {{ $user->lastName }}" title="Edit user"><i class="bi bi-pencil"></i></a>
          <form action="{{ route('admin.userManagement.delete', ['type' => 'admins', 'user' => $user]) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?');">
            @csrf
            <button type="submit" class="user-edit text-danger border-0 bg-transparent" aria-label="Delete {{ $user->firstName }} {{ $user->lastName }}" title="Delete user"><i class="bi bi-trash"></i></button>
          </form>
        </div>
      </div>
    @empty
      <div class="users-empty">No admin users found.</div>
    @endforelse
  </div>
</div>
@push('scripts')
<script>
  document.querySelectorAll('[data-status]').forEach(function (button) {
    button.addEventListener('click', function () {
      document.querySelectorAll('.users-status button').forEach(function (item) { item.classList.remove('active'); });
      button.classList.add('active');
      document.querySelectorAll('.user-row').forEach(function (row) {
        row.hidden = button.dataset.status !== 'all' && row.dataset.status !== button.dataset.status;
      });
    });
  });

  document.getElementById('role').addEventListener('change', function () {
    document.querySelectorAll('.user-row').forEach(function (row) {
      row.hidden = this.value !== 'all' && row.dataset.role !== this.value;
    }, this);
  });

  document.getElementById('export-users').addEventListener('click', function () {
    const rows = [['Name', 'Email', 'Status', 'Role']];
    document.querySelectorAll('.user-row:not([hidden])').forEach(function (row) {
      rows.push(Array.from(row.children).slice(0, 4).map(function (cell) { return cell.innerText.trim(); }));
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
