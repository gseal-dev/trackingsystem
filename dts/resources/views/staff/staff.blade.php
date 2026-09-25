@extends('layouts.app')

@section('title', 'Staff Dashboard')

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
    background: #111827;
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

  .btn-action.btn-delete:hover {
    background: #fee2e2;
    border-color: #fecaca;
    color: #dc2626;
  }

  .btn-create {
    background: #111827;
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

  .table th {
    text-transform: uppercase;
    font-size: 0.78rem;
    font-weight: 800;
    letter-spacing: 0.05em;
    color: #646771;
  }

  .btn-create:hover {
    background: #1f2937;
    color: #fff;
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
  <div class="dashboard-container">
    <div class="staff-layout">
      <aside class="staff-sidebar">
        <nav class="staff-nav">
          <ul class="staff-nav-list">
            <li>
              <a href="{{ route('dashboard') }}" class="staff-nav-link active">
                <i class="bi bi-grid-fill"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li>
              <a href="{{ route('staff.history') }}" class="staff-nav-link">
                <i class="bi bi-clock-history"></i>
                <span>History</span>
              </a>
            </li>
          </ul>
        </nav>
      </aside>

      <main class="staff-main-content">
        <div class="section-header">
          <div>
            <h1 class="section-title">Staff Records</h1>
            <p class="text-muted">Manage and track your assigned documents</p>
          </div>
          <div class="d-flex gap-2 align-items-center flex-wrap">
            <a href="{{ route('staff.document.create') }}" class="btn-create">
              <i class="bi bi-plus-lg"></i>
              <span>Add Document</span>
            </a>
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-outline-secondary d-flex align-items-center gap-2 px-3 py-2 rounded-3 text-uppercase fw-bold" style="font-size: 0.82rem; letter-spacing: 0.04em; height: 42px;">
                <i class="bi bi-box-arrow-right"></i> Logout
              </button>
            </form>
          </div>
        </div>

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert" style="background: #ecfdf5; color: #065f46; border-radius: 12px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="table-toolbar">
          <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="staff-record-search" class="form-control" placeholder="Search records...">
          </div>
        </div>

        <div class="table-responsive">
          <table class="table table-hover align-middle">
            <thead class="bg-light">
              <tr>
                <th class="border-0">Reference Number</th>
                <th class="border-0">From Office</th>
                <th class="border-0">Type</th>
                <th class="border-0">Subject</th>
                <th class="border-0">Date</th>
                <th class="border-0 text-end">Actions</th>
              </tr>
            </thead>
            <tbody id="staff-records-body">
              @forelse($documents as $document)
                <tr data-record-search="{{ strtolower($document->documentNo . ' ' . ($document->department->depName ?? '') . ' ' . $document->documentType . ' ' . $document->title) }}">
                  <td class="fw-bold">{{ $document->documentNo }}</td>
                  <td>{{ $document->department->depName ?? '-' }}</td>
                  <td><span class="badge bg-light text-dark border">{{ $document->documentType }}</span></td>
                  <td>{{ $document->title }}</td>
                  <td class="text-muted">{{ $document->documentDate ? \Carbon\Carbon::parse($document->documentDate)->format('F d, Y') : $document->created_at?->format('F d, Y') }}</td>
                  <td>
                    <div class="actions-cell justify-content-end">
                      @if($document->filePath)
                      <a href="{{ asset('storage/' . $document->filePath) }}" class="btn-action" target="_blank" title="View PDF">
                        <i class="bi bi-eye"></i>
                      </a>
                      @endif
                      <a href="{{ route('staff.document.edit', $document->documentId) }}" class="btn-action" title="Edit Details">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <a href="{{ route('staff.processDocumentForm', $document->documentId) }}" class="btn-action" title="Process/Route">
                        <i class="bi bi-gear"></i>
                      </a>
                      <form action="{{ route('staff.document.delete', $document->documentId) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this document?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete" title="Delete">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="6" class="text-center py-5 text-muted">
                    <i class="bi bi-folder-x display-4 mb-3 d-block"></i>
                    No records found.
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
  document.getElementById('staff-record-search').addEventListener('input', function () {
    const search = this.value.trim().toLowerCase();
    document.querySelectorAll('#staff-records-body tr[data-record-search]').forEach(function (row) {
      row.hidden = !row.dataset.recordSearch.includes(search);
    });
  });
</script>
@endpush
@endsection
