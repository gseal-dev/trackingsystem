@extends('layout')
@section('title', 'Registration')
@section('content')
    <div class="container">
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
        <form action="{{route('registration.post')}}" method="POST" class="ms-auto me-auto mt-5" style="width: 500px">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control" name="email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <div class="mb-3">
                <label class="form-label">First Name</label>
                <input type="text" class="form-control" name="firstName" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Middle Name</label>
                <input type="text" class="form-control" name="middleName">
            </div>
            <div class="mb-3">
                <label class="form-label">Last Name</label>
                <input type="text" class="form-control" name="lastName" required>
            </div>
            <div class="mb-3">
                <input type="hidden" name="roleID" value="2">
            </div>
            <div class="mb-3">
                <label class="form-label">Department</label>
                <select class="form-control" name="departmentID" required>
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
            <div class="mb-3">
                <label class="form-label">Phone Number</label>
                <input type="text" class="form-control" name="phoneNo">
            </div>
            <button type="submit" class="btn btn-primary">Register</button>
            <a href="{{ route('login') }}" class="btn btn-link">Already have an account? Login</a>
        </form>
    </div>
@endsection