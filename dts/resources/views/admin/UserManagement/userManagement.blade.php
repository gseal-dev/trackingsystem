@extends('layouts.app')
@section('content')
<h2>User Management</h2>

@if(session('success'))
    <div>{{ session('success') }}</div>
@endif

<a href="{{ route('admin.userManagement.addForm') }}">
    <button type="button">Add User</button>
</a>

<h3>Admins</h3>
<table border="1">
    <tr>
        <th>Username</th><th>Email</th><th>Name</th><th>Actions</th>
    </tr>
    @foreach($admins as $user)
    <tr>
        <td>{{ $user->username }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->firstName }} {{ $user->lastName }}</td>
        <td>
            <form method="POST" action="{{ route('admin.userManagement.delete', $user) }}" style="display:inline;">
                @csrf
                <button type="submit">Delete</button>
            </form>
            <a href="{{ route('admin.userManagement.editForm', $user) }}">
                <button type="button">Edit</button>
            </a>
        </td>
    </tr>
    @endforeach
</table>

<h3>Document Owners</h3>
<table border="1">
    <tr>
        <th>Username</th><th>Email</th><th>Name</th><th>Actions</th>
    </tr>
    @foreach($owners as $user)
    <tr>
        <td>{{ $user->username }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->firstName }} {{ $user->lastName }}</td>
        <td>
            <form method="POST" action="{{ route('admin.userManagement.delete', $user) }}" style="display:inline;">
                @csrf
                <button type="submit">Delete</button>
            </form>
            <a href="{{ route('admin.userManagement.editForm', $user) }}">
                <button type="button">Edit</button>
            </a>
        </td>
    </tr>
    @endforeach
</table>

<h3>Staff</h3>
<table border="1">
    <tr>
        <th>Username</th><th>Email</th><th>Name</th><th>Actions</th>
    </tr>
    @foreach($staffs as $user)
    <tr>
        <td>{{ $user->username }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->firstName }} {{ $user->lastName }}</td>
        <td>
            <form method="POST" action="{{ route('admin.userManagement.delete', $user) }}" style="display:inline;">
                @csrf
                <button type="submit">Delete</button>
            </form>
            <a href="{{ route('admin.userManagement.editForm', $user) }}">
                <button type="button">Edit</button>
            </a>
        </td>
    </tr>
    @endforeach
</table>

<h3>Auditors</h3>
<table border="1">
    <tr>
        <th>Username</th><th>Email</th><th>Name</th><th>Actions</th>
    </tr>
    @foreach($auditors as $user)
    <tr>
        <td>{{ $user->username }}</td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->firstName }} {{ $user->lastName }}</td>
        <td>
            <form method="POST" action="{{ route('admin.userManagement.delete', $user) }}" style="display:inline;">
                @csrf
                <button type="submit">Delete</button>
            </form>
            <a href="{{ route('admin.userManagement.editForm', $user) }}">
                <button type="button">Edit</button>
            </a>
        </td>
    </tr>
    @endforeach
</table>

@endsection