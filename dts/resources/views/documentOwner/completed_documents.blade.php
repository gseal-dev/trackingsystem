@extends('layouts.app')

@section('title', 'Completed Documents')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Completed Documents</h1>
        <div class="text-muted">View the list of your completed documents</div>
      </div>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>

    <form method="GET" action="{{ route('documentOwner.completedDocuments') }}" class="mb-3 d-flex gap-2">
      <input type="text" name="search" class="form-control" placeholder="Search by Title or Document No" value="{{ request('search') }}">
      <button type="submit" class="btn btn-brand"><i class="bi bi-search"></i> Search</button>
    </form>

    <div class="card-surface p-3">
      @if($documents->isEmpty())
        <div class="alert alert-info mb-0">No completed documents found.</div>
      @else
        <div class="table-responsive">
          <table class="table table-clean align-middle mb-0">
            <thead>
              <tr>
                <th>Document No</th>
                <th>Title</th>
                <th>Status</th>
                <th>Last Updated</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
            @foreach($documents as $doc)
              <tr>
                <td>{{ $doc->documentNo }}</td>
                <td>{{ $doc->title }}</td>
                <td>{{ $doc->status->statusName ?? 'Unknown' }}</td>
                <td>{{ $doc->updated_at ? $doc->updated_at->format('Y-m-d H:i:s') : 'N/A' }}</td>
                <td class="text-end">
                  @if($doc->filePath)
                    <a href="{{ asset('storage/' . $doc->filePath) }}" class="btn btn-brand btn-sm" download>
                      <i class="bi bi-download"></i> Download
                    </a>
                  @else
                    <span class="text-muted">No file</span>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>
  </div>
</div>
@endsection