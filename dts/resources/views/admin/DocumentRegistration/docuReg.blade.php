@extends('layouts.app')
@section('title', 'Document Registration')
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
  <!-- Back Button Link -->
  <div class="mb-3 text-center" style="max-width: 520px; margin: 0 auto;">
    <a href="{{ route('dashboard') }}" class="text-decoration-none text-muted small">
      <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
    </a>
  </div>

  <div class="custom-card">
    <!-- Header Section -->
    <div class="text-center mb-4">
      <h2 class="fw-bold text-dark mb-1">Document Registration</h2>
      <p class="text-secondary small mb-0">Register new incoming documents to continue</p>
    </div>

    <form method="POST" action="{{ route('admin.documentRegistration.submit') }}" enctype="multipart/form-data">
      @csrf

      <div class="d-flex flex-column gap-3">
        <!-- Document No -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-hash"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" id="documentNo" name="documentNo" class="form-control custom-input" placeholder="document number" required>
          </div>
        </div>

        <!-- Document Type -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-file-earmark-text"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" id="documentType" name="documentType" class="form-control custom-input" placeholder="document type" required>
          </div>
        </div>

        <!-- Title -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-card-heading"></i>
          </div>
          <div class="flex-grow-1">
            <input type="text" id="title" name="title" class="form-control custom-input" placeholder="title" required>
          </div>
        </div>

        <!-- Owner Username -->
        <div class="d-flex align-items-center gap-3 position-relative">
          <div class="field-icon">
            <i class="bi bi-person"></i>
          </div>
          <div class="flex-grow-1 position-relative">
            <input type="text" id="ownerInput" name="ownerInput" class="form-control custom-input" placeholder="owner (username)" autocomplete="off" required>
            <input type="hidden" id="ownerID" name="ownerID" required>
            <div id="ownerSuggestions" class="suggestion-box" style="display:none;"></div>
          </div>
        </div>

        <!-- Description -->
        <div class="d-flex align-items-start gap-3 mt-1">
          <div class="field-icon pt-2">
            <i class="bi bi-card-text"></i>
          </div>
          <div class="flex-grow-1">
            <textarea id="description" name="description" class="form-control custom-textarea" rows="3" placeholder="description"></textarea>
          </div>
        </div>

        <!-- Upload File -->
        <div class="d-flex align-items-center gap-3">
          <div class="field-icon">
            <i class="bi bi-paperclip"></i>
          </div>
          <div class="flex-grow-1">
            <input type="file" id="file" name="file" class="form-control custom-input" required>
          </div>
        </div>
      </div>

      <!-- Submit Button -->
      <div class="text-center mt-4 pt-2">
        <button type="submit" class="btn btn-custom-dark w-100">
          Register Document
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
        fetch('/admin/user-search?q=' + encodeURIComponent(q))
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