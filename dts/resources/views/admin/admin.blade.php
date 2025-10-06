@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
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
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }

        h2 {
            color: var(--cafe-noir);
            font-weight: 700;
            margin-bottom: 30px;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 25px;
        }

        .card-custom {
            background-color: var(--tan);
            border: 2px solid var(--moss-green);
            border-radius: 15px;
            padding: 30px 20px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .card-custom button {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .card-custom button:hover {
            background-color: var(--cafe-noir);
        }

        .logout-button {
            margin-bottom: 40px;
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 10px;
            padding: 12px 35px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        .logout-button:hover {
            background-color: var(--cafe-noir);
        }
    </style>
</head>
<body>
<div class="container-custom">
    <h2>Admin Dashboard 👋</h2>

    {{-- Logout --}}
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="logout-button">Logout</button>
    </form>

    {{-- Dashboard Cards --}}
    <div class="dashboard-cards">
        <div class="card-custom">
            <h3>User Management</h3>
            <a href="{{ route('admin.userManagement') }}">
                <button type="button">Go</button>
            </a>
        </div>

        <div class="card-custom">
            <h3>Document Registration</h3>
            <a href="{{ route('admin.documentRegistration') }}">
                <button type="button">Go</button>
            </a>
        </div>

        <div class="card-custom">
            <h3>Send Documents</h3>
            <a href="{{ route('admin.sendDocumentList') }}">
                <button type="button">Go</button>
            </a>
        </div>

        <div class="card-custom">
            <h3>Processed Documents</h3>
            <a href="{{ route('admin.processedDocuments') }}">
                <button type="button">Go</button>
            </a>
        </div>
    </div>
</div>
</body>
</html>
@endsection
