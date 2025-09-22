@extends('layouts.app')
@section('content')
<h2>Document Registration</h2>
@if(session('success'))
    <div>{{ session('success') }}</div>
@endif
<form method="POST" action="{{ route('admin.documentRegistration.submit') }}" enctype="multipart/form-data">
    @csrf
    <div>
        <label for="documentNo">Document No</label>
        <input type="text" id="documentNo" name="documentNo" required>
    </div>
    <div>
        <label for="title">Title</label>
        <input type="text" id="title" name="title" required>
    </div>
    <div>
        <label for="description">Description</label>
        <textarea id="description" name="description"></textarea>
    </div>
    <div>
        <label for="documentType">Document Type</label>
        <input type="text" id="documentType" name="documentType" required>
    </div>
    <div>
        <label for="ownerID">Owner (Username)</label>
        <input type="text" id="ownerInput" name="ownerInput" autocomplete="off" required>
        <input type="hidden" id="ownerID" name="ownerID" required>
        <div id="ownerSuggestions" style="border:1px solid #ccc; display:none; position:absolute; background:#fff; z-index:1000;"></div>
    </div>
    <div>
        <label for="file">Upload Document</label>
        <input type="file" id="file" name="file" required>
    </div>
    <button type="submit">Register Document</button>
</form>
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
@endsection