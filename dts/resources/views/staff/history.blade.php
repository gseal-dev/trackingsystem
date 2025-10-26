@extends('layouts.app')
@section('title', 'Processed Documents')
@section('content')
<div class="page-container">
  <div class="page-container">
  <div class="container" style="max-width: 820px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Processed Documents</h1>
        <div class="text-muted">Document History</div>
      </div>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>
    @foreach($histories as $history)
      <div class="card-surface p-3 mb-3">
        <div class="fw-bold">{{ $history->document->title ?? 'Unknown' }}</div>
        <div class="text-muted">{{ $history->document->documentNo ?? '' }}</div>
        <div>
          Status: 
          <span class="fw-semibold">
            {{ $history->document->status->statusName ?? 'Unknown' }}
          </span>
        </div>
        <div>Action: {{ $history->action }}</div>
        <div>Date: {{ $history->created_at }}</div>
        <div class="mt-2">
          @if($history->document && $history->document->filePath)
            <a href="{{ asset('storage/' . $history->document->filePath) }}" class="btn btn-brand btn-sm" download>
              <i class="bi bi-download"></i> Download Processed File
            </a>
          @else
            <span class="text-muted">No file available</span>
          @endif
        </div>
      </div>
    @endforeach
  </div>
</div>
@endsection