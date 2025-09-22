@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Register as Document Owner</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <div>
            <label>Username</label>
            <input type="text" name="username" required>
        </div>
        <div>
            <label>Email</label>
            <input type="email" name="email" required>
        </div>
        <div>
            <label>First Name</label>
            <input type="text" name="firstName" required>
        </div>
        <div>
            <label>Middle Name</label>
            <input type="text" name="middleName">
        </div>
        <div>
            <label>Last Name</label>
            <input type="text" name="lastName" required>
        </div>
        <div>
            <label>Department</label>
                <select name="departmentID" required>
                    <option value="">Select Department</option>
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
            <label>Phone Number</label>
            <input type="text" name="phoneNo">
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <div>
            <label>Confirm Password</label>
            <input type="password" name="password_confirmation" required>
        </div>
        <button type="submit">Register</button>
    </form>
    <div style="margin-top: 16px;">
        <a href="{{ route('login') }}">
            <button type="button">Already have an account? Login</button>
        </a>
    </div>
</div>
@endsection