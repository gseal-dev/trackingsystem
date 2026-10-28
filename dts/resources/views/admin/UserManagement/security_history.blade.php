@extends('layouts.app')

@section('title', 'Security History')

@push('head')
<style>
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
      <div class="page-card-header"><i class="bi bi-shield-check"></i> Security History</div>

      <div class="section-header d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div class="d-flex flex-column gap-1">
          <h2 class="section-subtitle mb-1">Account Security History</h2>
          <p class="text-muted mb-0 small">Logins, failed logins, locks, password resets and role changes</p>
        </div>
      </div>

      <div class="table-toolbar">
        <form method="GET" action="{{ route('admin.userManagement.securityHistory') }}" class="w-100" id="security-history-filter">
          <!-- Search bar on top, full width -->
          <div class="mb-3">
            <label class="form-label fw-bold text-uppercase small text-secondary mb-1">
              <i class="bi bi-search me-1"></i> Search Security History
            </label>
            <div class="search-box w-100">
              <input type="search" id="security-history-search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Search action, IP, or user..." autocomplete="off">
            </div>
          </div>

          <!-- Rows per page helper bar -->
          <div class="d-flex align-items-center justify-content-end mt-3">
            <div class="d-flex align-items-center gap-2">
              <label for="security-history-per-page" class="small text-muted mb-0">Rows per page</label>
              <select name="per_page" id="security-history-per-page" class="form-select form-select-sm" style="width: 80px; height: 38px;" onchange="this.form.submit()">
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
              <th class="border-0">User</th>
              <th class="border-0">Action</th>
              <th class="border-0">IP Address</th>
              <th class="border-0">Details</th>
              <th class="border-0">Timestamp</th>
            </tr>
          </thead>
          <tbody>
            @forelse($histories as $history)
              <tr>
                <td class="fw-bold">
                  @php
                    $user = $history->user;
                    if ($user) {
                        echo e(trim($user->firstName . ' ' . $user->lastName) ?: $user->username);
                    } else {
                        echo '<span class="badge bg-warning text-dark fw-normal small">(unknown)</span>';
                    }
                  @endphp
                </td>
                <td>{{ e($history->action) }}</td>
                <td class="text-muted small">{{ e($history->ip ?? '-') }}</td>
                <td class="text-break">{{ e($history->details ?? '-') }}</td>
                <td class="text-muted">{{ $history->created_at->timezone(\App\Models\Document::DISPLAY_TIMEZONE)->format('F d, Y \\a\\t h:i A') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="bi bi-shield-x display-4 mb-3 d-block"></i>
                  No security events recorded yet.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
        </div>
      </div>

      @include('partials.list-pager', ['paginator' => $histories, 'noun' => 'events'])
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // Search runs on the server (it has to look through every page, not just the rows on screen)
  (function () {
    const input = document.getElementById('security-history-search');
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
