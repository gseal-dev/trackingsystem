@extends('layout')
@section('title', 'Dashboard')
@section('content')
    @if(isset($role) && $role == 'owner')
        <h2>Document Owner Dashboard</h2>
        <p>You can send documents to the admin for registration.</p>
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-secondary mb-3">Logout</button>
        </form>
    @elseif(isset($role) && $role == 'staff')
        <h2>Staff Dashboard</h2>
        <p>You can process and route documents.</p>
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-secondary mb-3">Logout</button>
        </form>
    @else
        <h2>Dashboard</h2>
        <p>Welcome!</p>
        <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-secondary mb-3">Logout</button>
        </form>
    @endif
@endsection