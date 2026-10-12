{{--
  Edit Document popup (staff only). It opens from the Edit button inside the details popup,
  which fires a "document-edit-requested" event carrying the document's data.
--}}
@php
  $editErrors = $errors->getBag('editDocument');
  $reopenId = old('_editing_id');
  $reopen = $editErrors->any() && $reopenId;
  $fieldIcon = 'font-size: 1.4rem; color: #333; width: 32px; display: flex; justify-content: center;';
@endphp

<div class="modal fade" id="editDocumentModal" tabindex="-1" aria-labelledby="editDocumentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 rounded-4 p-3 shadow-lg" style="background-color: #ffffff !important; color: #1e293b !important;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold text-dark" id="editDocumentModalLabel">Edit Document</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form method="POST" id="editDocumentForm" action="{{ $reopen ? route('staff.document.update', $reopenId) : '#' }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="_editing_id" id="editDocumentId" value="{{ $reopenId }}">
        <div class="modal-body">
          <div id="editDocumentErrors" class="alert alert-danger rounded-4 mb-3 {{ $editErrors->any() ? '' : 'd-none' }}">
            <ul class="mb-0">
              @foreach($editErrors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>

          <div class="d-flex flex-column gap-3">
            <div class="d-flex align-items-center gap-3">
              <div style="{{ $fieldIcon }}"><i class="bi bi-hash"></i></div>
              <div class="flex-grow-1"><input type="text" name="documentNo" id="editDocumentNo" class="form-control rounded-pill text-uppercase bg-white" placeholder="REFERENCE NUMBER" value="{{ old('documentNo') }}" required></div>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div style="{{ $fieldIcon }}"><i class="bi bi-building"></i></div>
              <div class="flex-grow-1"><input type="text" name="fromOffice" id="editFromOffice" class="form-control rounded-pill text-uppercase bg-white" placeholder="FROM OFFICE" value="{{ old('fromOffice') }}" required></div>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div style="{{ $fieldIcon }}"><i class="bi bi-file-earmark-text"></i></div>
              <div class="flex-grow-1"><input type="text" name="documentType" id="editDocumentType" class="form-control rounded-pill text-uppercase bg-white" placeholder="TYPE" value="{{ old('documentType') }}" required></div>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div style="{{ $fieldIcon }}"><i class="bi bi-card-heading"></i></div>
              <div class="flex-grow-1"><input type="text" name="title" id="editTitle" class="form-control rounded-pill text-uppercase bg-white" placeholder="SUBJECT" value="{{ old('title') }}" required></div>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div style="{{ $fieldIcon }}"><i class="bi bi-calendar-event"></i></div>
              <div class="flex-grow-1"><input type="date" name="documentDate" id="editDocumentDate" class="form-control rounded-pill bg-white" value="{{ old('documentDate') }}"></div>
            </div>
            <div class="d-flex align-items-center gap-3">
              <div style="{{ $fieldIcon }}"><i class="bi bi-paperclip"></i></div>
              <div class="flex-grow-1">
                <input type="file" name="file" id="editFile" class="form-control rounded-pill bg-white" accept=".pdf">
                <div class="form-text ms-3">Optional: choose a new PDF only if you want to replace the current file<span id="editCurrentFile"></span>.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark rounded-pill px-4 text-uppercase fw-bold" style="background-color: #000; color: #fff;">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    const modalEl = document.getElementById('editDocumentModal');
    const form = document.getElementById('editDocumentForm');

    function openEditModal() {
      // Keep the popup directly under <body> so nothing can cover it
      if (modalEl.parentElement !== document.body) document.body.appendChild(modalEl);
      bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    // Fill the form from the document the user chose
    document.addEventListener('document-edit-requested', function (event) {
      const doc = event.detail;
      const data = doc.edit || {};

      form.action = doc.updateUrl;
      document.getElementById('editDocumentId').value = doc.id;
      document.getElementById('editDocumentNo').value = data.documentNo || '';
      document.getElementById('editFromOffice').value = data.fromOffice || '';
      document.getElementById('editDocumentType').value = data.documentType || '';
      document.getElementById('editTitle').value = data.title || '';
      document.getElementById('editDocumentDate').value = data.documentDate || '';
      document.getElementById('editFile').value = '';
      document.getElementById('editCurrentFile').textContent = doc.fileName ? ' (' + doc.fileName + ')' : '';
      document.getElementById('editDocumentErrors').classList.add('d-none');

      openEditModal();
    });

    @if($reopen)
    // Saving failed validation: show the form again with the errors and what was typed
    document.addEventListener('DOMContentLoaded', openEditModal);
    @endif
  })();
</script>
@endpush
