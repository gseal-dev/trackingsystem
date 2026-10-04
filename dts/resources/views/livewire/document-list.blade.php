<div>
    <!-- Header Section -->
    <div class="section-header mb-4">
        <div>
            <h1 class="section-title">Documents List</h1>
            <p class="text-muted">Manage and track all registered documents</p>
        </div>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            <!-- Add Document button is now part of the upload form above -->
            <form method="POST" action="{{ route('logout') }}" class="m-0">
                @csrf
                <button type="submit" class="btn btn-dark px-4 py-2" style="background-color: #000000; color: #ffffff; border-radius: 50px; font-weight: 600;">
                    <i class="bi bi-box-arrow-right me-1"></i> Logout
                </button>
            </form>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success mb-4">{{ session('success') }}</div>
    @endif

    <!-- Upload Form -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Upload New Document</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="createDocument">
                @csrf

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Document No</label>
                        <input type="text" class="form-control" wire:model.live="documentNo" placeholder="Enter document number">
                        @error('documentNo') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" wire:model.live="title" placeholder="Enter title">
                        @error('title') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Document Type</label>
                        <input type="text" class="form-control" wire:model.live="documentType" placeholder="Enter document type">
                        @error('documentType') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">From Office</label>
                        <input type="text" class="form-control" wire:model.live="fromOffice" placeholder="Enter office/department name">
                        @error('fromOffice') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Document Date</label>
                        <input type="date" class="form-control" wire:model.live="documentDate">
                        @error('documentDate') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">File (PDF)</label>
                        <input type="file" class="form-control" wire:model="file" accept=".pdf">
                        @error('file') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <span wire:loading.attr="disabled">
                            Uploading...
                        </span>
                        <span wire:loading.remove>
                            Upload Document
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filter Documents</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Search</label>
                    <input type="text" class="form-control" wire:model.live="search" placeholder="Search by title, doc no, owner">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Year</label>
                    <input type="number" class="form-control" wire:model.live="year" min="2000" max="{{ now()->year + 5 }}" placeholder="YYYY">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Month</label>
                    <input type="number" class="form-control" wire:model.live="month" min="1" max="12" placeholder="MM">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Day</label>
                    <input type="number" class="form-control" wire:model.live="day" min="1" max="31" placeholder="DD">
                </div>
                <div class="col-md-3 d-grid">
                    <button type="button" class="btn btn-outline-secondary" wire:click="$reset(['search', 'year', 'month', 'day'])">
                        Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Documents List with Livewire polling for real-time updates -->
    <div wire:poll.5s> <!-- Poll every 5 seconds to refresh list -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Documents List (auto-refreshes every 5 seconds)</h5>
            </div>
            <div class="card-body">
                @if ($documents->isEmpty())
                    <p class="text-muted">No documents found.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Document No</th>
                                    <th>Title</th>
                                    <th>Type</th>
                                    <th>Owner</th>
                                    <th>Department</th>
                                    <th>Date</th>
                                    <th>File</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($documents as $index => $doc)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $doc->documentNo }}</td>
                                        <td>{{ $doc->title }}</td>
                                        <td>{{ $doc->documentType }}</td>
                                        <td>
                                            {{ $doc->owner ? ($doc->owner->firstName . ' ' . $doc->owner->lastName) : 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $doc->department ? $doc->department->depName : 'N/A' }}
                                        </td>
                                        <td>
                                            {{ $doc->documentDate ? $doc->documentDate : ($doc->created_at ? $doc->created_at->format('Y-m-d') : 'N/A') }}
                                        </td>
                                        <td>
                                            @if ($doc->filePath && Storage::disk('public')->exists($doc->filePath))
                                                <a href="{{ Storage::url($doc->filePath) }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                                    ⬇️ Download
                                                </a>
                                            @else
                                                <span class="text-muted">File missing</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if (auth()->user()->can('update', $doc) || auth()->user()->hasRole('admin'))
                                                <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#editModal{{ $doc->documentId }}">
                                                    ✏️ Edit
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Edit Modal (optional) -->
    @foreach ($documents as $doc)
        <div class="modal fade" id="editModal{{ $doc->documentId }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Document {{ $doc->documentNo }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Placeholder for edit form; you can implement Livewire edit if needed -->
                        <p>Edit functionality can be added here.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

@push('scripts')
<script>
    // Optional: Show toast for alerts emitted from Livewire
    document.addEventListener('livewire:alert', event => {
        const { type, message } = event.detail;
        // You can integrate with a toast library or simply use alert for demo
        alert(message);
    });
</script>
@endpush