@extends('layouts.app')
@section('content')
<h2>Document Owner Dashboard</h2>
<form method="POST" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Logout</button>
</form>
@endsection