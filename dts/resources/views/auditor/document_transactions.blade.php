@extends('layouts.app')

@section('title', 'Document Transactions')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Document Transactions</h1>
        <div class="text-muted">View all document transactions except pending ones</div>
      </div>
      <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left"></i> Back to Dashboard</a>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
      <form method="GET" action="{{ route('auditor.documentTransactions') }}" class="d-flex gap-2 flex-wrap align-items-center">
        <input type="text" name="search" class="form-control" placeholder="Search by title or owner" value="{{ request('search') }}" style="max-width: 220px;">
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
          <a href="{{ route('auditor.documentTransactions') }}" class="btn btn-outline-secondary">Reset</a>
        @endif
      </form>
      <a href="{{ route('auditor.documentTransactions.export', request()->all()) }}" class="btn btn-success">
        <i class="bi bi-file-earmark-arrow-down"></i> Export to CSV
      </a>
    </div>

    <div class="card-surface p-3">
  

      @if($documents->isEmpty())
        <div class="alert alert-info mb-0">No document transactions found.</div>
      @else
        <div class="table-responsive">
          <table class="table table-clean table-sm align-middle mb-0">
            <thead>
              <tr>
                <th>Document No</th>
                <th>Title</th>
                <th>Owner</th>
                <th>Status</th>
                <th>Department</th>
                <th>Last Updated</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody id="auditor-transactions-body" data-live-refresh>
            @foreach($documents as $doc)
              <tr>
                <td>{{ $doc->documentNo }}</td>
                <td>{{ $doc->title }}</td>
                <td>{{ $doc->owner->username ?? 'Unknown' }}</td>
                <td>{{ $doc->status->statusName ?? 'Unknown' }}</td>
                <td>{{ $doc->department->depName ?? 'Unknown' }}</td>
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

        <div class="mt-3">
          {{ $documents->links('pagination::bootstrap-5') }}
        </div>
      @endif
    </div>
  </div>
</div>
@endsection