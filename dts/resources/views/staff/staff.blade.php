@extends('layouts.app')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --cafe-noir: #4C3D19;
            --kombu-green: #354024;
            --moss-green: #889063;
            --tan: #CFBB99;
            --bone: #ffffff;
        }

        body {
            font-family: "Poppins", "Segoe UI", sans-serif;
            background-color: var(--bone);
            color: var(--kombu-green);
            margin: 0;
            padding: 0;
        }

        .container-custom {
            max-width: 900px;
            margin: 40px auto;
            padding: 20px;
        }

        h2 {
            color: var(--cafe-noir);
            font-weight: 700;
            margin-bottom: 20px;
            text-align: center;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }

        .card-custom {
            background-color: var(--tan);
            border: 2px solid var(--moss-green);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        .card-custom strong {
            font-size: 1.1rem;
            color: var(--cafe-noir);
        }

        .btn-custom {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 8px;
            padding: 8px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
            margin-top: 8px;
        }

        .btn-custom:hover {
            background-color: var(--cafe-noir);
        }

        .form-group {
            margin-top: 8px;
        }

        select, input[type="file"] {
            margin-top: 4px;
            margin-bottom: 8px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

    </style>
</head>
<body>
<div class="container-custom">
    <h2>Staff Dashboard</h2>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn-custom">Logout</button>
    </form>

    @foreach($documents as $doc)
        <div class="card-custom">
            <strong>{{ $doc->title }}</strong> ({{ $doc->documentNo }})<br>
            Status: {{ $doc->status->statusName ?? 'Unknown' }}<br>
            @if($doc->filePath)
                <a href="{{ asset('storage/' . $doc->filePath) }}" class="btn-custom" download>Download</a>
            @else
                <span>No file available</span>
            @endif

            <form method="POST" action="{{ route('staff.processAndRouteDocument', $doc->documentId) }}" enctype="multipart/form-data">
                @csrf
                <div class="form-group">
                    <label>Reupload Processed Document</label>
                    <input type="file" name="file" required>
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="statusID" required>
                        <option value="2">Approved</option>
                        <option value="3">Rejected</option>
                        <option value="4">In Review</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Send To:</label>
                    <select name="departmentID" required>
                        <option value="1">Admin</option>
                        @foreach($departments as $dep)
                            <option value="{{ $dep->depID }}">{{ $dep->depName }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn-custom">Send & Upload</button>
            </form>
        </div>
    @endforeach
</div>
</body>
</html>
@endsection
