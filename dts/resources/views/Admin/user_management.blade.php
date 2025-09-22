@extends('layout')

@section('content')
<h2>User Management</h2>
<a href="{{ route('admin.users.create') }}">Add User</a>
<table>
    <thead>
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
        </tr>
    </thead>
    <tbody>
        @foreach($users as $user)
        <tr>
            <td>{{ $user->username }}</td>
            <td>{{ $user->email }}</td>
            <td>{{ $user->role->roleName ?? '' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
<a href="{{ route('admin.dashboard') }}" class="btn btn-secondary mb-3">Back to Admin Dashboard</a>
@endsection