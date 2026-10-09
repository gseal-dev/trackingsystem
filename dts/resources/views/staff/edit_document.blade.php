@extends('layouts.app')
@section('title', 'Edit Document')
@section('content')
<style>
  .custom-card {
    background-color: #f5f5f5;
    border-radius: 24px;
    padding: 2.5rem 2rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
    max-width: 520px;
    margin: 0 auto;
  }
  .custom-input {
    border-radius: 50px !important;
    padding: 0.65rem 1.25rem;
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    font-size: 0.95rem;
  }
  .custom-input:focus {
    border-color: #000;
    box-shadow: none;
  }
  .custom-textarea {
    border-radius: 18px !important;
    padding: 0.75rem 1.25rem;
    border: 1px solid #e2e8f0;
    background-color: #ffffff;
    font-size: 0.95rem;
  }
  .custom-textarea:focus {
    border-color: #000;
    box-shadow: none;
  }
  .field-icon {
    font-size: 1.4rem;
    color: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
  }
  .btn-custom-dark {
    background-color: #001253;
    color: #fff;
    border-radius: 50px;
    padding: 0.65rem 2rem;
    font-weight: 600;
    border: none;
    transition: all 0.2s ease;
  }
  .btn-custom-dark:hover {
    background-color: #000a33;
    color: #fff;
  }
</style>

<div class="container py-4">
  <div class="mb-3 text-center" style="max-width: 520px; margin: 0 auto;">
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted small">
      <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
  </div>

  <div class="custom-card">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-dark mb-1">Edit Document</h2>
      <p class="text-secondary small mb-0">Update document details</p>
    </div>

    @if($errors->any())
        <div class="alert alert-danger rounded-4">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('staff.document.update', $document->documentId) }}" enctype="multipart/form-data">
      @csrf

      <div class="d-flex flex-column gap-3">
        <!-- Reference Number -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon"><i class="bi bi-hash"></i></div>
          <div class="flex-grow-1">
            <input type="text" id="documentNo" name="documentNo" class="form-control custom-input" placeholder="reference number" value="{{ old('documentNo', $document->documentNo) }}" required>
          </div>
        </div>

        <!-- From Office -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon"><i class="bi bi-building"></i></div>
          <div class="flex-grow-1">
            <input type="text" id="fromOffice" name="fromOffice" class="form-control custom-input" placeholder="from office" value="{{ old('fromOffice', $document->department->depName ?? '') }}" required>
          </div>
        </div>

        <!-- Document Type -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon"><i class="bi bi-file-earmark-text"></i></div>
          <div class="flex-grow-1">
            <input type="text" id="documentType" name="documentType" class="form-control custom-input" placeholder="document type" value="{{ old('documentType', $document->documentType) }}" required>
          </div>
        </div>

        <!-- Subject -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon"><i class="bi bi-card-heading"></i></div>
          <div class="flex-grow-1">
            <input type="text" id="title" name="title" class="form-control custom-input" placeholder="subject" value="{{ old('title', $document->title) }}" required>
          </div>
        </div>

        <!-- Date -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon"><i class="bi bi-calendar-event"></i></div>
          <div class="flex-grow-1">
            <input type="date" id="documentDate" name="documentDate" class="form-control custom-input" value="{{ old('documentDate', $document->documentDate ? \Carbon\Carbon::parse($document->documentDate)->format('Y-m-d') : $document->created_at?->format('Y-m-d')) }}">
          </div>
        </div>

        <!-- Replace PDF (optional) -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon"><i class="bi bi-paperclip"></i></div>
          <div class="flex-grow-1">
            <input type="file" id="file" name="file" class="form-control custom-input" accept=".pdf">
          </div>
        </div>
      </div>

      @if($document->filePath)
        <p class="small text-muted mt-3 mb-0 text-center">
          Current file: <a href="{{ asset('storage/' . $document->filePath) }}" target="_blank">view PDF</a>
          &middot; choose a new PDF above only to replace it.
        </p>
      @endif

      <div class="text-center mt-4 pt-2">
        <button type="submit" class="btn btn-custom-dark w-100">
          Update Document
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
