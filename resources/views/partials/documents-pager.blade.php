{{-- "Showing 1-25 of 312 documents" + page links. Kept inside a live-refresh element by the lists. --}}
@include('partials.list-pager', ['paginator' => $documents, 'noun' => 'documents'])
