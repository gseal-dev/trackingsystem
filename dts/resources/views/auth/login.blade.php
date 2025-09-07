@extends('layout')
@section('title', 'Login')
@section('content')
    <div  class="container">
        <div class="mt-5">
            @if($errors->any())
                <div class="col-12">
                    @foreach($errors->all() as $error)
                    <div class="alert alert-danger">{{$error}}</div>
                    @endforeach
                </div>
            @endif

            @if(session()->has('error'))
                <div class="alert alert-danger">{{session('error')}}</div>
            @endif

            @if(session()->has('success'))
                <div class="alert alert-success">{{session('success')}}</div>
            @endif
        </div>
        <form action="{{route('login.post')}}" method="POST" class="ms-auto me-auto mt-5" style="width: 500px">
            @csrf
            <div class="mb-3">
                <label  class="form-label">Email address</label>
                <input type="email" class="form-control" aria-describedby="emailHelp" name="email">
            </div>
            <div class="mb-3">
                <label  class="form-label">Password</label>
                <input type="password" class="form-control" name="password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
            <a href="{{ route('registration') }}" class="btn btn-link">Don't have an account? Register</a>
        </form>
    </div>
@endsection