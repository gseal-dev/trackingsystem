@extends('layout')

@section('content')

<h2 class="mt-5 text-center">Add User</h2>
<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">Back to Admin Dashboard</a>
<div class="ms-auto me-auto mt-3" style="width: 500px">
    <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf

        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" class="form-control" name="password" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" class="form-control" name="password_confirmation" required>
        </div>
        <div class="mb-3">
            <label class="form-label">First Name</label>
            <input type="text" class="form-control" name="firstName">
        </div>
        <div class="mb-3">
            <label class="form-label">Last Name</label>
            <input type="text" class="form-control" name="lastName">
        </div>
        <div class="mb-3">
            <label class="form-label">Role</label>
            <select class="form-control" name="roleID" required>
                @foreach($roles as $role)
                    <option value="{{ $role->roleID }}">{{ $role->roleName }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Department</label>
            <select class="form-control" name="departmentID" required>
                @foreach($departments as $department)
                    <option value="{{ $department->depID }}">{{ $department->depName }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Create User</button>
    </form>
</div>
@endsection