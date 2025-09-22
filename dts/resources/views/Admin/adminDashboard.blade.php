@extends('layout')
@section('title', 'Admin Dashboard')
@section('content')
    <h2>Admin Dashboard</h2>
    

    {{-- Document Registration Form --}}
    <form action="{{ route('docuReg.post') }}" method="POST" enctype="multipart/form-data" class="ms-auto me-auto mt-5" style="width: 500px">
        @csrf

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        {{-- Document Owner Autocomplete --}}
        <div class="mb-3">
            <label class="form-label">Document Owner (Username)</label>
            <input type="text" class="form-control" id="owner-search" placeholder="Type username..." autocomplete="off" required>
            <input type="hidden" name="ownerID" id="owner-id" required>
            <div id="owner-suggestions" class="list-group"></div>
        </div>

        <div class="mb-3">
            <label class="form-label">Document Title</label>
            <input type="text" class="form-control" name="title" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea class="form-control" name="description" rows="3"></textarea>
        </div>
        <div class="mb-3">
            <label class="form-label">Document Type</label>
            <select class="form-control" name="documentType" required>
                <option value="">Select Type</option>
                <option value="Memo">Memo</option>
                <option value="Report">Report</option>
                <option value="Letter">Letter</option>
                <option value="Proposal">Proposal</option>
                <option value="Instructions">Instructions</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Upload File (PDF or Word)</label>
            <input type="file" class="form-control" name="file" accept=".pdf,.doc,.docx" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload Document</button>
    </form>
    <a href="{{ route('admin.users.index') }}" class="btn btn-success mb-3">User Management</a>
    <form action="{{ route('logout') }}" method="POST" style="display:inline;">
            @csrf
            <button type="submit" class="btn btn-secondary mb-3">Logout</button>
        </form>
    {{-- Autocomplete Script --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('owner-search');
            const suggestions = document.getElementById('owner-suggestions');
            const ownerIdInput = document.getElementById('owner-id');

            searchInput.addEventListener('input', function () {
                const query = this.value;
                if (query.length < 1) {
                    suggestions.innerHTML = '';
                    return;
                }
                fetch(`/admin/document-owners/search?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        suggestions.innerHTML = '';
                        data.forEach(owner => {
                            const item = document.createElement('a');
                            item.className = 'list-group-item list-group-item-action';
                            item.textContent = `${owner.username} (${owner.email})`;
                            item.href = '#';
                            item.onclick = function (e) {
                                e.preventDefault();
                                searchInput.value = owner.username;
                                ownerIdInput.value = owner.userID;
                                suggestions.innerHTML = '';
                            };
                            suggestions.appendChild(item);
                        });
                    });
            });

            // Clear ownerID if input is changed manually
            searchInput.addEventListener('change', function () {
                if (!this.value) {
                    ownerIdInput.value = '';
                }
            });
        });
        </script>
@endsection