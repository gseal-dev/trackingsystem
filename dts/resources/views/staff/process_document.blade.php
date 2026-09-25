@extends('layouts.app')
@section('title', 'Process Document')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 700px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Process Document</h1>
        <div class="text-muted">{{ $document->title }}</div>
      </div>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="card-surface p-4">
      <form method="POST" action="{{ route('staff.processAndRouteDocument', $document->documentId) }}" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
          <label class="form-label fw-semibold">Reupload Processed Document</label>
          <input type="file" name="file" class="form-control" required>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Status</label>
          <select name="statusID" class="form-select" required>
            <option value="2">Approved</option>
            <option value="3">Rejected</option>
            <option value="4">In Review</option>
          </select>
        </div>
        <div class="mb-3">
          <label class="form-label fw-semibold">Send To</label>
          <select name="departmentID" class="form-select" required>
            <option value="1">Admin</option>
            @foreach($departments as $dep)
              <option value="{{ $dep->depID }}">{{ $dep->depName }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-brand"><i class="bi bi-send"></i> Send & Upload</button>
      </form>
    </div>
  </div>
</div>
@endsection