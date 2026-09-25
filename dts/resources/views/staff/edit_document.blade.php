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
    background-color: #000;
    color: #fff;
    border-radius: 50px;
    padding: 0.65rem 2rem;
    font-weight: 600;
    border: none;
    transition: all 0.2s ease;
  }
  .btn-custom-dark:hover {
    background-color: #222;
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

    <form method="POST" action="{{ route('staff.document.update', $document->documentId) }}">
      @csrf

      <div class="d-flex flex-column gap-3">
        <!-- Document No (Read Only) -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-hash"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" class="form-control custom-input bg-light" value="{{ $document->documentNo }}" readonly disabled>
          </div>
        </div>

        <!-- Document Type -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-file-earmark-text"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" id="documentType" name="documentType" class="form-control custom-input" placeholder="document type" value="{{ old('documentType', $document->documentType) }}" required>
          </div>
        </div>

        <!-- Title -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-card-heading"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" id="title" name="title" class="form-control custom-input" placeholder="title" value="{{ old('title', $document->title) }}" required>
          </div>
        </div>

        <!-- Description -->
        <div class="d-flex align-items-start gap-3 mt-1">
          <div class="field-icon pt-2">
            <i class="bi bi-card-text"></i>
          </div>
          <div class="flex-grow-1">
            <textarea id="description" name="description" class="form-control custom-textarea" rows="3" placeholder="description">{{ old('description', $document->description) }}</textarea>
          </div>
        </div>
      </div>

      <div class="text-center mt-4 pt-2">
        <button type="submit" class="btn btn-custom-dark w-100">
          Update Document
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
