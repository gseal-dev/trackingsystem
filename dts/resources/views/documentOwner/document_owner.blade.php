@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Owner Dashboard</title>
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

        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(207,187,153,0.25), rgba(136,144,99,0.2));
            z-index: -1;
        }

        .navbar {
            background-color: var(--kombu-green);
            padding: 15px 0;
        }

        .navbar-brand {
            color: var(--bone) !important;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .dashboard-container {
            min-height: 85vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: start;
            padding: 60px 20px;
            text-align: center;
        }

        .dashboard-container h1 {
            font-size: 2.4rem;
            font-weight: 700;
            color: var(--cafe-noir);
            margin-bottom: 10px;
        }

        .dashboard-container p {
            max-width: 700px;
            font-size: 1.05rem;
            color: var(--kombu-green);
            margin-bottom: 50px;
        }

        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(270px, 1fr));
            gap: 25px;
            width: 100%;
            max-width: 1000px;
        }

        .card-custom {
            background-color: var(--tan);
            border: 2px solid var(--moss-green);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .card-custom h3 {
            color: var(--cafe-noir);
            font-weight: 700;
            margin-bottom: 10px;
        }

        .card-custom p {
            color: var(--kombu-green);
            font-size: 0.95rem;
        }

        .btn-logout {
            margin-top: 50px;
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 10px;
            padding: 12px 35px;
            font-weight: 600;
            transition: 0.3s ease;
        }

        .btn-logout:hover {
            background-color: var(--cafe-noir);
        }

        footer {
            background-color: var(--kombu-green);
            color: var(--bone);
            text-align: center;
            padding: 14px 0;
            font-size: 0.9rem;
            margin-top: 60px;
        }
    </style>
</head>
<body>

    {{-- Navbar --}}
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">📄 Document Tracking System</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn btn-light btn-sm fw-semibold">Logout</button>
            </form>
        </div>
    </nav>

    {{-- Dashboard Content --}}
    <div class="dashboard-container">
        <h1>Welcome, Document Owner 👋</h1>
        <p>Manage and track your submitted documents efficiently. Stay updated on the progress of each document as it moves through the verification and approval process.</p>

        <div class="dashboard-cards">
            <div class="card-custom">
                <h3>📦 Submitted Documents</h3>
                <p>Track your recently submitted documents and view their status.</p>
            </div>

            <div class="card-custom">
                <h3>🕒 Pending Approvals</h3>
                <p>View documents that are currently under review or awaiting signature.</p>
            </div>

            <div class="card-custom">
                <h3>✅ Completed</h3>
                <p>Access documents that have been fully processed and approved.</p>
            </div>
        </div>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout mt-5">Logout</button>
        </form>
    </div>

    <footer>
        © 2025 Document Tracking System | Designed by CIT @4A
    </footer>
</body>
</html>
@endsection
