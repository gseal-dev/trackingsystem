@extends('layouts.app')

@section('title', 'Send Documents')

@section('content')
<div class="page-container">
  <div class="container" style="max-width: 1100px;">
    <div class="page-header mb-3 d-flex align-items-end justify-content-between flex-wrap gap-3">
      <div>
        <h1 class="h3 mb-1">Send Documents</h1>
        <div class="text-muted">Route pending documents to departments</div>
      </div>
      <form method="POST" action="{{ route('logout') }}" class="ms-auto">
        @csrf
        <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-right"></i> Logout</button>
      </form>
    </div>

    @if(session('success'))
      <div class="alert alert-success card-surface border-0">{{ session('success') }}</div>
    @endif

    <div class="card-surface p-3">
      @if($documents->isEmpty())
        <div class="alert alert-info mb-0">No documents available to send.</div>
      @else
        <div class="table-responsive">
          <table class="table table-clean align-middle mb-0">
            <thead>
              <tr>
                <th>Document No</th>
                <th>Title</th>
                <th>Document Owner</th>
                <th class="text-end">Action</th>
              </tr>
            </thead>
            <tbody>
            @foreach($documents as $doc)
              <tr>
                <td>{{ $doc->documentNo }}</td>
                <td>{{ $doc->title }}</td>
                <td>{{ $doc->owner->username ?? 'Unknown' }} (ID: {{ $doc->ownerID }})</td>
                <td class="text-end">
                  <a href="{{ route('admin.sendDocumentForm', $doc->documentId) }}" class="btn btn-brand btn-sm">
                    <i class="bi bi-send"></i> Send
                  </a>
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
