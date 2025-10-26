@extends('layouts.app')

@section('title', 'Processed Documents')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Processed Documents</h1>
        <div class="text-muted">Completed documents returned to admin</div>
      </div>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="card-surface p-3">
      @if($documents->isEmpty())
        <div class="alert alert-info mb-0">No processed documents found.</div>
      @else
        <div class="table-responsive">
          <table class="table table-clean align-middle mb-0">
            <thead>
              <tr>
                <th>Document No</th>
                <th>Title</th>
                <th>Document Owner</th>
                <th>From Department</th>
                <th>Status</th>
                <th class="text-end">Download</th>
              </tr>
            </thead>
            <tbody>
            @foreach($documents as $doc)
              @php
                $lastHistory = $doc->histories()->where('currentDepartmentID', 1)->orderByDesc('created_at')->first();
                $fromDepartment = $lastHistory ? \App\Models\Department::find($lastHistory->prevDepartmentID) : null;
              @endphp
              <tr>
                <td>{{ $doc->documentNo }}</td>
                <td>{{ $doc->title }}</td>
                <td>{{ $doc->owner->username ?? 'Unknown' }} (ID: {{ $doc->ownerID }})</td>
                <td>{{ $fromDepartment ? $fromDepartment->depName : 'Unknown' }}</td>
                <td>{{ $doc->status->statusName ?? 'Unknown' }}</td>
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
