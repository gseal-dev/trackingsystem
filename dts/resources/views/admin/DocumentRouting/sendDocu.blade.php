@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Document</title>
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
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
        }

        h2 {
            color: var(--cafe-noir);
            font-weight: 700;
            margin-bottom: 25px;
            text-align: center;
        }

        .card-custom {
            background-color: var(--tan);
            border: 2px solid var(--moss-green);
            border-radius: 15px;
            padding: 30px 25px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        label {
            font-weight: 600;
            margin-top: 10px;
            display: block;
        }

        select {
            width: 100%;
            padding: 10px 12px;
            margin-top: 5px;
            margin-bottom: 20px;
            border-radius: 8px;
            border: 1px solid var(--moss-green);
            font-size: 0.95rem;
        }

        button {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background-color: var(--cafe-noir);
        }
    </style>
</head>
<body>

<div class="container-custom">
    <h2>Send Document: {{ $document->title }}</h2>
    <div class="card-custom">
        <form method="POST" action="{{ route('admin.sendDocument', $document->documentId) }}">
            @csrf

            <label for="departmentID">Select Department</label>
            <select id="departmentID" name="departmentID" required>
                @foreach($departments as $dep)
                    <option value="{{ $dep->depID }}">{{ $dep->depName }}</option>
                @endforeach
            </select>

            <button type="submit">Send</button>
        </form>
    </div>
</div>

</body>
</html>
@endsection
