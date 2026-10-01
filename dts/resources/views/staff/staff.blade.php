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
            <h1 class="section-title">Documents List</h1>
            <p class="text-muted">Manage and track all registered documents</p>
          </div>
          <div class="d-flex gap-2 align-items-center flex-wrap">
            <button type="button" class="btn-create border-0 cursor-pointer" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
              <i class="bi bi-plus-lg"></i>
              <span>Add Document</span>
            </button>
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-dark px-4 py-2" style="background-color: #000000; color: #ffffff; border-radius: 50px; font-weight: 600;">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
          </div>
        </div>

        @if(session('undo_delete_id'))
          <div class="alert alert-info alert-dismissible fade show border-0 mb-4 d-flex justify-content-between align-items-center" role="alert" style="background: #eff6ff; color: #1e40af; border-radius: 12px;">
            <span>Document "{{ session('undo_delete_title') }}" deleted.</span>
            <form action="{{ route('staff.document.undo', session('undo_delete_id')) }}" method="POST" class="m-0">
              @csrf
              <button type="submit" class="btn btn-sm btn-dark fw-bold px-3 py-1" style="border-radius: 50px;">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Undo / Retrieve
              </button>
            </form>
          </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show border-0 mb-4" role="alert" style="background: #ecfdf5; color: #065f46; border-radius: 12px;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <div class="table-toolbar">
          <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2 flex-wrap align-items-center w-100">
            <div class="search-box flex-grow-1" style="max-width: 250px;">
              <i class="bi bi-search"></i>
              <input type="text" id="staff-record-search" class="form-control" placeholder="Search records...">
            </div>
            <select name="year" class="form-select" style="width: 120px;" onchange="this.form.submit()">
              <option value="">All Years</option>
              @for($y = 2017; $y <= 2026; $y++)
                <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
              @endfor
            </select>
            <select name="month" class="form-select" style="width: 140px;" onchange="this.form.submit()">
              <option value="">All Months</option>
              @for($m = 1; $m <= 12; $m++)
                <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
              @endfor
            </select>
            <select name="day" class="form-select" style="width: 110px;" onchange="this.form.submit()">
              <option value="">All Days</option>
              @for($d = 1; $d <= 31; $d++)
                <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>Day {{ $d }}</option>
              @endfor
            </select>
            @if(request('year') || request('month') || request('day'))
              <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-3 py-2" style="border-radius: 50px;">Reset</a>
            @endif
          </form>
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
                  <td style="text-transform: uppercase;">{{ $document->documentType }}</td>
                  <td>{{ $document->title }}</td>
                  <td class="text-muted">{{ $document->documentDate ? \Carbon\Carbon::parse($document->documentDate)->format('F d, Y') : $document->created_at?->format('F d, Y') }}</td>
                  <td>
                    <div class="actions-cell justify-content-end">
                      @if($document->filePath)
                      <a href="{{ asset('storage/' . $document->filePath) }}" class="btn-action" target="_blank" title="View PDF">
                        <i class="bi bi-eye"></i>
                      </a>
                      @endif
                      <button type="button" class="btn-action border-0 bg-transparent" data-bs-toggle="modal" data-bs-target="#editDocumentModal-{{ $document->documentId }}" title="Edit Details">
                        <i class="bi bi-pencil"></i>
                      </button>
                      <form action="{{ route('staff.document.delete', $document->documentId) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this file?')">
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
@if($errors->any())
<script>
  document.addEventListener('DOMContentLoaded', function() {
    var myModal = new bootstrap.Modal(document.getElementById('addDocumentModal'));
    myModal.show();
  });
</script>
@endif
@endpush

<!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 p-3" style="background-color: #f5f5f5;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="addDocumentModalLabel">Add Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('staff.document.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          @if($errors->any())
            <div class="alert alert-danger rounded-4">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="d-flex flex-column gap-3">
            <!-- Reference Number -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-hash"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" id="documentNo" name="documentNo" class="form-control rounded-pill text-uppercase" placeholder="REFERENCE NUMBER" value="{{ old('documentNo') }}" required>
              </div>
            </div>

            <!-- From Office -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-building"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" id="fromOffice" name="fromOffice" class="form-control rounded-pill text-uppercase" placeholder="FROM OFFICE" value="{{ old('fromOffice') }}" required>
              </div>
            </div>

            <!-- Type -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" id="documentType" name="documentType" class="form-control rounded-pill text-uppercase" placeholder="TYPE" value="{{ old('documentType') }}" required>
              </div>
            </div>

            <!-- Subject -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-card-heading"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" id="title" name="title" class="form-control rounded-pill text-uppercase" placeholder="SUBJECT" value="{{ old('title') }}" required>
              </div>
            </div>

            <!-- Date -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-calendar-event"></i>
              </div>
              <div class="flex-grow-1">
                <input type="date" id="documentDate" name="documentDate" class="form-control rounded-pill" value="{{ old('documentDate', date('Y-m-d')) }}" required>
              </div>
            </div>

            <!-- Upload PDF File -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-paperclip"></i>
              </div>
              <div class="flex-grow-1">
                <input type="file" id="file" name="file" class="form-control rounded-pill" accept=".pdf" required>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark rounded-pill px-4 text-uppercase fw-bold" style="background-color: #000; color: #fff;">Add Document</button>
        </div>
      </form>
    </div>
  </div>
</div>

@foreach($documents as $document)
<!-- Edit Document Modal -->
<div class="modal fade" id="editDocumentModal-{{ $document->documentId }}" tabindex="-1" aria-labelledby="editDocumentModalLabel-{{ $document->documentId }}" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 p-3" style="background-color: #f5f5f5;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="editDocumentModalLabel-{{ $document->documentId }}">Edit Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('staff.document.update', $document->documentId) }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          <div class="d-flex flex-column gap-3">
            <!-- Reference Number -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-hash"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" name="documentNo" class="form-control rounded-pill text-uppercase" placeholder="REFERENCE NUMBER" value="{{ $document->documentNo }}" required>
              </div>
            </div>

            <!-- From Office -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-building"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" name="fromOffice" class="form-control rounded-pill text-uppercase" placeholder="FROM OFFICE" value="{{ $document->department->depName ?? '' }}" required>
              </div>
            </div>

            <!-- Type -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" name="documentType" class="form-control rounded-pill text-uppercase" placeholder="TYPE" value="{{ $document->documentType }}" required>
              </div>
            </div>

            <!-- Subject -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-card-heading"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" name="title" class="form-control rounded-pill text-uppercase" placeholder="SUBJECT" value="{{ $document->title }}" required>
              </div>
            </div>

            <!-- Date -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-calendar-event"></i>
              </div>
              <div class="flex-grow-1">
                <input type="date" name="documentDate" class="form-control rounded-pill" value="{{ $document->documentDate ?? date('Y-m-d') }}" required>
              </div>
            </div>

            <!-- Upload PDF File -->
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-paperclip"></i>
              </div>
              <div class="flex-grow-1">
                <input type="file" name="file" class="form-control rounded-pill" accept=".pdf">
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark rounded-pill px-4 text-uppercase fw-bold" style="background-color: #000; color: #fff;">Update Document</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endforeach
@endsection
