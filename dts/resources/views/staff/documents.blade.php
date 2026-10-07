@extends('layouts.app')

@section('title', 'Staff Documents List')

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

  .staff-main-content {
    background: transparent;
    border: none;
    border-radius: 0;
    box-shadow: none;
    padding: 0;
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
    max-width: 400px;
    width: 100%;
  }

  .search-box input, .form-select {
    border-radius: 50px !important;
    border-color: #cbd5e1;
  }

  .search-box input {
    padding-left: 2.75rem;
    height: 48px;
  }

  .form-select {
    height: 48px;
    padding-left: 1.25rem;
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

  .btn-action.btn-delete:hover {
    background: #fee2e2;
    border-color: #fecaca;
    color: #dc2626;
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
    padding: 1.15rem 1rem !important;
    border-bottom: 2px solid #e5e7eb !important;
  }

  .table td {
    padding: 1.15rem 1rem !important;
  }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
      <main class="staff-main-content" style="background: transparent; border: none; border-radius: 0; padding: 0; min-height: 600px; width: 100%;">
        <div class="section-header">
          <div>
            <h1 class="section-title">Documents List</h1>
            <p class="text-muted">Manage and track all registered documents</p>
          </div>
          <div class="d-flex gap-2 align-items-center flex-wrap">
            <button type="button" class="btn btn-outline-success fw-bold px-3 py-2 rounded-pill d-inline-flex align-items-center gap-2" id="export-documents-btn" style="border-width: 2px;">
              <i class="bi bi-file-earmark-spreadsheet"></i>
              <span>Export Excel</span>
            </button>
            <button type="button" class="btn-create border-0 cursor-pointer" data-bs-toggle="modal" data-bs-target="#addDocumentModal">
              <i class="bi bi-plus-lg"></i>
              <span>Add Document</span>
            </button>
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
          <form method="GET" action="{{ route('staff.documents') }}" class="w-100">
            <div class="search-box w-100 mb-3" style="max-width: 100%;">
              <i class="bi bi-search"></i>
              <input type="text" id="staff-record-search" class="form-control" placeholder="Search by reference number, subject, etc..." style="max-width: 100%;">
            </div>
            <div class="d-flex gap-2 flex-wrap align-items-center">
              <select name="year" class="form-select" style="width: 160px;" onchange="this.form.submit()">
                <option value="">Year: All</option>
                @for($y = 2017; $y <= 2026; $y++)
                  <option value="{{ $y }}" {{ request('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
              </select>
              <select name="month" class="form-select" style="width: 160px;" onchange="this.form.submit()">
                <option value="">Month: All</option>
                @for($m = 1; $m <= 12; $m++)
                  <option value="{{ $m }}" {{ request('month') == $m ? 'selected' : '' }}>{{ date('F', mktime(0, 0, 0, $m, 1)) }}</option>
                @endfor
              </select>
              <select name="day" class="form-select" style="width: 140px;" onchange="this.form.submit()">
                <option value="">Day: All</option>
                @for($d = 1; $d <= 31; $d++)
                  <option value="{{ $d }}" {{ request('day') == $d ? 'selected' : '' }}>Day {{ $d }}</option>
                @endfor
              </select>
              @if(request('year') || request('month') || request('day'))
                <a href="{{ route('staff.documents') }}" class="btn btn-outline-secondary px-4 py-2" style="border-radius: 50px;">Reset</a>
              @endif
            </div>
          </form>
        </div>

        <p class="text-muted small fst-italic mb-3">*Project information, documents, and records are continuously being tracked. Thank you for your patience.*</p>

        <div class="mb-3">
          <div class="d-inline-flex align-items-center px-4 py-2 text-white fw-bold shadow-sm" style="background-color: #ea3a14; border-radius: 50px; font-size: 0.9rem;">
            <i class="bi bi-file-earmark-text-fill me-2"></i> {{ count($documents) }} Documents Found
          </div>
        </div>

        <div class="card border rounded-4 bg-white shadow-sm overflow-hidden p-3">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-light">
                <tr>
                  <th>Reference Number</th>
                  <th>From Office</th>
                  <th>Type</th>
                  <th>Subject</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th class="text-end">Actions</th>
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
                  <td><span class="badge bg-light text-dark border">{{ $document->status->statusName ?? 'Pending' }}</span></td>
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
                      <form action="{{ route('staff.document.delete', $document->documentId) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this file?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action btn-delete border-0 bg-transparent text-danger" title="Delete">
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

  document.getElementById('export-documents-btn').addEventListener('click', function () {
    const rows = [
      ['Reference Number', 'From Office', 'Type', 'Subject', 'Date']
    ];
    document.querySelectorAll('#staff-records-body tr[data-record-search]').forEach(function (row) {
      if (!row.hidden) {
        const cols = row.querySelectorAll('td');
        if (cols.length >= 5) {
          rows.push([
            cols[0].innerText.trim(),
            cols[1].innerText.trim(),
            cols[2].innerText.trim(),
            cols[3].innerText.trim(),
            cols[4].innerText.trim()
          ]);
        }
      }
    });
    const csv = rows.map(function (row) { 
      return row.map(function (cell) { return '"' + cell.replace(/"/g, '""') + '"'; }).join(','); 
    }).join('\n');
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'documents_export.csv';
    link.click();
  });
</script>

<!-- Add Document Modal -->
<div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel" aria-hidden="true" data-bs-backdrop="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 p-3 shadow-lg" style="background-color: #ffffff !important; color: #1e293b !important;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="addDocumentModalLabel">Add Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" action="{{ route('staff.document.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="modal-body">
          @if($errors->any())
            <div class="alert alert-danger rounded-4 mb-3">
              <ul class="mb-0">
                @foreach($errors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          @endif

          <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-hash"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" id="documentNo" name="documentNo" class="form-control rounded-pill text-uppercase bg-white" placeholder="REFERENCE NUMBER" value="{{ old('documentNo') }}" required>
              </div>
            </div>

            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-building"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" id="fromOffice" name="fromOffice" class="form-control rounded-pill text-uppercase bg-white" placeholder="FROM OFFICE" value="{{ old('fromOffice') }}" required>
              </div>
            </div>

            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" id="documentType" name="documentType" class="form-control rounded-pill text-uppercase bg-white" placeholder="TYPE" value="{{ old('documentType') }}" required>
              </div>
            </div>

            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-card-heading"></i>
              </div>
              <div class="flex-grow-1">
                <input type="text" id="title" name="title" class="form-control rounded-pill text-uppercase bg-white" placeholder="SUBJECT" value="{{ old('title') }}" required>
              </div>
            </div>

            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-calendar-event"></i>
              </div>
              <div class="flex-grow-1">
                <input type="date" id="documentDate" name="documentDate" class="form-control rounded-pill bg-white" value="{{ old('documentDate', date('Y-m-d')) }}" required>
              </div>
            </div>

            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-tag"></i>
              </div>
              <div class="flex-grow-1">
                <select name="currentStatus" id="currentStatus" class="form-select rounded-pill bg-white" required>
                  @foreach(\App\Models\DocumentStatus::all() as $status)
                    <option value="{{ $status->statusID }}" {{ old('currentStatus', 1) == $status->statusID ? 'selected' : '' }}>{{ $status->statusName }}</option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="d-flex align-items-center gap-3">
              <div class="field-icon" style="font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;">
                <i class="bi bi-paperclip"></i>
              </div>
              <div class="flex-grow-1">
                <input type="file" id="file" name="file" class="form-control rounded-pill bg-white" accept=".pdf" required>
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

@if($errors->any())
<script>
  document.addEventListener('DOMContentLoaded', function() {
    const modalEl = document.getElementById('addDocumentModal');
    if (modalEl) {
      const myModal = new bootstrap.Modal(modalEl);
      myModal.show();
    }
  });
</script>
@endif
@endsection
