{{-- "Showing 1-25 of 312 documents" + page links. Kept inside a live-refresh element by the lists. --}}
@if ($documents->total() > 0)
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
    <div class="text-muted small">Showing {{ number_format($documents->firstItem()) }}&ndash;{{ number_format($documents->lastItem()) }} of {{ number_format($documents->total()) }} documents</div>
    {{ $documents->onEachSide(1)->links('partials.pagination') }}
  </div>
@endif
