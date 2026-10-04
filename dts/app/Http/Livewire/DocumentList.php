<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Document;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Database\Eloquent\Builder;

class DocumentList extends Component
{
    use WithFileUploads;

    // Form fields
    public $title = '';
    public $documentNo = '';
    public $documentType = '';
    public $fromOffice = '';
    public $documentDate = null;
    public $file; // temporary uploaded file

    // List data
    public $documents = [];
    public $departments = [];

    // Search/filter
    public $search = '';
    public $year = '';
    public $month = '';
    public $day = '';

    protected $listeners = [
        'documentCreated' => 'loadDocuments',
    ];

    public function mount()
    {
        $this->loadDepartments();
        $this->loadDocuments();
    }

    public function loadDepartments()
    {
        $this->departments = Department::pluck('depName', 'depID')->toArray();
    }

    public function loadDocuments()
    {
        $user = auth()->user();

        $query = Document::with(['owner', 'department', 'status'])
            ->when($user->departmentID, function ($q) use ($user) {
                // Show documents for user's department (adjust as needed)
                return $q->where('currentDepartmentID', $user->departmentID);
            })
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('documentNo', 'like', '%' . $this->search . '%')
                        ->orWhereHas('owner', function ($q) {
                            $q->where('username', 'like', '%' . request()->input('search') . '%')
                                ->orWhere('firstName', 'like', '%' . request()->input('search') . '%')
                                ->orWhere('lastName', 'like', '%' . request()->input('search') . '%');
                        });
                });
            })
            ->when($this->year, function ($q) {
                $q->where(function ($query) {
                    $query->whereYear('documentDate', $this->year)
                        ->orWhere(function ($sub) {
                            $sub->whereNull('documentDate')
                                ->whereYear('created_at', $this->year);
                        });
                });
            })
            ->when($this->month, function ($q) {
                $q->where(function ($query) {
                    $query->whereMonth('documentDate', $this->month)
                        ->orWhere(function ($sub) {
                            $sub->whereNull('documentDate')
                                ->whereMonth('created_at', $this->month);
                        });
                });
            })
            ->when($this->day, function ($q) {
                $q->where(function ($query) {
                    $query->whereDay('documentDate', $this->day)
                        ->orWhere(function ($sub) {
                            $sub->whereNull('documentDate')
                                ->whereDay('created_at', $this->day);
                        });
                });
            });

        $this->documents = $query->orderByDesc('created_at')->get();
    }

    public function createDocument()
    {
        $this->validate([
            'documentNo' => ['required', Rule::unique('documents', 'documentNo')],
            'title' => 'required|string|max:255',
            'documentType' => 'required|string|max:255',
            'fromOffice' => 'required|string|max:255',
            'documentDate' => 'nullable|date',
            'file' => 'required|file|mimes:pdf|max:10240', // 10MB
        ]);

        // Determine department from fromOffice
        $officeName = trim($this->fromOffice);
        $department = Department::where('depName', 'LIKE', $officeName)->first();

        if (!$department) {
            $department = Department::create([
                'depName' => $officeName,
                'description' => $officeName,
            ]);
        }

        $file = $this->file;
        $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $document = Document::create([
            'documentId' => (string) Str::uuid(),
            'documentNo' => $this->documentNo,
            'title' => $this->title,
            'description' => $this->title, // duplicate as before
            'documentType' => $this->documentType,
            'documentDate' => $this->documentDate ?? now()->toDateString(),
            'ownerID' => auth()->user()->userID,
            'currentStatus' => 1, // Pending
            'currentDepartmentID' => $department->depID,
            'filePath' => $filePath,
        ]);

        // History entry
        \DB::table('document_histories')->insert([
            'documentId' => $document->documentId,
            'prevDepartmentID' => $department->depID,
            'currentDepartmentID' => $department->depID,
            'statusID' => 1,
            'userID' => auth()->user()->userID,
            'action' => 'Document added / registered',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Reset form
        $this->reset(['title', 'documentNo', 'documentType', 'fromOffice', 'documentDate', 'file']);

        // Notify other components to refresh their document list
        $this->emit('documentCreated');

        $this->emit('alert', ['type' => 'success', 'message' => 'Document added successfully!']);
    }

    public function render()
    {
        return view('livewire.document-list');
    }
}