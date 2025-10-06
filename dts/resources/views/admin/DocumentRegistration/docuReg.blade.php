@extends('layouts.app')
@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Registration</title>
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
            max-width: 800px;
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
            padding: 30px;
            box-shadow: 0 6px 18px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="file"],
        input[type="email"],
        textarea,
        select,
        input[type="password"] {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid var(--moss-green);
            border-radius: 8px;
            font-size: 0.95rem;
        }

        textarea {
            resize: vertical;
        }

        .btn-custom {
            background-color: var(--kombu-green);
            color: var(--bone);
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
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

        #ownerSuggestions div:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<div class="container-custom">
    <h2>Document Registration</h2>

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    <div class="card-custom">
        <form method="POST" action="{{ route('admin.documentRegistration.submit') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label for="documentNo">Document No</label>
                <input type="text" id="documentNo" name="documentNo" required>
            </div>

            <div class="form-group">
                <label for="title">Title</label>
                <input type="text" id="title" name="title" required>
            </div>

            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description"></textarea>
            </div>

            <div class="form-group">
                <label for="documentType">Document Type</label>
                <input type="text" id="documentType" name="documentType" required>
            </div>

            <div class="form-group" style="position:relative;">
                <label for="ownerID">Owner (Username)</label>
                <input type="text" id="ownerInput" name="ownerInput" autocomplete="off" required>
                <input type="hidden" id="ownerID" name="ownerID" required>
                <div id="ownerSuggestions" style="border:1px solid #ccc; display:none; position:absolute; background:#fff; z-index:1000;"></div>
            </div>

            <div class="form-group">
                <label for="file">Upload Document</label>
                <input type="file" id="file" name="file" required>
            </div>

            <button type="submit" class="btn-custom">Register Document</button>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('ownerInput');
    const hidden = document.getElementById('ownerID');
    const suggestions = document.getElementById('ownerSuggestions');

    input.addEventListener('input', function() {
        const q = input.value;
        if (q.length < 1) {
            suggestions.style.display = 'none';
            return;
        }
        fetch('/admin/user-search?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(user => {
                        const div = document.createElement('div');
                        div.textContent = user.username + ' (ID: ' + user.userID + ')';
                        div.style.cursor = 'pointer';
                        div.onclick = function() {
                            input.value = user.username;
                            hidden.value = user.userID;
                            suggestions.style.display = 'none';
                        };
                        suggestions.appendChild(div);
                    });
                    suggestions.style.display = 'block';
                } else {
                    suggestions.style.display = 'none';
                }
            });
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!suggestions.contains(e.target) && e.target !== input) {
            suggestions.style.display = 'none';
        }
    });
});
</script>

</body>
</html>
@endsection
