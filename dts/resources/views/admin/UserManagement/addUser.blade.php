@extends('layouts.app')
@section('content')
<h2>Add User</h2>
<form method="POST" action="{{ route('admin.userManagement.add') }}">
    @csrf
    <div>
        <label for="username">Username</label>
        <input type="text" id="username" name="username" placeholder="Username" required>
    </div>
    <div>
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Email" required>
    </div>
    <div>
        <label for="firstName">First Name</label>
        <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
    </div>
    <div>
        <label for="middleName">Middle Name</label>
        <input type="text" id="middleName" name="middleName" placeholder="Middle Name">
    </div>
    <div>
        <label for="lastName">Last Name</label>
        <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
    </div>
    <div>
        <label for="roleID">Role</label>
        <select id="roleID" name="roleID" required>
            <option value="">Select Role</option>
            <option value="1">Admin</option>
            <option value="2">DocumentOwner</option>
            <option value="3">Staff</option>
            <option value="4">Auditor</option>
        </select>
    </div>
    <div>
        <label for="departmentID">Department</label>
        <select id="departmentID" name="departmentID" required>
            <option value="">Select Department</option>
            <option value="1">Admin</option>
            <option value="2">Office of the Chancellor</option>
            <option value="3">Campus Student Body Organization</option>
            <option value="4">Student Affairs and Services</option>
            <option value="5">COT - College of Technology</option>
            <option value="6">CIT - College of Information Technology</option>
            <option value="7">COM - College of Management</option>
            <option value="8">COE - College of Engineering</option>
            <option value="9">CE - College of Education</option>
            <option value="10">ICJE - Institute of Crimial Justice Education</option>
            <option value="11">CAS - College of Arts and Sciences</option>
        </select>
    </div>
    <div>
        <label for="phoneNo">Phone Number</label>
        <input type="text" id="phoneNo" name="phoneNo" placeholder="Phone Number">
    </div>
    <div>
        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Password" required>
    </div>
    <div>
        <label for="password_confirmation">Confirm Password</label>
        <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>
    </div>
    <button type="submit">Add User</button>
</form>
@endsection