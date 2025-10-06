@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
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
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        h2 {
            color: var(--cafe-noir);
            font-weight: 700;
            margin-bottom: 20px;
        }

        .card-custom {
            background-color: var(--tan);
            border: 2px solid var(--moss-green);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .card-custom h3 {
            color: var(--cafe-noir);    
            font-weight: 700;
            margin-bottom: 15px;
        }

        .btn-custom {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        .btn-custom:hover {
            background-color: var(--cafe-noir);
        }

        .btn-add {
            margin-bottom: 15px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }

        .badge-role {
            font-size: 0.85rem;
            font-weight: 600;
        }
    </style>
</head>
<body>

<div class="container-custom">
    <h2>User Management</h2>

    <a href="{{ route('admin.userManagement.addForm') }}" class="btn btn-custom btn-add">➕ Add User</a>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- Admins --}}
    <h4 class="mt-4 mb-2">Admins</h4>
    @forelse($admins as $user)
        <div class="card-custom d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $user->username }}</strong>
                <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
                <small>Email: {{ $user->email }}</small><br>
                <span class="badge bg-primary badge-role">Admin</span>
            </div>
            <div>
                <a href="{{ route('admin.userManagement.editForm', $user) }}" class="btn btn-dark btn-sm">Edit</a>
                <form action="{{ route('admin.userManagement.delete', $user) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No admins found.</div>
    @endforelse

    {{-- Document Owners --}}
    <h4 class="mt-4 mb-2">Document Owners</h4>
    @forelse($owners as $user)
        <div class="card-custom d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $user->username }}</strong>
                <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
                <small>Email: {{ $user->email }}</small><br>
                <span class="badge bg-success badge-role">Owner</span>
            </div>
            <div>
                <a href="{{ route('admin.userManagement.editForm', $user) }}" class="btn btn-dark btn-sm">Edit</a>
                <form action="{{ route('admin.userManagement.delete', $user) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No owners found.</div>
    @endforelse

    {{-- Staff --}}
    <h4 class="mt-4 mb-2">Staff</h4>
    @forelse($staffs as $user)
        <div class="card-custom d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $user->username }}</strong>
                <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
                <small>Email: {{ $user->email }}</small><br>
                <span class="badge bg-warning text-dark badge-role">Staff</span>
            </div>
            <div>
                <a href="{{ route('admin.userManagement.editForm', $user) }}" class="btn btn-dark btn-sm">Edit</a>
                <form action="{{ route('admin.userManagement.delete', $user) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No staff found.</div>
    @endforelse

    {{-- Auditors --}}
    <h4 class="mt-4 mb-2">Auditors</h4>
    @forelse($auditors as $user)
        <div class="card-custom d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $user->username }}</strong>
                <span class="text-muted">({{ $user->firstName }} {{ $user->lastName }})</span><br>
                <small>Email: {{ $user->email }}</small><br>
                <span class="badge bg-info text-dark badge-role">Auditor</span>
            </div>
            <div>
                <a href="{{ route('admin.userManagement.editForm', $user) }}" class="btn btn-dark btn-sm">Edit</a>
                <form action="{{ route('admin.userManagement.delete', $user) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                </form>
            </div>
        </div>
    @empty
        <div class="alert alert-info">No auditors found.</div>
    @endforelse

</div>
</body>
</html>
@endsection
