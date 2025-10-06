@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
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
            padding: 20px;
        }

        h2 {
            color: var(--cafe-noir);
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }

        .card-custom {
            background-color: var(--tan);
            border: 2px solid var(--moss-green);
            border-radius: 15px;
            padding: 30px 25px;
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

        button {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: var(--cafe-noir);
        }
    </style>
</head>
<body>

<div class="container-custom">
    <h2>Edit User</h2>
    <div class="card-custom">
        <form method="POST" action="{{ route('admin.userManagement.edit', $user) }}">
            @csrf

            <label for="username">Username</label>
            <input type="text" id="username" name="username" value="{{ $user->username }}" required>

            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ $user->email }}" required>

            <label for="firstName">First Name</label>
            <input type="text" id="firstName" name="firstName" value="{{ $user->firstName }}" required>

            <label for="middleName">Middle Name</label>
            <input type="text" id="middleName" name="middleName" value="{{ $user->middleName }}">

            <label for="lastName">Last Name</label>
            <input type="text" id="lastName" name="lastName" value="{{ $user->lastName }}" required>

            <label for="roleID">Role</label>
            <select id="roleID" name="roleID" required>
                <option value="1" {{ $user->roleID == 1 ? 'selected' : '' }}>Admin</option>
                <option value="2" {{ $user->roleID == 2 ? 'selected' : '' }}>DocumentOwner</option>
                <option value="3" {{ $user->roleID == 3 ? 'selected' : '' }}>Staff</option>
                <option value="4" {{ $user->roleID == 4 ? 'selected' : '' }}>Auditor</option>
            </select>

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

            <label for="phoneNo">Phone Number</label>
            <input type="text" id="phoneNo" name="phoneNo" value="{{ $user->phoneNo }}">

            <label for="password">New Password (leave blank to keep current)</label>
            <input type="password" id="password" name="password" placeholder="New Password">

            <label for="password_confirmation">Confirm New Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Confirm New Password">

            <button type="submit">Update User</button>
        </form>
    </div>
</div>

</body>
</html>
@endsection
