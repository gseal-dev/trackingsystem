@extends('layouts.app')
@section('content')
<h2>Edit User</h2>
<form method="POST" action="{{ route('admin.userManagement.edit', $user) }}">
    @csrf
    <div>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" value="{{ $user->username }}" required>
    </div>
    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="{{ $user->email }}" required>
    </div>
    <div>
        <label for="firstName">First Name</label>
        <input type="text" id="firstName" name="firstName" value="{{ $user->firstName }}" required>
    </div>
    <div>
        <label for="middleName">Middle Name</label>
        <input type="text" id="middleName" name="middleName" value="{{ $user->middleName }}">
    </div>
    <div>
        <label for="lastName">Last Name</label>
        <input type="text" id="lastName" name="lastName" value="{{ $user->lastName }}" required>
    </div>
    <div>
        <label for="roleID">Role</label>
        <select id="roleID" name="roleID" required>
            <option value="1" {{ $user->roleID == 1 ? 'selected' : '' }}>Admin</option>
            <option value="2" {{ $user->roleID == 2 ? 'selected' : '' }}>DocumentOwner</option>
            <option value="3" {{ $user->roleID == 3 ? 'selected' : '' }}>Staff</option>
            <option value="4" {{ $user->roleID == 4 ? 'selected' : '' }}>Auditor</option>
        </select>
    </div>
    <div>
        <label for="departmentID">Department</label>
        <select id="departmentID" name="departmentID" required>
            <option value="1" {{ $user->departmentID == 1 ? 'selected' : '' }}>Admin</option>
            <option value="2" {{ $user->departmentID == 2 ? 'selected' : '' }}>Office of the Chancellor</option>
            <option value="3" {{ $user->departmentID == 3 ? 'selected' : '' }}>Campus Student Body Organization</option>
            <option value="4" {{ $user->departmentID == 4 ? 'selected' : '' }}>Student Affairs and Services</option>
            <option value="5" {{ $user->departmentID == 5 ? 'selected' : '' }}>COT - College of Technology</option>
            <option value="6" {{ $user->departmentID == 6 ? 'selected' : '' }}>CIT - College of Information Technology</option>
            <option value="7" {{ $user->departmentID == 7 ? 'selected' : '' }}>COM - College of Management</option>
            <option value="8" {{ $user->departmentID == 8 ? 'selected' : '' }}>COE - College of Engineering</option>
            <option value="9" {{ $user->departmentID == 9 ? 'selected' : '' }}>CE - College of Education</option>
            <option value="10" {{ $user->departmentID == 10 ? 'selected' : '' }}>ICJE - Institute of Crimial Justice Education</option>
            <option value="11" {{ $user->departmentID == 11 ? 'selected' : '' }}>CAS - College of Arts and Sciences</option>
        </select>
    </div>
    <div>
        <label for="phoneNo">Phone Number</label>
        <input type="text" id="phoneNo" name="phoneNo" value="{{ $user->phoneNo }}">
    </div>
    <div>
        <label for="password">New Password (leave blank to keep current)</label>
        <input type="password" id="password" name="password" placeholder="New Password">
    </div>
    <div>
        <label for="password_confirmation">Confirm New Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password">
    </div>
    <button type="submit">Update User</button>
</form>
@endsection