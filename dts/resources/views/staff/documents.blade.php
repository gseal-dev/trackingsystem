@extends('layouts.app')
@section('title', 'Staff Documents')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Staff Documents</h1>
        <div class="text-muted">Process, upload, and route assigned documents</div>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('staff.document.create') }}" class="btn btn-brand"><i class="bi bi-plus-lg"></i> Add Document</a>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
      </div>
    </div>

    @if(session('success'))
      <div class="alert alert-success card-surface border-0">{{ session('success') }}</div>
    @endif

    @foreach($documents as $doc)
        <div class="card-surface p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <div class="fw-bold fs-5 mb-1">Subject: {{ $doc->title }}</div>
                <div class="text-muted mb-1"><strong class="text-dark">Reference Number:</strong> {{ $doc->documentNo }}</div>
                <div class="text-muted mb-1"><strong class="text-dark">From Office:</strong> {{ $doc->department->depName ?? '-' }}</div>
                <div class="text-muted mb-1"><strong class="text-dark">Type:</strong> <span class="badge bg-light text-dark border">{{ $doc->documentType }}</span></div>
                <div class="text-muted mb-1"><strong class="text-dark">Date:</strong> {{ $doc->created_at?->format('F d, Y') }}</div>
                <div class="small mt-1">Status: <span class="fw-semibold">{{ $doc->status->statusName ?? 'Unknown' }}</span></div>
            </div>
            <div>
                @if($doc->filePath)
                <div class="d-flex gap-2">
                  <a href="{{ asset('storage/' . $doc->filePath) }}" class="btn btn-outline-secondary btn-sm" target="_blank"><i class="bi bi-eye"></i> View</a>
                  <a href="{{ asset('storage/' . $doc->filePath) }}" class="btn btn-brand btn-sm" download><i class="bi bi-download"></i> Download</a>
                </div>
                @else
                <span class="text-muted">No file available</span>
                @endif
            </div>
            </div>
            <div class="mt-3 d-flex gap-2 align-items-center flex-wrap">
              <a href="{{ route('staff.processDocumentForm', $doc->documentId) }}" class="btn btn-success btn-sm">
                  <i class="bi bi-gear"></i> Process Document
              </a>
              <a href="{{ route('staff.document.edit', $doc->documentId) }}" class="btn btn-outline-secondary btn-sm" title="Edit Document">
                  <i class="bi bi-pencil"></i> Edit
              </a>
              <form action="{{ route('staff.document.delete', $doc->documentId) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this document?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-outline-danger btn-sm" title="Delete Document">
                      <i class="bi bi-trash"></i> Delete
                  </button>
              </form>
            </div>
        </div>
    @endforeach
  </div>
</div>
@endsection