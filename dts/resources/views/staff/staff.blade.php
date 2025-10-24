@extends('layouts.app')
@section('title', 'Staff Dashboard')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Staff Dashboard</h1>
        <div class="text-muted">Process, upload, and route assigned documents</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="ms-auto">
        @csrf
        <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>

    @if(session('success'))
      <div class="alert alert-success card-surface border-0">{{ session('success') }}</div>
    @endif

    @foreach($documents as $doc)
      <div class="card-surface p-3 mb-3">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
          <div>
            <div class="fw-bold">{{ $doc->title }}</div>
            <div class="text-muted">{{ $doc->documentNo }}</div>
            <div class="small mt-1">Status: <span class="fw-semibold">{{ $doc->status->statusName ?? 'Unknown' }}</span></div>
          </div>
          <div>
            @if($doc->filePath)
              <a href="{{ asset('storage/' . $doc->filePath) }}" class="btn btn-brand btn-sm" download><i class="bi bi-download"></i> Download</a>
            @else
              <span class="text-muted">No file available</span>
            @endif
          </div>
        </div>

        <form class="mt-3" method="POST" action="{{ route('staff.processAndRouteDocument', $doc->documentId) }}" enctype="multipart/form-data">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-semibold">Reupload Processed Document</label>
              <input type="file" name="file" class="form-control" required>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Status</label>
              <select name="statusID" class="form-select" required>
                <option value="2">Approved</option>
                <option value="3">Rejected</option>
                <option value="4">In Review</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label fw-semibold">Send To</label>
              <select name="departmentID" class="form-select" required>
                <option value="1">Admin</option>
                @foreach($departments as $dep)
                  <option value="{{ $dep->depID }}">{{ $dep->depName }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mt-3">
            <button type="submit" class="btn btn-brand"><i class="bi bi-send"></i> Send & Upload</button>
          </div>
        </form>
      </div>
    @endforeach
  </div>
</div>
@endsection
