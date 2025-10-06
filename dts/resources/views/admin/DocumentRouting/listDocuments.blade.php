@extends('layouts.app')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Documents</title>
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
            margin-bottom: 25px;
            text-align: center;
        }

        .card-custom {
            background-color: var(--tan);
            border: 2px solid var(--moss-green);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th, td {
            border: 1px solid var(--moss-green);
            padding: 12px 15px;
            font-size: 0.95rem;
        }

        th {
            background-color: var(--moss-green);
            color: var(--bone);
            text-align: left;
        }

        td {
            background-color: var(--bone);
        }

        .btn-custom {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 8px;
            padding: 6px 16px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.3s ease;
        }

        .btn-custom:hover {
            background-color: var(--cafe-noir);
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-radius: 8px;
            padding: 10px 15px;
            margin-bottom: 20px;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-radius: 8px;
            padding: 10px 15px;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container-custom">
    <h2>Send Documents</h2>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card-custom">
        @if($documents->isEmpty())
            <div class="alert-info">No documents available to send.</div>
        @else
            <table>
                <tr>
                    <th>Document No</th>
                    <th>Title</th>
                    <th>Document Owner</th>
                    <th>Action</th>
                </tr>
                @foreach($documents as $doc)
                <tr>
                    <td>{{ $doc->documentNo }}</td>
                    <td>{{ $doc->title }}</td>
                    <td>{{ $doc->owner->username ?? 'Unknown' }} (ID: {{ $doc->ownerID }})</td>
                    <td>
                        <a href="{{ route('admin.sendDocumentForm', $doc->documentId) }}">
                            <button type="button" class="btn-custom">Send</button>
                        </a>
                    </td>
                </tr>
                @endforeach
            </table>
        @endif
    </div>
</div>

</body>
</html>
@endsection
