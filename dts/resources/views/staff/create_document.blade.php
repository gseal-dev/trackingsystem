@extends('layouts.app')
@section('title', 'Add Document')
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
  .suggestion-box {
    position: absolute;
    top: 100%;
    left: 48px;
    right: 0;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    margin-top: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    z-index: 1000;
    overflow: hidden;
  }
  .suggestion-item {
    padding: 8px 16px;
    cursor: pointer;
    transition: background 0.2s;
  }
  .suggestion-item:hover {
    background-color: #f0f0f0;
  }
</style>

<div class="container py-4">
  <div class="mb-3" style="max-width: 520px; margin: 0 auto;">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
  </div>

  <div class="custom-card">
    <div class="text-center mb-4">
      <h2 class="fw-bold text-dark mb-1">Add Document</h2>
      <p class="text-secondary small mb-0">Add new incoming documents to continue</p>
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

    <form method="POST" action="{{ route('staff.document.store') }}" enctype="multipart/form-data">
      @csrf

      <div class="d-flex flex-column gap-3">
        <!-- Reference Number -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-hash"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" id="documentNo" name="documentNo" class="form-control custom-input text-uppercase" placeholder="REFERENCE NUMBER" value="{{ old('documentNo') }}" required>
          </div>
        </div>

        <!-- From Office -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-building"></i>
          </div>
          <div class="flex-grow-1">
            <select id="departmentID" name="departmentID" class="form-control custom-input text-uppercase" required>
              <option value="">SELECT FROM OFFICE</option>
              @foreach($departments ?? [] as $dep)
                <option value="{{ $dep->depID }}" @selected(old('departmentID') == $dep->depID)>{{ strtoupper($dep->depName) }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <!-- Type -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-file-earmark-text"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" id="documentType" name="documentType" class="form-control custom-input text-uppercase" placeholder="TYPE" value="{{ old('documentType') }}" required>
          </div>
        </div>

        <!-- Subject -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-card-heading"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" id="title" name="title" class="form-control custom-input text-uppercase" placeholder="SUBJECT" value="{{ old('title') }}" required>
          </div>
        </div>

        <!-- Date -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-calendar-event"></i>
          </div>
          <div class="flex-grow-1">
            <input type="date" id="documentDate" name="documentDate" class="form-control custom-input" value="{{ old('documentDate', date('Y-m-d')) }}" required>
          </div>
        </div>

        <!-- Upload PDF File -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-paperclip"></i>
          </div>
          <div class="flex-grow-1">
            <input type="file" id="file" name="file" class="form-control custom-input" accept=".pdf" required>
          </div>
        </div>
      </div>

      <div class="text-center mt-4 pt-2">
        <button type="submit" class="btn btn-custom-dark w-100 text-uppercase fw-bold" style="letter-spacing: 0.05em;">
          ADD DOCUMENT
        </button>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('ownerInput');
    const hidden = document.getElementById('ownerID');
    const suggestions = document.getElementById('ownerSuggestions');

    input.addEventListener('input', function() {
        const q = input.value;
        if (q.length < 1) {
            suggestions.style.display = 'none';
            return;
        }
        fetch('{{ route("staff.userSearch") }}?q=' + encodeURIComponent(q))
            .then(res => res.json())
            .then(data => {
                suggestions.innerHTML = '';
                if (data.length > 0) {
                    data.forEach(user => {
                        const div = document.createElement('div');
                        div.className = 'suggestion-item';
                        div.textContent = user.username + ' (ID: ' + user.userID + ')';
                        div.onclick = function() {
                            input.value = user.username;
                            hidden.value = user.userID;
                            suggestions.style.display = 'none';
                        };
                        suggestions.appendChild(div);
                    });
                    suggestions.style.display = 'block';
                } else {
                    suggestions.style.display = 'none';
                }
            });
    });

    document.addEventListener('click', function(e) {
        if (!suggestions.contains(e.target) && e.target !== input) {
            suggestions.style.display = 'none';
        }
    });
});
</script>
@endsection
