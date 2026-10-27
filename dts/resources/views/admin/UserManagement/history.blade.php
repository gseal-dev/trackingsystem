@extends('layouts.app')

@section('title', 'Admin History')

@push('head')
<style>
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

  /* Reduce row height in history table */
  table.table-sm tbody tr {
    height: 3rem !important;
  }
  table.table-sm tbody td,
  table.table-sm tbody th {
    padding: 0.2rem 0.5rem !important;
    vertical-align: middle !important;
    line-height: 1.2 !important;
  }
</style>
@endpush

@section('content')
<div class="dashboard-shell">
  <div class="dashboard-container wide">
    <div class="page-card">
      <div class="page-card-header"><i class="bi bi-clock-history"></i> Document History</div>

      <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="d-flex flex-column gap-1">
          <h2 class="section-subtitle mb-1">Document History</h2>
          <p class="text-muted mb-0 small">View document transaction history</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
          <a href="{{ route('admin.userManagement.history.export', request()->only(['q', 'date_from', 'date_to', 'department'])) }}" class="btn btn-outline-success fw-bold px-3 py-2 rounded-2 d-inline-flex align-items-center gap-2" id="export-history-btn" style="border-width: 2px;" title="Exports every history record matching the current filters">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>Export CSV</span>
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
              <a href="{{ route('admin.userManagement.history', array_filter(array_merge(request()->query(), ['q' => null]))) }}" class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 text-decoration-none small">
                <span>{{ e(request('q')) }}</span>
                <i class="bi bi-x-circle fw-bold" style="font-size: 0.65rem;"></i>
              </a>
            @endif
            @if(request('date_from') || request('date_to'))
              <a href="{{ route('admin.userManagement.history', array_filter(array_merge(request()->query(), ['date_from' => null, 'date_to' => null]))) }}" class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 text-decoration-none small">
                <span>{{ request('date_from') ?? 'Any' }} → {{ request('date_to') ?? 'Any' }}</span>
                <i class="bi bi-x-circle fw-bold" style="font-size: 0.65rem;"></i>
              </a>
            @endif
            @if(request('department'))
              @php $deptName = \App\Models\Department::where('depID', request('department'))->value('depName') ?? request('department'); @endphp
              <a href="{{ route('admin.userManagement.history', array_filter(array_merge(request()->query(), ['department' => null]))) }}" class="badge bg-light text-dark border d-inline-flex align-items-center gap-2 text-decoration-none small">
                <span>{{ e($deptName) }}</span>
                <i class="bi bi-x-circle fw-bold" style="font-size: 0.65rem;"></i>
              </a>
            @endif
            <a href="{{ route('admin.userManagement.history') }}" class="btn btn-outline-secondary btn-sm text-decoration-none">Clear all</a>
          </div>
        </div>
      @endif

      <div class="table-toolbar">
        <form method="GET" action="{{ route('admin.userManagement.history') }}" class="w-100" id="admin-history-filter">
          <!-- Search bar on top, full width -->
          <div class="mb-3">
            <label class="form-label fw-bold text-uppercase small text-secondary mb-1">
              <i class="bi bi-search me-1"></i> Search History
            </label>
            <div class="search-box w-100">
              <i class="bi bi-search"></i>
              <input type="search" id="admin-history-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search reference number, subject, office, user..." autocomplete="off">
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
              <a href="{{ route('admin.userManagement.history') }}" class="btn btn-outline-danger w-50 d-inline-flex align-items-center justify-content-center" style="height: 48px; border-radius: 4px;">Reset</a>
            </div>
          </div>

          <!-- Rows per page helper bar -->
          <div class="d-flex align-items-center justify-content-end mt-3">
            <div class="d-flex align-items-center gap-2">
              <label for="admin-history-per-page" class="small text-muted mb-0">Rows per page</label>
              <select name="per_page" id="admin-history-per-page" class="form-select form-select-sm" style="width: 80px; height: 38px;" onchange="this.form.submit()">
                @foreach(\App\Support\DocumentList::PER_PAGE_OPTIONS as $n)
                  <option value="{{ $n }}" {{ $histories->perPage() == $n ? 'selected' : '' }}>{{ $n }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </form>
      </div>

      <div class="border rounded-2 overflow-hidden" style="margin-top: 1rem;">
        <div class="table-responsive">
        <table class="table table-hover table-sm align-middle">
          <thead class="bg-light">
            <tr>
              <th class="border-0">Reference Number</th>
              <th class="border-0">From Office</th>
              <th class="border-0">Subject</th>
              <th class="border-0">Action</th>
              <th class="border-0">User</th>
              <th class="border-0">Timestamp</th>
            </tr>
          </thead>
          <tbody>
            @forelse($histories as $history)
              <tr>
                <td class="fw-bold">{{ $history->document?->documentNo ?? '-' }}</td>
                <td>{{ $history->document?->department?->depName ?? '-' }}</td>
                <td>{{ $history->document?->title ?? '-' }}</td>
                <td>{{ $history->action ?? '-' }}</td>
                <td>
                  @php
                      $user = $history->user;
                      $name = '';
                      if ($user) {
                          $firstName = trim($user->firstName ?? '');
                          $lastName = trim($user->lastName ?? '');
                          if ($firstName || $lastName) {
                              $name = trim($firstName . ' ' . $lastName);
                          }
                          if (!$name) {
                              $name = $user->username ?? 'Unknown';
                          }
                      } else {
                          $name = 'Unknown';
                      }
                      echo e($name);
                  @endphp
                </td>
                <td class="text-muted">{{ $history->created_at->timezone(\App\Models\Document::DISPLAY_TIMEZONE)->format('F d, Y \\a\\t h:i A') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">
                  <i class="bi bi-folder-x display-4 mb-3 d-block"></i>
                  No history records found.
                </td>
              </tr>
            @endforelse
        </tbody>
        </table>
        </div>
      </div>

      @include('partials.list-pager', ['paginator' => $histories, 'noun' => 'records'])
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Search runs on the server (it has to look through every page, not just the 25 rows on screen)
  (function () {
    const input = document.getElementById('admin-history-search');
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