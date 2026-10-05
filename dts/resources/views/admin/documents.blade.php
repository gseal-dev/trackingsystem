@extends('layouts.app')

@section('title', 'Admin Documents')

@push('head')
<style>
  .dashboard-shell {
    width: 100%;
    min-height: calc(100vh - 100px);
    padding: clamp(1rem, 2vw, 2rem);
  }

  .dashboard-container {
    width: 100%;
    max-width: 100%;
    margin: 0 auto;
  }

  .staff-layout {
    display: grid;
    grid-template-columns: 240px 1fr;
    gap: 2rem;
    align-items: start;
  }

  .staff-sidebar {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 1.5rem;
    position: sticky;
    top: 2rem;
  }

  .staff-nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
  }

  .staff-nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 12px;
    color: #4b5563;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.2s;
  }

  .staff-nav-link:hover {
    background: #f3f4f6;
    color: #111827;
  }

  .staff-nav-link.active {
    background: #001253;
    color: #fff;
  }

  .staff-main-content {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 24px;
    padding: 2rem;
    min-height: 600px;
  }

  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 1rem;
  }

  .section-title {
    font-size: 1.875rem;
    font-weight: 800;
    letter-spacing: -0.025em;
    margin: 0;
  }

  .table-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
    gap: 1rem;
    flex-wrap: wrap;
  }

  .search-box {
    position: relative;
    max-width: 320px;
    width: 100%;
  }

  .search-box input {
    padding-left: 2.5rem;
    height: 44px;
  }

  .search-box i {
    position: absolute;
    left: 1rem;
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

  .table th {
    text-transform: uppercase;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    color: #646771;
  }

  @media (max-width: 1024px) {
    .staff-layout {
      grid-template-columns: 1fr;
    }
    .staff-sidebar {
      position: static;
    }
    .staff-nav-list {
      flex-direction: row;
      overflow-x: auto;
    }
  }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
      <main class="staff-main-content bg-white p-4 p-md-5 rounded-4 border">
        <div class="section-header">
          <div>
            <h1 class="section-title">Documents List</h1>
            <p class="text-muted">Manage and track all registered documents</p>
          </div>
        </div>

        <div class="table-toolbar">
          <form method="GET" action="{{ route('admin.documents') }}" class="d-flex gap-2 flex-wrap align-items-center w-100">
            <div class="search-box flex-grow-1" style="max-width: 320px;">
              <i class="bi bi-search"></i>
              <input type="text" id="admin-doc-search" class="form-control" placeholder="Search by contract name, location, or contractor...">
            </div>
            <select name="year" class="form-select" style="width: 130px;" onchange="this.form.submit()">
              <option value="">Year: All</option>
              @for($y = 2017; $y <= 2026; $y++)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endfor
            </select>
            <select name="month" class="form-select" style="width: 140px;" onchange="this.form.submit()">
              <option value="">Month: All</option>
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
              @endfor
            </select>
            <select name="day" class="form-select" style="width: 120px;" onchange="this.form.submit()">
              <option value="">Day: All</option>
              @for($d = 1; $d <= 31; $d++)
                <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>Day {{ $d }}</option>
              @endfor
            </select>
            @if(request('year') || request('month') || request('day'))
              <a href="{{ route('admin.documents') }}" class="btn btn-outline-secondary px-3 py-2" style="border-radius: 50px;">Reset</a>
            @endif
          </form>
        </div>

        <p class="text-muted small fst-italic mb-3">*Project information, documents, and records are continuously being tracked. Thank you for your patience.*</p>

        <div class="mb-3">
          <div class="d-inline-flex align-items-center px-4 py-2 text-white fw-bold shadow-sm" style="background-color: #ea3a14; border-radius: 50px; font-size: 0.9rem;">
            <i class="bi bi-file-earmark-text-fill me-2"></i> {{ count($documents) }} Documents Found
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover table-sm align-middle">
            <thead class="bg-light">
              <tr>
                <th class="border-0">Reference Number</th>
                <th class="border-0">Office</th>
                <th class="border-0">Type</th>
                <th class="border-0">Subject</th>
                <th class="border-0">Uploaded By</th>
                <th class="border-0">Date</th>
                <th class="border-0 text-end">Actions</th>
              </tr>
            </thead>
            <tbody id="admin-docs-body" data-live-refresh>
              @forelse($documents as $doc)
                @php
                  $uploaderName = $doc->owner ? ($doc->owner->username ?: trim(($doc->owner->firstName ?? '') . ' ' . ($doc->owner->lastName ?? ''))) : 'Unknown';
                @endphp
                <tr data-doc-search="{{ strtolower($doc->documentNo . ' ' . ($doc->department->depName ?? '') . ' ' . $doc->documentType . ' ' . $doc->title . ' ' . $uploaderName) }}">
                  <td class="fw-bold">{{ $doc->documentNo }}</td>
                  <td>{{ $doc->department->depName ?? '-' }}</td>
                  <td style="text-transform: uppercase;">{{ $doc->documentType }}</td>
                  <td>{{ $doc->title }}</td>
                  <td>{{ $uploaderName }}</td>
                  <td class="text-muted">{{ $doc->documentDate ? \Carbon\Carbon::parse($doc->documentDate)->format('F d, Y') : $doc->created_at?->format('F d, Y') }}</td>
                  <td>
                    <div class="actions-cell justify-content-end">
                      @if($doc->filePath)
                      <a href="{{ asset('storage/' . $doc->filePath) }}" class="btn-action" target="_blank" title="View PDF">
                        <i class="bi bi-eye"></i>
                      </a>
                      @endif
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center py-5 text-muted">
                    <i class="bi bi-folder-x display-4 mb-3 d-block"></i>
                    No documents found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </main>
    </div>
  </div>
</div>

@push('scripts')
<script>
  function applyAdminSearch() {
    const search = document.getElementById('admin-doc-search').value.trim().toLowerCase();
    document.querySelectorAll('#admin-docs-body tr[data-doc-search]').forEach(function (row) {
      row.hidden = !row.dataset.docSearch.includes(search);
    });
  }
  document.getElementById('admin-doc-search').addEventListener('input', applyAdminSearch);
  document.addEventListener('live-refreshed', applyAdminSearch);
</script>
@endpush
@endsection
