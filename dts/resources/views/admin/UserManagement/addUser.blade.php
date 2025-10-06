@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cafe-noir: #4C3D19;
            --kombu-green: #354024;
            --moss-green: #889063;
            --tan: #CFBB99;
            --bone: #ffffff; /* clean white background */
        }

        body {
            font-family: "Poppins", "Segoe UI", sans-serif;
            background-color: var(--bone);
            color: var(--kombu-green);
            margin: 0;
            padding: 0;
        }

        .container-custom {
            max-width: 700px;
            margin: 50px auto;
            padding: 25px;
        }

        h2 {
            color: var(--cafe-noir);
            font-weight: 700;
            margin-bottom: 25px;
        }

        .card-custom {
            background-color: var(--tan);
            border: 2px solid var(--moss-green);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        label {
            font-weight: 600;
            margin-top: 10px;
        }

        input, select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 5px;
            margin-bottom: 15px;
            border-radius: 8px;
            border: 1px solid var(--moss-green);
            font-size: 0.95rem;
        }

        button[type="submit"] {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 10px;
            padding: 12px 35px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: var(--cafe-noir);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container-custom">
    <div class="card-custom">
        <h2>Add User</h2>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('admin.userManagement.add') }}">
            @csrf

            <label for="username">Username</label>
            <input type="text" id="username" name="username" placeholder="Username" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" placeholder="Email" required>

            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" placeholder="First Name" required>

            <label for="middleName">Middle Name</label>
            <input type="text" id="middleName" name="middleName" placeholder="Middle Name">

            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>

            <label for="roleID">Role</label>
            <select id="roleID" name="roleID" required>
                <option value="">Select Role</option>
                <option value="1">Admin</option>
                <option value="2">DocumentOwner</option>
                <option value="3">Staff</option>
                <option value="4">Auditor</option>
            </select>

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
                <option value="10">ICJE - Institute of Criminal Justice Education</option>
                <option value="11">CAS - College of Arts and Sciences</option>
            </select>

            <label for="phoneNo">Phone Number</label>
            <input type="text" id="phoneNo" name="phoneNo" placeholder="Phone Number">

            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Password" required>

            <label for="password_confirmation">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm Password" required>

            <button type="submit">Add User</button>
        </form>
    </div>
</div>
</body>
</html>
@endsection
