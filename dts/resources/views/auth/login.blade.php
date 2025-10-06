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

    .login-wrapper {
        min-height: 100vh;
        background: linear-gradient(145deg, rgba(136, 144, 99, 0.25), rgba(229, 215, 196, 0.6));
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    .login-card {
        background-color: var(--tan);
        border: 1px solid var(--moss-green);
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(53, 64, 36, 0.25);
        padding: 2.5rem 2rem;
        width: 100%;
        max-width: 420px;
        transition: all 0.3s ease-in-out;
    }

    .login-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 28px rgba(53, 64, 36, 0.35);
    }

    .login-card h2 {
        color: var(--cafe-noir);
        font-weight: 700;
        text-align: center;
        margin-bottom: 1rem;
    }

    .login-card p {
        color: var(--kombu-green);
        text-align: center;
        margin-bottom: 2rem;
        font-size: 0.95rem;
    }

    .login-card label {
        color: var(--kombu-green);
        font-weight: 500;
        margin-bottom: 0.4rem;
        display: block;
    }

    .login-card input {
        width: 100%;
        padding: 0.8rem 1rem;
        border-radius: 10px;
        border: 2px solid var(--moss-green);
        background-color: #fffefc;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .login-card input:focus {
        outline: none;
        border-color: var(--cafe-noir);
        box-shadow: 0 0 0 0.2rem rgba(76, 61, 25, 0.2);
    }

    .btn-login {
        background-color: var(--kombu-green);
        color: var(--bone);
        font-weight: 600;
        border: none;
        border-radius: 10px;
        width: 100%;
        padding: 0.9rem;
        transition: background-color 0.3s ease;
    }

    .btn-login:hover {
        background-color: var(--cafe-noir);
    }

    .divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 1.5rem 0;
        color: var(--moss-green);
        font-size: 0.9rem;
    }

    .divider::before,
    .divider::after {
        content: "";
        flex: 1;
        height: 1px;
        background: var(--moss-green);
        margin: 0 10px;
    }

    .register-link {
        color: var(--kombu-green);
        font-weight: 500;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .register-link:hover {
        color: var(--cafe-noir);
        text-decoration: underline;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <h2>Welcome Back 👋</h2>
        <p>Login to continue tracking your documents efficiently.</p>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Username or Email --}}
            <div class="mb-4">
                <label for="login">Username or Email</label>
                <input type="text" name="login" id="login" required placeholder="Enter your username or email">
            </div>

            {{-- Password --}}
            <div class="mb-4">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required placeholder="Enter your password">
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="divider">or</div>

        {{-- Register Redirect --}}
        <div class="text-center">
            <a href="{{ route('register') }}" class="register-link">
                Don’t have an account? Register
            </a>
        </div>
    </div>
</div>
@endsection
