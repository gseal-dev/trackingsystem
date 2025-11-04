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
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
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
                <div class="small mt-1">
                  Owner: 
                  <span class="fw-semibold">
                    {{ $doc->owner->username ?? 'Unknown' }} 
                    ({{ $doc->owner->firstName ?? '' }} {{ $doc->owner->lastName ?? '' }})
                  </span>
                </div>
            </div>
            <div>
                @if($doc->filePath)
                <a href="{{ asset('storage/' . $doc->filePath) }}" class="btn btn-brand btn-sm" download><i class="bi bi-download"></i> Download</a>
                @else
                <span class="text-muted">No file available</span>
                @endif
            </div>
            </div>
            <div class="mt-3">
            <a href="{{ route('staff.processDocumentForm', $doc->documentId) }}" class="btn btn-success">
                <i class="bi bi-pencil-square"></i> Process Document
            </a>
            </div>
        </div>
    @endforeach
  </div>
</div>
@endsection