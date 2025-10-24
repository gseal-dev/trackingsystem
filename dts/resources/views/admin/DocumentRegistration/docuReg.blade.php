@extends('layouts.app')
@section('title', 'Document Registration')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 820px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Document Registration</h1>
        <div class="text-muted">Register new incoming documents</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="ms-auto">
        @csrf
        <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>

    @if(session('success'))
      <div class="alert alert-success card-surface border-0">{{ session('success') }}</div>
    @endif

    <div class="card-surface p-4">
      <form method="POST" action="{{ route('admin.documentRegistration.submit') }}" enctype="multipart/form-data">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label for="documentNo" class="form-label fw-semibold">Document No</label>
            <input type="text" id="documentNo" name="documentNo" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label for="documentType" class="form-label fw-semibold">Document Type</label>
            <input type="text" id="documentType" name="documentType" class="form-control" required>
          </div>
          <div class="col-12">
            <label for="title" class="form-label fw-semibold">Title</label>
            <input type="text" id="title" name="title" class="form-control" required>
          </div>
          <div class="col-12">
            <label for="description" class="form-label fw-semibold">Description</label>
            <textarea id="description" name="description" class="form-control" rows="4"></textarea>
          </div>
          <div class="col-12 position-relative">
            <label for="ownerID" class="form-label fw-semibold">Owner (Username)</label>
            <input type="text" id="ownerInput" name="ownerInput" class="form-control" autocomplete="off" required>
            <input type="hidden" id="ownerID" name="ownerID" required>
            <div id="ownerSuggestions" class="card-surface" style="display:none; position:absolute; inset:auto 0 0 0; transform:translateY(100%); z-index:1000;"></div>
          </div>
          <div class="col-12">
            <label for="file" class="form-label fw-semibold">Upload Document</label>
            <input type="file" id="file" name="file" class="form-control" required>
          </div>
        </div>
        <div class="mt-3">
          <button type="submit" class="btn btn-brand"><i class="bi bi-journal-plus"></i> Register Document</button>
        </div>
      </form>
    </div>
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
@endsection
