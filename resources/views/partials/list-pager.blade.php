{{-- "Showing 1-25 of 312 users" + page links. Needs $paginator and $noun. --}}
@if ($paginator->total() > 0)
  <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
    <div class="text-muted small">Showing {{ number_format($paginator->firstItem()) }}&ndash;{{ number_format($paginator->lastItem()) }} of {{ number_format($paginator->total()) }} {{ $noun }}</div>
    {{ $paginator->onEachSide(1)->links('partials.pagination') }}
  </div>
@endif
