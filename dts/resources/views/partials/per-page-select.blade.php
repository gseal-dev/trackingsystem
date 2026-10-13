{{-- Rows-per-page choice; lives inside the list's GET form. Needs $paginator and $id. --}}
<div class="ms-auto d-flex align-items-center gap-2">
  <label for="{{ $id }}" class="small text-muted mb-0">Rows per page</label>
  <select name="per_page" id="{{ $id }}" class="form-select" style="width: 90px;" onchange="this.form.submit()">
    @foreach(\App\Support\Paging::PER_PAGE_OPTIONS as $n)
      <option value="{{ $n }}" {{ $paginator->perPage() == $n ? 'selected' : '' }}>{{ $n }}</option>
    @endforeach
  </select>
</div>
