@extends('layouts.app')

@section('title', 'Staff History')

@push('head')
<style>


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


  .section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    /* margin-bottom: 2rem; */
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
      <div class="page-card">
        <div class="page-card-header"><i class="bi bi-clock-history"></i> Document History</div>
        <div class="section-header">
          <div>
            <p class="text-muted">View document transaction history</p>
          </div>
        </div>

        <div class="table-toolbar">
          <form method="GET" action="{{ route('staff.history') }}" class="d-flex gap-2 flex-wrap align-items-center w-100">
            <div class="search-box flex-grow-1" style="max-width: 250px;">
              <i class="bi bi-search"></i>
              <input type="text" name="search" class="form-control" placeholder="Search history..." value="{{ request('search') }}">
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
            @if(request('search') || request('year') || request('month') || request('day'))
              <a href="{{ route('staff.history') }}" class="btn btn-outline-secondary px-3 py-2" style="border-radius: 50px;">Reset</a>
            @endif
            @include('partials.per-page-select', ['paginator' => $histories, 'id' => 'history-per-page'])

            <!-- Export button -->
            <a href="{{ route('staff.history.export', request()->all()) }}" class="btn btn-outline-primary">
              <i class="bi bi-download"></i> Export CSV
            </a>
          </form>
        </div>

        <div class="border rounded-2 overflow-hidden">
          <div class="table-responsive">
          <table class="table table-hover table-sm align-middle">
            <thead class="bg-light">
              <tr>
                <th class="border-0">Reference Number</th>
                <th class="border-0">From Office</th>
                <th class="border-0">Type</th>
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
                  <td><span class="badge bg-light text-dark border">{{ $history->document?->documentType ?? '-' }}</span></td>
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
