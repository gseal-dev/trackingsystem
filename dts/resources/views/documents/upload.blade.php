@extends('layouts.app')
@section('title', 'Upload New Document')

@push('head')
<style>
  .upload-wrap { width: 100%; max-width: var(--page-max); margin: 0 auto; padding: 0; }
  .upload-card { background: #ffffff; border-radius: 6px; overflow: hidden; box-shadow: 0 6px 24px rgba(15, 23, 42, 0.12); color: #1f2937; }

  .upload-header { display: flex; align-items: center; gap: 0.6rem; padding: 0.95rem 1.5rem; color: #ffffff; font-weight: 700; font-size: 0.95rem; background: linear-gradient(90deg, rgb(0, 18, 83)0%,rgb(0, 18, 83) 100%); }
  .upload-body { padding: 0.5rem 1.75rem 1.75rem; }

  .up-section { display: flex; align-items: center; gap: 0.5rem; margin: 1.6rem 0 1.1rem; padding-bottom: 0.55rem; border-bottom: 1px solid #f3dcda; color: rgb(0, 18, 83); font-size: 0.8rem; font-weight: 800; letter-spacing: 0.02em; text-transform: uppercase; }

  .up-label { display: block; margin-bottom: 0.3rem; color: #1f2937; font-size: 0.68rem; font-weight: 700; letter-spacing: 0.02em; text-transform: uppercase; }
  .up-label .req { color: rgb(0, 18, 83)); }
  .up-input { width: 100%; padding: 0.5rem 0.75rem; font-size: 0.85rem; color: #1f2937; background: #ffffff; border: 1px solid #d5dbe2; border-radius: 3px; box-shadow: none; text-transform: uppercase; }
  .up-input:focus { border-color: rgb(0, 18, 83); box-shadow: 0 0 0 3px rgba(232, 57, 45, 0.12); outline: 0; }
  .up-input[readonly] { background: #f3f4f6; color: #374151; cursor: default; }
  .up-input.is-invalid { border-color: rgb(0, 18, 83); background-image: none; }
  .up-help { margin-top: 0.25rem; color: #6b7280; font-size: 0.68rem; }
  .up-error { margin-top: 0.25rem; color: rgb(0, 18, 83); font-size: 0.75rem; font-weight: 600; }

  /* drag & drop zone: the real file input sits invisibly on top, so click and drop both work */
  .up-drop { position: relative; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 150px; padding: 1.75rem 1rem; text-align: center; background: #fff5f4; border: 1.5px dashed #ee7068; border-radius: 6px; transition: background 0.15s, border-color 0.15s; }
  .up-drop:hover, .up-drop.is-dragover { background: #ffeae8; border-color:rgb(0, 18, 83); }
  .up-drop.is-invalid { border-color: rgb(0, 18, 83); }
  .up-drop input[type="file"] { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; }
  .up-drop-icon { font-size: 2.8rem; line-height: 1; color: rgb(0, 18, 83); }
  .up-drop-text { margin-top: 0.5rem; font-size: 0.85rem; color: #374151; }
  .up-drop-sub { margin-top: 0.2rem; font-size: 0.75rem; color: #6b7280; }
  .up-file-name { margin-top: 0.4rem; font-size: 0.95rem; font-weight: 700; color: #1f2937; word-break: break-all; }

  .up-actions { display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.75rem; }
  .up-btn { display: inline-flex; align-items: center; gap: 0.45rem; padding: 0.6rem 1.3rem; font-size: 0.85rem; font-weight: 700; border-radius: 4px; border: 1px solid transparent; text-decoration: none; cursor: pointer; }
  .up-btn-submit { color: #ffffff; background: rgb(0, 18, 83); }
  .up-btn-submit:hover { background: rgb(0, 18, 83); color: #ffffff; }
  .up-btn-submit:disabled { opacity: 0.7; cursor: wait; }
  .up-btn-cancel { color: #4b5563; background: #ffffff; border-color: #d1d5db; }
  .up-btn-cancel:hover { background: #f3f4f6; color: #1f2937; }

  @media (max-width: 575.98px) { .upload-body { padding: 0.25rem 1rem 1.25rem; } .up-actions { flex-direction: column-reverse; } .up-btn { justify-content: center; } }
</style>
@endpush

@section('content')
<div class="upload-wrap">
  <div class="upload-card">
    <div class="upload-header">
      <i class="bi bi-cloud-arrow-up-fill"></i> Upload New Document
    </div>

    <form method="POST" action="{{ route('staff.document.store') }}" enctype="multipart/form-data" id="uploadForm">
      @csrf
      <div class="upload-body">

        <div class="up-section"><i class="bi bi-file-earmark-text-fill"></i> Document Information</div>

        <div class="row g-3">


          <div class="col-md-6">
            <label class="up-label" for="documentNo">Reference Number <span class="req">*</span></label>
            <input type="text" id="documentNo" name="documentNo" autocomplete="off"
                   class="up-input @error('documentNo') is-invalid @enderror" placeholder="e.g. OO-2026-001"
                   value="{{ old('documentNo') }}" required>
            @error('documentNo') <div class="up-error">{{ $message }}</div> @else <div class="up-help">Must be unique for each document.</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="up-label" for="fromOffice">From Office <span class="req">*</span></label>
            <input type="text" id="fromOffice" name="fromOffice" list="officeList" autocomplete="off"
                   class="up-input @error('fromOffice') is-invalid @enderror" placeholder="Select or type the originating office"
                   value="{{ old('fromOffice') }}" required>
            <datalist id="officeList">
              @foreach($offices as $office)
                <option value="{{ $office }}"></option>
              @endforeach
            </datalist>
            @error('fromOffice') <div class="up-error">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="up-label" for="documentDate">Document Date <span class="req">*</span></label>
            <input type="date" id="documentDate" name="documentDate"
                   class="up-input @error('documentDate') is-invalid @enderror"
                   value="{{ old('documentDate', now()->timezone(\App\Models\Document::DISPLAY_TIMEZONE)->format('Y-m-d')) }}" required>
            @error('documentDate') <div class="up-error">{{ $message }}</div> @else <div class="up-help">The original date this document was created.</div> @enderror
          </div>

          <div class="col-12">
            <label class="up-label" for="title">Subject <span class="req">*</span></label>
            <input type="text" id="title" name="title" maxlength="255" autocomplete="off"
                   class="up-input @error('title') is-invalid @enderror" placeholder="Provide a brief subject of the document..."
                   value="{{ old('title') }}" required>
            @error('title') <div class="up-error">{{ $message }}</div> @enderror
          </div>
        </div>

        <div class="up-section"><i class="bi bi-upload"></i> File Upload</div>

        <div class="up-drop @error('file') is-invalid @enderror" id="dropZone">
          <input type="file" id="file" name="file" accept=".pdf,application/pdf" required>
          <div id="dropEmpty">
            <div class="up-drop-icon"><i class="bi bi-cloud-arrow-up-fill"></i></div>
            <div class="up-drop-text"><strong>Click to browse</strong> or drag file here</div>
            <div class="up-drop-sub">PDF only (Max 10MB)</div>
          </div>
          <div id="dropChosen" class="d-none">
            <div class="up-drop-icon"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div class="up-file-name" id="chosenName"></div>
            <div class="up-drop-sub"><span id="chosenSize"></span> &middot; click or drop another file to replace it</div>
          </div>
        </div>
        <div class="up-error {{ $errors->has('file') ? '' : 'd-none' }}" id="fileError">{{ $errors->first('file') }}</div>

        <div class="up-section"><i class="bi bi-info-circle-fill"></i> Document Metadata</div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="up-label" for="uploadDate">Upload Date</label>
            <input type="text" id="uploadDate" class="up-input" value="{{ $uploadDate }}" readonly tabindex="-1">
          </div>
          <div class="col-md-6">
            <label class="up-label" for="uploadedBy">Uploaded By</label>
            <input type="text" id="uploadedBy" class="up-input" value="{{ $uploadedBy }}" readonly tabindex="-1">
          </div>
        </div>

        <div class="up-actions">
          <a href="{{ route($backRoute) }}" class="up-btn up-btn-cancel">Cancel</a>
          <button type="submit" class="up-btn up-btn-submit" id="submitBtn"><i class="bi bi-cloud-arrow-up-fill"></i> Upload Document</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    const MAX_BYTES = 10 * 1024 * 1024;
    const zone = document.getElementById('dropZone');
    const input = document.getElementById('file');
    const empty = document.getElementById('dropEmpty');
    const chosen = document.getElementById('dropChosen');
    const errorBox = document.getElementById('fileError');

    function formatSize(bytes) {
      return bytes >= 1048576 ? (bytes / 1048576).toFixed(1) + ' MB' : Math.max(1, Math.round(bytes / 1024)) + ' KB';
    }
    function showError(message) {
      errorBox.textContent = message;
      errorBox.classList.toggle('d-none', !message);
      zone.classList.toggle('is-invalid', !!message);
    }
    function reset() {
      input.value = '';
      empty.classList.remove('d-none');
      chosen.classList.add('d-none');
    }

    input.addEventListener('change', function () {
      const file = input.files[0];
      if (!file) { reset(); return; }

      const isPdf = file.type === 'application/pdf' || /\.pdf$/i.test(file.name);
      if (!isPdf) { reset(); showError('Only PDF files can be uploaded.'); return; }
      if (file.size > MAX_BYTES) { reset(); showError('That file is too large. The maximum size is 10MB.'); return; }

      showError('');
      document.getElementById('chosenName').textContent = file.name;
      document.getElementById('chosenSize').textContent = formatSize(file.size);
      empty.classList.add('d-none');
      chosen.classList.remove('d-none');
    });

    ['dragenter', 'dragover'].forEach(function (name) {
      zone.addEventListener(name, function () { zone.classList.add('is-dragover'); });
    });
    ['dragleave', 'drop'].forEach(function (name) {
      zone.addEventListener(name, function () { zone.classList.remove('is-dragover'); });
    });

    // Stop double submits while a big file is uploading
    document.getElementById('uploadForm').addEventListener('submit', function () {
      const btn = document.getElementById('submitBtn');
      btn.disabled = true;
      btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Uploading...';
    });

    // Auto-capslock text inputs
    ['documentNo', 'fromOffice', 'title'].forEach(function (id) {
      const el = document.getElementById(id);
      if (el) {
        el.addEventListener('input', function () {
          const start = el.selectionStart;
          const end = el.selectionEnd;
          el.value = el.value.toUpperCase();
          el.setSelectionRange(start, end);
        });
      }
    });
  })();
</script>
@endpush
@endsection
