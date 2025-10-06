<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Tracking System - Welcome</title>
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
            position: relative;
            overflow-x: hidden;
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

        .hero {
            min-height: 85vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 0 20px;
        }

        .hero h1 {
            font-size: 2.8rem;
            font-weight: 700;
            color: var(--cafe-noir);
        }

        .hero p {
            max-width: 700px;
            margin-top: 15px;
            color: var(--kombu-green);
            font-size: 1.1rem;
        }

        .hero-buttons {
            margin-top: 35px;
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn-primary-custom {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 12px;
            padding: 12px 32px;
            font-weight: 600;
            font-size: 1rem;
            transition: 0.3s ease;
        }

        .btn-primary-custom:hover {
            background-color: var(--cafe-noir);
        }

        .btn-secondary-custom {
            background-color: var(--bone);
            color: var(--kombu-green);
            border: 2px solid var(--kombu-green);
            border-radius: 12px;
            padding: 12px 32px;
            font-weight: 600;
            font-size: 1rem;
            transition: 0.3s ease;
        }

        .btn-secondary-custom:hover {
            background-color: var(--moss-green);
            color: var(--bone);
        }

        footer {
            background-color: var(--kombu-green);
            color: var(--bone);
            text-align: center;
            padding: 14px 0;
            font-size: 0.9rem;
        }

        @media (max-width: 576px) {
            .hero h1 {
                font-size: 2rem;
            }
            .hero p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container">
            <a class="navbar-brand" href="#">📄 Document Tracking System</a>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to the Document Tracking System</h1>
        <p>
            Efficiently manage, monitor, and track your important documents across departments with transparency and ease. 
            Simplify your workflow and ensure every document finds its destination.
        </p>

        <div class="hero-buttons">
            <a href="{{ route('login') }}" class="btn btn-primary-custom">Login</a>
            <a href="{{ route('register') }}" class="btn btn-secondary-custom">Register</a>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        © 2025 Document Tracking System | Designed by CIT @4A 
    </footer>
</body>
</html>
