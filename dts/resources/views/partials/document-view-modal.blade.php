{{--
  Document details popup, shared by the admin dashboard list and the staff list.
  Open it with a button that carries  data-doc-view="{{ json_encode($document->viewData()) }}".
  Pass  ['canEdit' => true]    to show the Edit button   (staff only),
        ['canDelete' => true]  to show the Delete button (admin only).

  The popup has two states:
  - view (default): read-only Document Information card, opened from [data-doc-view].
  - edit: the same card turns into textboxes prefilled with the current data,
    opened by clicking the Edit button inside the view state. Save writes back
    through the existing staff.document.update route.

  On a failed validation the redirect back re-opens the popup in edit mode
  (see $reopen below) so the user's typed values and errors are shown.
--}}
@php
  $canEdit = $canEdit ?? false;
  $canDelete = $canDelete ?? false;

  $editErrors = $errors->getBag('editDocument');
  $reopenId = old('_editing_id');
  $reopen = $editErrors->any() && $reopenId;
  $editMode = $reopen ? 'edit' : 'view';
@endphp

<div class="modal fade" id="documentViewModal" tabindex="-1" aria-labelledby="documentViewTitle" aria-hidden="true"
     data-edit-mode="{{ $editMode }}"
     data-reopen-id="{{ $reopenId ?? '' }}">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="documentViewTitle">
          <i class="bi bi-file-earmark-text-fill doc-title-icon"></i>
          <span data-doc-field="heading"></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body">
        <div class="doc-actions">
          <a href="#" id="documentViewBtn" target="_blank" rel="noopener" class="doc-action-btn doc-action-view"><i class="bi bi-eye-fill"></i> View</a>
          <a href="#" id="documentDownloadBtn" class="doc-action-btn doc-action-download"><i class="bi bi-download"></i> Download</a>
          @if($canEdit)
            <button type="button" id="documentUploadVersionBtn" class="doc-action-btn doc-action-upload"><i class="bi bi-cloud-arrow-up"></i> Upload new version</button>
          @endif
        </div>
        <p class="text-danger small mt-2 mb-0 d-none" id="documentNoFileNote"><i class="bi bi-exclamation-circle me-1"></i> No file is attached to this document.</p>

        {{-- Hidden form for uploading a new file version (submitted normally, redirects back with flash) --}}
        <form id="documentUploadVersionForm" method="POST" action="#" enctype="multipart/form-data" class="d-none">
          @csrf
          <input type="file" id="documentUploadVersionFile" name="file" accept=".pdf">
        </form>

        <hr class="doc-divider">

        <div class="doc-info-card">
          <div class="doc-info-title"><i class="bi bi-info-circle-fill"></i> Document Information</div>

          {{-- VIEW STATE: read-only labelled rows --}}
          <div id="documentViewFields">
            <div class="doc-info-row"><div class="doc-info-label">Filename</div><div class="doc-info-value" data-doc-field="fileName"></div></div>
            <div class="doc-info-row"><div class="doc-info-label">Uploaded By</div><div class="doc-info-value" data-doc-field="uploadedBy"></div></div>
            <div class="doc-info-row"><div class="doc-info-label">Upload Date</div><div class="doc-info-value" data-doc-field="uploadedAt"></div></div>
            <div class="doc-info-row"><div class="doc-info-label">Reference No.</div><div class="doc-info-value" data-doc-field="referenceNo"></div></div>
            <div class="doc-info-row"><div class="doc-info-label">Subject</div><div class="doc-info-value" data-doc-field="subject"></div></div>
            <div class="doc-info-row"><div class="doc-info-label">From Office</div><div class="doc-info-value" data-doc-field="office"></div></div>
            <!-- <div class="doc-info-row"><div class="doc-info-label">Type</div><div class="doc-info-value text-uppercase" data-doc-field="type"></div></div> -->
            <div class="doc-info-row"><div class="doc-info-label">Document Date</div><div class="doc-info-value" data-doc-field="date"></div></div>
            <div class="doc-info-row"><div class="doc-info-label">Description</div><div class="doc-info-value" data-doc-field="description"></div></div>
          </div>

          {{-- EDIT STATE: textboxes prefilled with the current data --}}
          <form id="documentEditForm" method="POST"
                action="{{ $reopen ? route('staff.document.update', $reopenId) : '#' }}"
                enctype="multipart/form-data" class="d-none">
            @csrf
            <input type="hidden" name="_editing_id" id="editDocumentId" value="{{ $reopenId ?? '' }}">
            <input type="hidden" id="editUpdateUrl" value="{{ $reopen ? route('staff.document.update', $reopenId) : '' }}">

            <div class="doc-info-row">
              <div class="doc-info-label">Reference No.</div>
              <div class="doc-info-value">
                <input type="text" name="documentNo" id="editDocumentNo"
                       class="edit-input" placeholder="REFERENCE NUMBER"
                       value="{{ old('documentNo') ?? '' }}" required>
              </div>
            </div>
            <div class="doc-info-row">
              <div class="doc-info-label">From Office</div>
              <div class="doc-info-value">
                <input type="text" name="fromOffice" id="editFromOffice"
                       class="edit-input" placeholder="FROM OFFICE"
                       value="{{ old('fromOffice') ?? '' }}" required>
              </div>
            </div>
            <div class="doc-info-row">
              <div class="doc-info-label">Subject</div>
              <div class="doc-info-value">
                <input type="text" name="title" id="editTitle"
                       class="edit-input text-uppercase" placeholder="SUBJECT"
                       value="{{ old('title') ?? '' }}" required>
              </div>
            </div>
            <div class="doc-info-row">
              <div class="doc-info-label">Document Date</div>
              <div class="doc-info-value">
                <input type="date" name="documentDate" id="editDocumentDate"
                       class="edit-input"
                       value="{{ old('documentDate') ?? '' }}">
              </div>
            </div>
            <div class="doc-info-row">
              <div class="doc-info-label">Description</div>
              <div class="doc-info-value">
                <textarea name="description" id="editDescription"
                          class="edit-input edit-textarea" placeholder="DESCRIPTION">{{ old('description') ?? '' }}</textarea>
              </div>
            </div>
            {{-- Edit errors, scoped to this popup's own bag --}}
            <div id="editDocumentErrors" class="alert alert-danger rounded-4 mb-3 {{ $editErrors->any() ? '' : 'd-none' }}">
              <ul class="mb-0">
                @foreach($editErrors->all() as $error)
                  <li>{{ $error }}</li>
                @endforeach
              </ul>
            </div>
          </form>
        </div>
      </div>

      @if($canEdit || $canDelete)
        <div class="modal-footer">
          {{-- VIEW STATE footer buttons --}}
          <div id="documentViewFooter" @class(['d-none' => $editMode === 'edit'])>
            @if($canEdit)
              <button type="button" id="documentEditBtn" class="btn btn-outline-secondary px-4"><i class="bi bi-pencil me-1"></i> Edit</button>
            @endif
            @if($canDelete)
              <form method="POST" action="#" id="documentDeleteForm" class="m-0" onsubmit="return confirm('Are you sure you want to delete this document?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger px-4"><i class="bi bi-trash me-1"></i> Delete</button>
              </form>
            @endif
          </div>

          {{-- EDIT STATE footer buttons --}}
          <div id="documentEditFooter" @class(['d-none' => $editMode === 'view'])>
            <button type="button" class="btn btn-outline-secondary px-4" id="documentEditCancel">Cancel</button>
            <button type="submit" form="documentEditForm" class="btn btn-dark px-4 text-uppercase fw-bold" style="background-color: #000; color: #fff;">Save Changes</button>
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

@push('head')
<style>
  #documentViewModal .modal-content { border: 0; border-radius: 4px; overflow: hidden; background: #fff; color: #1f2937; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.25); }
  #documentViewModal .modal-header { background: #f8f9fa; border-bottom: 1px solid #e5e7eb; padding: 1rem 1.5rem; }
  #documentViewModal .modal-title { display: flex; align-items: center; gap: 0.6rem; font-size: 1.05rem; font-weight: 700; color: #1f2937; word-break: break-word; }
  #documentViewModal .doc-title-icon { color: #001253; font-size: 1.15rem; }
  #documentViewModal .modal-body { padding: 1.5rem; background: #fff; }
  #documentViewModal .modal-footer { background: #f8f9fa; border-top: 1px solid #e5e7eb; padding: 0.75rem 1.5rem; gap: 0.5rem; }

  /* View / Download: two equal full-width buttons */
  #documentViewModal .doc-actions { display: flex; gap: 1rem; }
  #documentViewModal .doc-action-btn { flex: 1; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.75rem 1rem; border-radius: 2px; font-weight: 700; color: #fff; text-decoration: none; transition: background 0.15s; }
  #documentViewModal .doc-action-view { background: #001253; }
  #documentViewModal .doc-action-view:hover { background: #284096; color: #fff; }
  #documentViewModal .doc-action-download { background: #ea3a14; }
  #documentViewModal .doc-action-download:hover { background: #b83719; color: #fff; }
  #documentViewModal .doc-action-upload { background: #001253; }
  #documentViewModal .doc-action-upload:hover { background: #284096; color: #fff; }
  #documentViewModal .doc-action-btn.disabled { opacity: 0.45; pointer-events: none; }
  #documentViewModal .doc-divider { border: 0; border-top: 1px solid #e5e7eb; opacity: 1; margin: 1.25rem 0; }

  /* Document Information card */
  #documentViewModal .doc-info-card { background: #f8f9fa; border: 1px solid #e5e7eb; border-radius: 4px; padding: 1.25rem 1.5rem 0.5rem; }
  #documentViewModal .doc-info-title { display: flex; align-items: center; gap: 0.6rem; font-size: 1.05rem; font-weight: 700; color: #1f2937; padding-bottom: 0.6rem; margin-bottom: 1rem; border-bottom: 2px solid #001253; }
  #documentViewModal .doc-info-title i { color: #001253; font-size: 1rem; }
  #documentViewModal .doc-info-row { display: grid; grid-template-columns: minmax(130px, 30%) 1fr; border-bottom: 1px solid #e5e7eb; }
  #documentViewModal .doc-info-row:last-child { border-bottom: 0; }
  #documentViewModal .doc-info-label { padding: 0.9rem 1rem; color: #6b7280; font-weight: 600; }
  #documentViewModal .doc-info-value { padding: 0.9rem 1rem; background: #fff; color: #4b5563; word-break: break-word; }

  /* Edit state: textboxes inside the same card rows */
  #documentViewModal .edit-input { width: 100%; padding: 0.5rem 0.75rem; border: 1px solid #cbd5e1; border-radius: 4px; background: #fff; color: #1f2937; font-size: 0.95rem; }
  #documentViewModal .edit-input:focus { border-color: #000; outline: none; box-shadow: 0 0 0 2px rgba(0,0,0,0.12); }
  #documentViewModal .edit-textarea { min-height: 90px; resize: vertical; font-family: inherit; }

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
    const editForm = document.getElementById('documentEditForm');
    const cancelBtn = document.getElementById('documentEditCancel');
    const viewFields = document.getElementById('documentViewFields');
    const viewFooter = document.getElementById('documentViewFooter');
    const editFooter = document.getElementById('documentEditFooter');
    const editErrors = document.getElementById('editDocumentErrors');
    let currentDoc = null;

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

    function showView() {
      modalEl.dataset.editMode = 'view';
      viewFields.classList.remove('d-none');
      editForm.classList.add('d-none');
      viewFooter.classList.remove('d-none');
      editFooter.classList.add('d-none');
      editErrors.classList.add('d-none');
    }

    function showEdit(doc, preserve = false) {
      modalEl.dataset.editMode = 'edit';
      viewFields.classList.add('d-none');
      editForm.classList.remove('d-none');
      viewFooter.classList.add('d-none');
      editFooter.classList.remove('d-none');
      editErrors.classList.add('d-none');

      const updateUrl = document.getElementById('editUpdateUrl').value;
      editForm.action = updateUrl || doc.updateUrl;
      document.getElementById('editDocumentId').value = doc.id;

      // When reopening after a failed validation, Blade already rendered the
      // user's typed (old()) values into the inputs, so don't overwrite them.
      if (!preserve) {
        const fill = (id, value) => {
          const el = document.getElementById(id);
          if (el) el.value = value === null || value === undefined ? '' : value;
        };

        const e = doc.edit || {};
        fill('editDocumentNo', e.documentNo);
        fill('editFromOffice', e.fromOffice);
        fill('editTitle', e.title);
        fill('editDocumentDate', e.documentDate);
        const descEl = document.getElementById('editDescription');
        if (descEl) descEl.value = (doc.description && doc.description !== 'No description') ? doc.description : '';
      }
    }

    // One delegated listener, so rows added by the live refresh work too
    document.addEventListener('click', function (event) {
      const trigger = event.target.closest('[data-doc-view]');
      if (!trigger) return;

      const doc = JSON.parse(trigger.dataset.docView);
      currentDoc = doc;

      modalEl.querySelectorAll('[data-doc-field]').forEach(function (el) {
        const value = doc[el.dataset.docField];
        el.textContent = (value === null || value === undefined || value === '') ? '-' : value;
      });

      setLink(viewBtn, doc.viewUrl);
      setLink(downloadBtn, doc.downloadUrl);
      noFileNote.classList.toggle('d-none', !!doc.viewUrl);

      if (deleteForm) deleteForm.action = doc.deleteUrl;

      // Clicking a document row always opens the view state with fresh data.
      showView();

      // Keep the popup directly under <body> so nothing can cover it
      if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });

    // Edit: switch the SAME popup from view state to edit state (textboxes appear, prefilled)
    if (editBtn) {
      editBtn.addEventListener('click', function () {
        const doc = currentDoc;
        if (!doc) return;
        showEdit(doc);
      });
    }

    // Cancel: return to the view state without leaving the popup
    if (cancelBtn) {
      cancelBtn.addEventListener('click', function () {
        if (!currentDoc) return;
        showView();
      });
    }

    // Upload a new version of the attached file (no edit mode involved).
    const uploadBtn = document.getElementById('documentUploadVersionBtn');
    const uploadForm = document.getElementById('documentUploadVersionForm');
    const uploadFile = document.getElementById('documentUploadVersionFile');

    if (uploadBtn && uploadForm && uploadFile) {
      uploadBtn.addEventListener('click', function () {
        const doc = currentDoc;
        if (!doc) return;
        uploadForm.action = doc.uploadVersionUrl || '#';
        uploadFile.value = '';
        uploadFile.click();
      });

      uploadFile.addEventListener('change', function () {
        if (uploadFile.files.length) uploadForm.submit();
      });
    }

    // Initialize the popup in the state it was rendered in (view, or edit after
    // a failed validation). Reflect that in the shown/hidden sections.
    if (modalEl.dataset.editMode === 'edit') {
      showEdit({ id: document.getElementById('editDocumentId').value }, true);
    } else {
      showView();
    }

    // Reopen after failed validation: show the edit popup with the typed values
    document.addEventListener('DOMContentLoaded', function () {
      if (modalEl.dataset.editMode === 'edit') {
        if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
      }
    });

    // Keep the popup directly under <body> so nothing can cover it
    if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);

  })();
</script>
@endpush
