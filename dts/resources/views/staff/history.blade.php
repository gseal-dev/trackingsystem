@extends('layouts.app')
@section('title', 'Processed Documents')
@section('content')
<div class="page-container">
  <div class="container" style="max-width: 820px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Processed Documents</h1>
        <div class="text-muted">Document History</div>
      </div>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>

    <form method="GET" action="{{ route('staff.history') }}" class="mb-3 d-flex gap-2">
      <input type="text" name="search" class="form-control" placeholder="Search by Document Owner, Name, or ID" value="{{ request('search') }}">
      <button type="submit" class="btn btn-brand"><i class="bi bi-search"></i> Search</button>
    </form>

    @forelse($histories as $history)
      <div class="card-surface p-3 mb-3">
        <div class="fw-bold">{{ $history->document->title ?? 'Unknown' }}</div>
        <div class="text-muted">{{ $history->document->documentNo ?? '' }}</div>
        <div>
          Status: 
          <span class="fw-semibold">
            {{ $history->document->status->statusName ?? 'Unknown' }}
          </span>
        </div>
        <div>
          Owner: 
          <span class="fw-semibold">
            {{ $history->document->owner->username ?? 'Unknown' }}
            ({{ $history->document->owner->firstName ?? '' }} {{ $history->document->owner->lastName ?? '' }})
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
    @empty
      <div class="alert alert-info mb-0">No processed documents found.</div>
    @endforelse
  </div>
</div>
@endsection