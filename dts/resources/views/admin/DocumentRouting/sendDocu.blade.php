@extends('layouts.app')

@section('title', 'Send Document')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 640px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Send Document</h1>
        <div class="text-muted">{{ $document->title }}</div>
      </div>
        <a href="{{ route('admin.sendDocumentList') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to List</a>
    </div>

    <div class="card-surface p-4">
      <form method="POST" action="{{ route('admin.sendDocument', $document->documentId) }}">
        @csrf
        <div class="mb-3">
          <label for="departmentID" class="form-label fw-semibold">Select Department</label>
          <select id="departmentID" name="departmentID" class="form-select" required>
            @foreach($departments as $dep)
              <option value="{{ $dep->depID }}">{{ $dep->depName }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-brand"><i class="bi bi-send"></i> Send</button>
      </form>
    </div>
  </div>
</div>
@endsection
