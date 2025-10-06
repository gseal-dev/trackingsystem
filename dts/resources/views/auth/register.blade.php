@extends('layouts.app')

@section('content')
<style>
    :root {
        --cafe-noir: #4C3D19;
        --kombu-green: #354024;
        --moss-green: #889063;
        --tan: #CFBB99;
        --bone: #ffffff;
    }

    body {
        background-color: var(--bone);
        font-family: 'Poppins', 'Segoe UI', sans-serif;
    }

    .register-wrapper {
        min-height: 100vh;
        background: linear-gradient(135deg, rgba(136, 144, 99, 0.25), rgba(229, 215, 196, 0.7));
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .register-card {
        background-color: var(--tan);
        border: 1px solid var(--moss-green);
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(53, 64, 36, 0.25);
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 550px;
        transition: all 0.3s ease-in-out;
    }

    .register-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(53, 64, 36, 0.35);
    }

    .register-card h2 {
        color: var(--cafe-noir);
        font-weight: 700;
        text-align: center;
        margin-bottom: 1rem;
    }

    .register-card p {
        color: var(--kombu-green);
        text-align: center;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .register-card label {
        color: var(--kombu-green);
        font-weight: 500;
        margin-bottom: 0.4rem;
        display: block;
    }

    .register-card input,
    .register-card select {
        width: 100%;
        padding: 0.8rem 1rem;
        border-radius: 10px;
        border: 2px solid var(--moss-green);
        background-color: #fffefc;
        font-size: 0.95rem;
        transition: all 0.2s ease;
        margin-bottom: 1rem;
    }

    .register-card input:focus,
    .register-card select:focus {
        outline: none;
        border-color: var(--cafe-noir);
        box-shadow: 0 0 0 0.2rem rgba(76, 61, 25, 0.2);
    }

    .btn-register {
        background-color: var(--kombu-green);
        color: var(--bone);
        font-weight: 600;
        border: none;
        border-radius: 10px;
        width: 100%;
        padding: 0.9rem;
        transition: background-color 0.3s ease;
        margin-top: 0.5rem;
    }

    .btn-register:hover {
        background-color: var(--cafe-noir);
    }

    .login-redirect {
        text-align: center;
        margin-top: 1rem;
    }

    .login-redirect a {
        text-decoration: none;
        color: var(--kombu-green);
        font-weight: 500;
        transition: color 0.3s ease;
    }

    .login-redirect a:hover {
        color: var(--cafe-noir);
        text-decoration: underline;
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <h2>Register as Document Owner 🗂️</h2>
        <p>Create an account to manage and track your documents seamlessly.</p>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            {{-- Username --}}
            <label for="username">Username</label>
            <input type="text" name="username" id="username" required placeholder="Enter your username">

            {{-- Email --}}
            <label for="email">Email</label>
            <input type="email" name="email" id="email" required placeholder="Enter your email address">

            {{-- First Name --}}
            <label for="firstName">First Name</label>
            <input type="text" name="firstName" id="firstName" required placeholder="Enter your first name">

            {{-- Middle Name --}}
            <label for="middleName">Middle Name</label>
            <input type="text" name="middleName" id="middleName" placeholder="Enter your middle name">

            {{-- Last Name --}}
            <label for="lastName">Last Name</label>
            <input type="text" name="lastName" id="lastName" required placeholder="Enter your last name">

            {{-- Department --}}
            <label for="departmentID">Department</label>
            <select name="departmentID" id="departmentID" required>
                <option value="">Select Department</option>
                <option value="5">COT - College of Technology</option>
                <option value="6">CIT - College of Information Technology</option>
                <option value="7">COM - College of Management</option>
                <option value="8">COE - College of Engineering</option>
                <option value="9">CE - College of Education</option>
                <option value="10">ICJE - Institute of Criminal Justice Education</option>
                <option value="11">CAS - College of Arts and Sciences</option>
            </select>

            {{-- Phone --}}
            <label for="phoneNo">Phone Number</label>
            <input type="text" name="phoneNo" id="phoneNo" placeholder="Enter your phone number">

            {{-- Password --}}
            <label for="password">Password</label>
            <input type="password" name="password" id="password" required placeholder="Enter your password">

            {{-- Confirm Password --}}
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="Confirm your password">

            {{-- Submit --}}
            <button type="submit" class="btn-register">Register</button>
        </form>

        <div class="login-redirect">
            <a href="{{ route('login') }}">Already have an account? Login</a>
        </div>
    </div>
</div>
@endsection
