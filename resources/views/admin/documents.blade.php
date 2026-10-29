@extends('layouts.app')

@section('title', 'Admin Documents List')

@push('head')
<style>
  .documents-section { margin-top: 2.5rem; }
  .documents-section .section-subtitle { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.02em; margin: 0; }
  .table-toolbar {
    margin-bottom: 1.5rem;
  }

  .search-box {
    position: relative;
    width: 100%;
  }

  /* Remove rounded pill borders and make fields standard/square */
  .search-box input, .form-select, .form-control {
    border-radius: 4px !important;
    border-color: #cbd5e1;
  }

  .search-box input {
    padding-left: 2.75rem;
    height: 48px;
  }

  .form-select, .form-control {
    height: 48px;
    padding-left: 1rem;
  }

  .search-box i {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #9ca3af;
  }

  .actions-cell {
    display: flex;
    gap: 0.5rem;
  }

  .btn-action {
    width: 36px;
    height: 36px;
    padding: 0;
    display: grid;
    place-items: center;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    background: #fff;
    color: #4b5563;
    transition: all 0.2s;
  }

  .btn-action:hover {
    background: #f9fafb;
    border-color: #d1d5db;
    color: #111827;
  }

  .btn-create {
    background: #ea3a14;
    color: #fff;
    padding: 0.625rem 1.25rem;
    border-radius: 12px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.82rem;
    letter-spacing: 0.04em;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: background 0.2s;
  }

  .btn-create:hover {
    background: #1f2937;
    color: #fff;
  }

  .table th {
    text-transform: uppercase;
    font-size: 0.82rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    color: #374151;
    background-color: #f8fafc !important;
    padding: 0.5rem 1rem !important;
    border-bottom: 2px solid #e5e7eb !important;
  }

  .table td {
    padding: 0.2rem 1rem !important;
  }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
      <div class="page-card">
        <div class="page-card-header"><i class="bi bi-folder2-open"></i> Admin Documents List</div>
        
        <!-- Section Header with Title and Export Button -->
        <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
          <div class="d-flex flex-column gap-1">
            <h2 class="section-subtitle mb-1">Documents</h2>
            <p class="text-muted mb-0 small">Add and delete documents</p>
          </div>
          <div class="d-flex gap-2 align-items-center">
            <a href="{{ route('admin.documents.export', request()->only(['year', 'month', 'day', 'q', 'date_from', 'date_to', 'department'])) }}" class="btn btn-outline-success fw-bold px-3 py-2 rounded-2 d-inline-flex align-items-center gap-2" id="export-documents-btn" style="border-width: 2px;" title="Exports every document matching the current filters">
              <i class="bi bi-file-earmark-spreadsheet"></i>
              <span>Export Excel</span>
            </a>
          </div>
        </div>

        {{-- Active filter chips --}}
        @php
          $hasFilters = request()->filled(['q', 'date_from', 'date_to', 'department']);
        @endphp

        @if($hasFilters)
          <div class="mb-3">
            <div class="d-flex flex-wrap gap-2 align-items-center">
              <span class="text-muted small fw-semibold">Active filters:</span>
              @if(request('q'))
                <a href="{{ route('admin.documents', array_filter(array_merge(request()->query(), ['q' => null]))) }}" class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 text-decoration-none small">
                  <span>{{ e(request('q')) }}</span>
                  <i class="bi bi-x-circle fw-bold" style="font-size: 0.65rem;"></i>
                </a>
              @endif
              @if(request('date_from') || request('date_to'))
                <a href="{{ route('admin.documents', array_filter(array_merge(request()->query(), ['date_from' => null, 'date_to' => null]))) }}" class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 text-decoration-none small">
                  <span>{{ request('date_from') ?? 'Any' }} → {{ request('date_to') ?? 'Any' }}</span>
                  <i class="bi bi-x-circle fw-bold" style="font-size: 0.65rem;"></i>
                </a>
              @endif
              @if(request('department'))
                @php $deptName = \App\Models\Department::where('depID', request('department'))->value('depName') ?? request('department'); @endphp
                <a href="{{ route('admin.documents', array_filter(array_merge(request()->query(), ['department' => null]))) }}" class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 text-decoration-none small">
                  <span>{{ e($deptName) }}</span>
                  <i class="bi bi-x-circle fw-bold" style="font-size: 0.65rem;"></i>
                </a>
              @endif
              <a href="{{ route('admin.documents') }}" class="btn btn-outline-secondary btn-sm text-decoration-none">Clear all</a>
            </div>
          </div>
        @endif

        <div class="table-toolbar">
          <form method="GET" action="{{ route('admin.documents') }}" class="w-100" id="admin-docs-filter">
            <!-- Search bar on top, full width -->
            <div class="mb-3">
              <label class="form-label fw-bold text-uppercase small text-secondary mb-1">
                <i class="bi bi-search me-1"></i> Search Everything You Have Access To
              </label>
              <div class="search-box w-100">
                <i class="bi bi-search"></i>
                <input type="search" id="admin-doc-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search reference number, subject, office, type..." autocomplete="off">
              </div>
            </div>

            <!-- Other filters below the search bar with labels on top -->
            <div class="row g-3 align-items-end">
              <div class="col-md-2">
                <label class="form-label fw-bold small text-muted mb-1"><i class="bi bi-calendar-event me-1"></i> Doc. Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control form-control-sm">
              </div>

              <div class="col-md-2">
                <label class="form-label fw-bold small text-muted mb-1"><i class="bi bi-calendar-event me-1"></i> Doc. Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control form-control-sm">
              </div>

              <div class="col-md-3">
                <label class="form-label fw-bold small text-muted mb-1"><i class="bi bi-building me-1"></i> Department</label>
                <select name="department" class="form-select">
                  <option value="">All Departments</option>
                  @foreach($departments as $dept)
                    <option value="{{ $dept->depID }}" {{ request('department') == $dept->depID ? 'selected' : '' }}>{{ $dept->depName }}</option>
                  @endforeach
                </select>
              </div>

              <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-outline-secondary w-50" style="height: 48px; border-radius: 4px;">Apply</button>
                <a href="{{ route('admin.documents') }}" class="btn btn-outline-danger w-50 d-inline-flex align-items-center justify-content-center" style="height: 48px; border-radius: 4px;">Reset</a>
              </div>
            </div>

            <!-- Rows per page helper bar -->
            <div class="d-flex align-items-center justify-content-end mt-3">
              <div class="d-flex align-items-center gap-2">
                <label for="admin-per-page" class="small text-muted mb-0">Rows per page</label>
                <select name="per_page" id="admin-per-page" class="form-select form-select-sm" style="width: 80px; height: 38px;" onchange="this.form.submit()">
                  @foreach(\App\Support\DocumentList::PER_PAGE_OPTIONS as $n)
                    <option value="{{ $n }}" {{ $documents->perPage() == $n ? 'selected' : '' }}>{{ $n }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </form>
        </div>

        <div class="mb-3" id="admin-docs-count" data-live-refresh>
          <div class="d-inline-flex align-items-center px-4 py-2 text-white fw-bold shadow-sm" style="background-color: #ea3a14; border-radius: 4px; font-size: 0.9rem;">
            <i class="bi bi-file-earmark-text-fill me-2"></i> {{ number_format($documents->total()) }} Documents Found
          </div>
        </div>

        <div class="card border rounded-4 bg-white shadow-sm overflow-hidden p-3">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Reference Number</th>
                  <th>From Office</th>
                  <!-- <th>Type</th> -->
                  <th>Subject</th>
                  <th>Uploaded By</th>
                  <th>Date</th>
                  <th>Upload Date</th>
                  <th class="text-end">Actions</th>
                </tr>
              </thead>
              <tbody id="admin-docs-body" data-live-refresh>
                @forelse($documents as $doc)
                  @php
                    $uploader = $doc->owner
                      ? ($doc->owner->username ?: trim(($doc->owner->firstName ?? '') . ' ' . ($doc->owner->lastName ?? '')))
                      : 'Unknown';
                  @endphp
                  <tr>
                    <td class="fw-bold">{{ $doc->documentNo }}</td>
                    <td>{{ $doc->department->depName ?? '-' }}</td>
                    <td>{{ $doc->title }}</td>
                    <td>{{ $uploader }}</td>
                    <td class="text-muted">{{ $doc->documentDate ? \Carbon\Carbon::parse($doc->documentDate)->format('F d, Y') : $doc->created_at?->format('F d, Y') }}</td>
                    <td class="text-muted">{{ $doc->created_at ? \Carbon\Carbon::parse($doc->created_at)->format('F d, Y') : '-' }}</td>
                    <td>
                      <div class="actions-cell justify-content-end">
                        <button type="button" class="btn-action" title="View details" data-doc-view="{{ json_encode($doc->viewData()) }}">
                          <i class="bi bi-eye"></i>
                        </button>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center py-5 text-muted">
                      <i class="bi bi-folder-x display-4 mb-3 d-block"></i>
                      No documents found.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <div id="admin-docs-pager" data-live-refresh>
          @include('partials.documents-pager')
        </div>
      </div>
    </div>
  </div>

@include('partials.document-view-modal', ['canDelete' => true])

@push('scripts')
<script>
  // Search runs on the server (it has to look through every page, not just the 25 rows on screen)
  (function () {
    const input = document.getElementById('admin-doc-search');
    let timer = null;
    input.addEventListener('input', function () {
      clearTimeout(timer);
      timer = setTimeout(function () { input.form.submit(); }, 450);
    });
    if (input.value) {
      input.focus({ preventScroll: true });
      input.setSelectionRange(input.value.length, input.value.length);
    }
  })();
</script>
@endpush

@endsection