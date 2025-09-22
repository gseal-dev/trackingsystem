@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Login</h2>
    <form method="POST" action="{{ route('login') }}">
        @csrf
        <div>
            <label>Username or Email</label>
            <input type="text" name="login" required>
        </div>
        <div>
            <label>Password</label>
            <input type="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <div style="margin-top: 16px;">
        <a href="{{ route('register') }}">
            <button type="button">Don't have an account? Register</button>
        </a>
    </div>
</div>
@endsection