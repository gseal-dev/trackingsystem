{{--
  Document details popup, shared by the admin dashboard list and the staff list.
  Open it with a button that carries  data-doc-view="{{ json_encode($document->viewData()) }}".
  Pass  ['canManage' => true]  to also show Edit / Delete (admin only).
--}}
@php $canManage = $canManage ?? false; @endphp

<div class="modal fade" id="documentViewModal" tabindex="-1" aria-labelledby="documentViewTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="documentViewTitle">
          <i class="bi bi-file-earmark-text-fill doc-title-icon"></i>
          <span data-doc-field="subject"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="doc-actions">
          <a href="#" id="documentViewBtn" target="_blank" rel="noopener" class="doc-action-btn doc-action-view"><i class="bi bi-eye-fill"></i> View</a>
          <a href="#" id="documentDownloadBtn" class="doc-action-btn doc-action-download"><i class="bi bi-download"></i> Download</a>
        </div>
        <p class="text-danger small mt-2 mb-0 d-none" id="documentNoFileNote"><i class="bi bi-exclamation-circle me-1"></i> No file is attached to this document.</p>

        <hr class="doc-divider">

        <div class="doc-info-card">
          <div class="doc-info-title"><i class="bi bi-info-circle-fill"></i> Document Information</div>

          <div class="doc-info-row"><div class="doc-info-label">Filename</div><div class="doc-info-value" data-doc-field="fileName"></div></div>
          <div class="doc-info-row"><div class="doc-info-label">Uploaded By</div><div class="doc-info-value" data-doc-field="uploadedBy"></div></div>
          <div class="doc-info-row"><div class="doc-info-label">Upload Date</div><div class="doc-info-value" data-doc-field="uploadedAt"></div></div>
          <div class="doc-info-row"><div class="doc-info-label">Reference No.</div><div class="doc-info-value" data-doc-field="referenceNo"></div></div>
          <div class="doc-info-row"><div class="doc-info-label">From Office</div><div class="doc-info-value" data-doc-field="office"></div></div>
          <div class="doc-info-row"><div class="doc-info-label">Type</div><div class="doc-info-value text-uppercase" data-doc-field="type"></div></div>
          <div class="doc-info-row"><div class="doc-info-label">Document Date</div><div class="doc-info-value" data-doc-field="date"></div></div>
          <div class="doc-info-row"><div class="doc-info-label">Description</div><div class="doc-info-value" data-doc-field="description"></div></div>
        </div>
      </div>

      @if($canManage)
        <div class="modal-footer">
          <a href="#" id="documentEditBtn" class="btn btn-outline-secondary px-4"><i class="bi bi-pencil me-1"></i> Edit</a>
          <form method="POST" action="#" id="documentDeleteForm" class="m-0" onsubmit="return confirm('Are you sure you want to delete this document?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger px-4"><i class="bi bi-trash me-1"></i> Delete</button>
          </form>
        </div>
      @endif
    </div>
  </div>
</div>

@push('head')
<style>
  #documentViewModal .modal-content { border: 0; border-radius: 8px; overflow: hidden; background: #fff; color: #1f2937; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25); }
  #documentViewModal .modal-header { background: #f8f9fa; border-bottom: 1px solid #e5e7eb; padding: 1rem 1.5rem; }
  #documentViewModal .modal-title { display: flex; align-items: center; gap: 0.6rem; font-size: 1.05rem; font-weight: 700; color: #1f2937; word-break: break-word; }
  #documentViewModal .doc-title-icon { color: #ea3a14; font-size: 1.15rem; }
  #documentViewModal .modal-body { padding: 1.5rem; background: #fff; }
  #documentViewModal .modal-footer { background: #f8f9fa; border-top: 1px solid #e5e7eb; padding: 0.75rem 1.5rem; gap: 0.5rem; }

  /* View / Download: two equal full-width buttons */
  #documentViewModal .doc-actions { display: flex; gap: 1rem; }
  #documentViewModal .doc-action-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1rem; border-radius: 4px; font-weight: 700; color: #fff; text-decoration: none; transition: background 0.15s; }
  #documentViewModal .doc-action-view { background: #ea3a14; }
  #documentViewModal .doc-action-view:hover { background: #cf3010; color: #fff; }
  #documentViewModal .doc-action-download { background: #6c757d; }
  #documentViewModal .doc-action-download:hover { background: #5a6268; color: #fff; }
  #documentViewModal .doc-action-btn.disabled { opacity: 0.45; pointer-events: none; }
  #documentViewModal .doc-divider { border: 0; border-top: 1px solid #e5e7eb; opacity: 1; margin: 1.25rem 0; }

  /* Document Information card */
  #documentViewModal .doc-info-card { background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 6px; padding: 1.25rem 1.5rem 0.5rem; }
  #documentViewModal .doc-info-title { display: flex; align-items: center; gap: 0.6rem; font-size: 1.05rem; font-weight: 700; color: #1f2937; padding-bottom: 0.6rem; margin-bottom: 1rem; border-bottom: 2px solid #ea3a14; }
  #documentViewModal .doc-info-title i { color: #ea3a14; font-size: 1rem; }
  #documentViewModal .doc-info-row { display: grid; grid-template-columns: minmax(130px, 30%) 1fr; border-bottom: 1px solid #e5e7eb; }
  #documentViewModal .doc-info-row:last-child { border-bottom: 0; }
  #documentViewModal .doc-info-label { padding: 0.9rem 1rem; color: #6b7280; font-weight: 600; }
  #documentViewModal .doc-info-value { padding: 0.9rem 1rem; background: #fff; color: #4b5563; word-break: break-word; }

  @media (max-width: 575.98px) {
    #documentViewModal .doc-actions { flex-direction: column; gap: 0.5rem; }
    #documentViewModal .doc-info-row { grid-template-columns: 1fr; }
    #documentViewModal .doc-info-label { padding-bottom: 0.25rem; }
  }
</style>
@endpush

@push('scripts')
<script>
  (function () {
    const modalEl = document.getElementById('documentViewModal');
    const viewBtn = document.getElementById('documentViewBtn');
    const downloadBtn = document.getElementById('documentDownloadBtn');
    const noFileNote = document.getElementById('documentNoFileNote');
    const editBtn = document.getElementById('documentEditBtn');
    const deleteForm = document.getElementById('documentDeleteForm');

    function setLink(link, url) {
      if (url) {
        link.href = url;
        link.classList.remove('disabled');
        link.removeAttribute('aria-disabled');
      } else {
        link.href = '#';
        link.classList.add('disabled');
        link.setAttribute('aria-disabled', 'true');
      }
    }

    // One delegated listener, so rows added by the live refresh work too
    document.addEventListener('click', function (event) {
      const trigger = event.target.closest('[data-doc-view]');
      if (!trigger) return;

      const doc = JSON.parse(trigger.dataset.docView);

      modalEl.querySelectorAll('[data-doc-field]').forEach(function (el) {
        const value = doc[el.dataset.docField];
        el.textContent = (value === null || value === undefined || value === '') ? '-' : value;
      });

      setLink(viewBtn, doc.viewUrl);
      setLink(downloadBtn, doc.downloadUrl);
      noFileNote.classList.toggle('d-none', !!doc.viewUrl);

      if (editBtn) editBtn.href = doc.editUrl;
      if (deleteForm) deleteForm.action = doc.deleteUrl;

      // Keep the popup directly under <body> so nothing can cover it
      if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
  })();
</script>
@endpush
