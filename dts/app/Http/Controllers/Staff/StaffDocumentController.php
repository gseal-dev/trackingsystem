<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Document;
use App\Models\Department;
use App\Support\DocumentList;

class StaffDocumentController extends Controller
{
    // Show documents for staff's department
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\Document::with('department')
            ->when($user->departmentID, function($q) use ($user) {
                return $q->where('currentDepartmentID', $user->departmentID);
            });

        $documents = $query->get();

        $monthlyCounts = $documents->groupBy(function($doc) {
            return $doc->documentDate ? date('Y-m', strtotime($doc->documentDate)) : date('Y-m', strtotime($doc->created_at));
        })->map->count();

        $totalDocuments = $documents->count();

        // Uploads made by this user only (not their department's or anyone else's)
        $myUploads = Document::where('ownerID', $user->userID)->count();

        // ...and how many of those were uploaded this month (months follow Philippine time)
        $monthStart = now(Document::DISPLAY_TIMEZONE)->startOfMonth();
        $myUploadsThisMonth = Document::where('ownerID', $user->userID)
            ->where('created_at', '>=', $monthStart->copy()->setTimezone(config('app.timezone')))
            ->count();
        $monthLabel = $monthStart->format('F Y');

        // Starting value of the live date/time shown in the card header
        $now = now(Document::DISPLAY_TIMEZONE);
        $clockDate = $now->format('l, F j, Y');
        $clockTime = $now->format('g:i:s A');

        return view('staff.staff', compact(
            'totalDocuments', 'monthlyCounts', 'myUploads', 'myUploadsThisMonth', 'monthLabel', 'clockDate', 'clockTime'
        ));
    }

    /** Documents the logged-in staff member may see, filtered and searched, newest first. */
    private function staffDocumentsQuery(Request $request)
    {
        $user = auth()->user();

        return Document::with(['department', 'owner'])
            ->when($user->departmentID, function ($q) use ($user) {
                return $q->where('currentDepartmentID', $user->departmentID);
            })
            ->dateRange($request->input('date_from'), $request->input('date_to'))
            ->dateFilter($request->input('year'), $request->input('month'), $request->input('day'))
            ->when($request->filled('department'), function ($q) use ($request) {
                return $q->where('currentDepartmentID', $request->input('department'));
            })
            ->search($request->input('q'))
            ->orderByDesc('documentDate')
            ->orderByDesc('created_at')
            ->orderByDesc('documentId');
    }

    public function documents(Request $request)
    {
        $documents = $this->staffDocumentsQuery($request)
            ->paginate(DocumentList::perPage($request))
            ->withQueryString();

        // e.g. the last page emptied out after deletions: go to the new last page
        if ($documents->isEmpty() && $documents->currentPage() > 1) {
            return redirect()->to($documents->url($documents->lastPage()));
        }

        $departments = Department::all();

        return view('staff.documents', compact('documents', 'departments'));
    }

    /** CSV of every document matching the current filters (not only the page on screen). */
    public function exportDocuments(Request $request)
    {
        return DocumentList::csv(
            $this->staffDocumentsQuery($request),
            ['Reference Number', 'Document Title', 'From Office', 'Subject', 'Date'],
            fn ($d) => [
                $d->documentNo,
                $d->documentTitle,
                $d->department->depName ?? '',
                $d->title,
                $d->documentDate ? \Carbon\Carbon::parse($d->documentDate)->format('F d, Y') : $d->created_at?->format('F d, Y'),
            ]
        );
    }

    /** CSV export of history matching the current filters. */
    public function exportHistory(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

        $historiesQuery = \App\Models\DocumentHistory::with(['document.owner', 'document.department', 'user'])
            ->when($user->departmentID, function($q) use ($user) {
                return $q->where(function($sub) use ($user) {
                    $sub->where('currentDepartmentID', $user->departmentID)
                        ->orWhere('prevDepartmentID', $user->departmentID)
                        ->orWhere('userID', $user->userID);
                });
            });

        if ($search) {
            $historiesQuery->whereHas('document', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                    ->orWhere('documentTitle', 'LIKE', "%$search%")
                    ->orWhere('documentNo', 'LIKE', "%$search%")
                    ->orWhereHas('owner', function ($oq) use ($search) {
                        $oq->where('username', 'LIKE', "%$search%")
                            ->orWhere('firstName', 'LIKE', "%$search%")
                            ->orWhere('lastName', 'LIKE', "%$search%");
                    });
            });
        }

        if ($request->filled('year') || $request->filled('month') || $request->filled('day')) {
            $historiesQuery->whereHas('document', function ($q) use ($request) {
                $q->dateFilter($request->input('year'), $request->input('month'), $request->input('day'));
            });
        }

        $histories = $historiesQuery
            ->orderByDesc('created_at')
            ->orderByDesc('historyID')
            ->get(); // Get all records for export, not paginated

        // Prepare data for export
        $data = [];
        foreach ($histories as $history) {
            // Get user name
            $userName = 'Unknown';
            if ($history->user) {
                $firstName = trim($history->user->firstName ?? '');
                $lastName = trim($history->user->lastName ?? '');
                if ($firstName || $lastName) {
                    $userName = trim($firstName . ' ' . $lastName);
                }
                if (!$userName) {
                    $userName = $history->user->username ?? 'Unknown';
                }
            }

            // Format timestamp
            $timestamp = $history->created_at
                ->timezone(\App\Models\Document::DISPLAY_TIMEZONE)
                ->format('F d, Y \\a\\t h:i A');

            $data[] = [
                'Reference Number' => $history->document?->documentNo ?? '-',
                'From Office' => $history->document?->department?->depName ?? '-',
                'Subject' => $history->document?->title ?? '-',
                'Action' => $history->action ?? '-',
                'User' => $userName,
                'Timestamp' => $timestamp,
            ];
        }

        // Generate filename with timestamp
        $filename = 'document_history_' . now()->format('Y-m-d_H-i-s');

        // CSV export (works in Excel too)
        $output = fopen('php://output', 'w');
        // Add BOM for proper UTF-8 in Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

        // Add headers
        fputcsv($output, array_keys($data[0] ?? ['Reference Number', 'From Office', 'Subject', 'Action', 'User', 'Timestamp']));

        // Add data rows
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);

        return response()->stream(
            function () use ($output) {
                // This is handled above
            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '.csv"',
            ]
        );
    }


    public function history(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

        foreach (\App\Models\Document::all() as $doc) {
            \DB::table('document_histories')->updateOrInsert(
                ['documentId' => $doc->documentId],
                [
                    'prevDepartmentID' => $doc->currentDepartmentID,
                    'currentDepartmentID' => $doc->currentDepartmentID,
                    'userID' => $doc->ownerID ?? $user->userID,
                    'action' => 'Document added / registered',
                    'created_at' => $doc->created_at,
                    'updated_at' => $doc->updated_at ?? $doc->created_at,
                ]
            );
        }

        $historiesQuery = \App\Models\DocumentHistory::with(['document.owner', 'document.department', 'user'])
            ->when($user->departmentID, function($q) use ($user) {
                return $q->where(function($sub) use ($user) {
                    $sub->where('currentDepartmentID', $user->departmentID)
                        ->orWhere('prevDepartmentID', $user->departmentID)
                        ->orWhere('userID', $user->userID);
                });
            });

        if ($search) {
            $historiesQuery->whereHas('document', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                ->orWhere('documentTitle', 'LIKE', "%$search%")
                ->orWhere('documentNo', 'LIKE', "%$search%")
                ->orWhereHas('owner', function ($oq) use ($search) {
                    $oq->where('username', 'LIKE', "%$search%")
                        ->orWhere('firstName', 'LIKE', "%$search%")
                        ->orWhere('lastName', 'LIKE', "%$search%");
                });
            });
        }

        if ($request->filled('year') || $request->filled('month') || $request->filled('day')) {
            $historiesQuery->whereHas('document', function ($q) use ($request) {
                $q->dateFilter($request->input('year'), $request->input('month'), $request->input('day'));
            });
        }

        $histories = $historiesQuery
            ->orderByDesc('created_at')
            ->orderByDesc('historyID')
            ->paginate(DocumentList::perPage($request))
            ->withQueryString();

        // e.g. the last page emptied out: go to the new last page
        if ($histories->isEmpty() && $histories->currentPage() > 1) {
            return redirect()->to($histories->url($histories->lastPage()));
        }

        return view('staff.history', compact('histories'));
    }


    // The Upload New Document page (a page of its own, linked from the document lists)
    public function create()
    {
        $user = auth()->user();
        $fullName = trim(($user->firstName ?? '') . ' ' . ($user->lastName ?? ''));

        return view('documents.upload', [
            'offices' => Department::orderBy('depName')->pluck('depName'),
            'uploadedBy' => $fullName !== '' ? $fullName . ' (' . $user->username . ')' : $user->username,
            'uploadDate' => now()->timezone(Document::DISPLAY_TIMEZONE)->format('Y-m-d H:i'),
            'backRoute' => $this->listRouteFor($user),
        ]);
    }

    // Names shown in validation messages ("The reference number has already been taken.")
    private function fieldNames(): array
    {
        return [
            'documentNo' => 'reference number',
            'fromOffice' => 'from office',
            'documentDate' => 'document date',
            'title' => 'subject',
            'file' => 'PDF file',
        ];
    }

    // Where each role goes to see the document list
    private function listRouteFor($user): string
    {
        return $user->role?->roleName === 'Admin' ? 'admin.documents' : 'staff.documents';
    }

    public function store(Request $request)
    {
        $request->validate([
            'documentNo' => 'required|unique:documents,documentNo',
            'title' => 'required|string|max:255',
            'fromOffice' => 'required|string|max:255',
            'documentDate' => 'nullable|date',
            'file' => 'required|file|mimes:pdf|max:10240',
        ], [], $this->fieldNames());

        $officeName = trim($request->fromOffice);
        $department = Department::where('depName', 'LIKE', $officeName)->first();
        if (!$department) {
            $department = Department::create([
                'depName' => $officeName,
                'description' => $officeName,
            ]);
        }

        $file = $request->file('file');
        $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $newDoc = Document::create([
            'documentId' => (string) Str::uuid(),
            'documentNo' => $request->documentNo,
            'title' => $request->title,
            'description' => $request->title,
            'documentDate' => $request->documentDate ?? now()->toDateString(),
            'ownerID' => auth()->user()->userID,
            'currentDepartmentID' => $department->depID,
            'filePath' => $filePath,
        ]);

        \DB::table('document_histories')->insert([
            'documentId' => $newDoc->documentId,
            'prevDepartmentID' => $department->depID,
            'currentDepartmentID' => $department->depID,
            'userID' => auth()->user()->userID,
            'action' => 'Document added / registered',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route($this->listRouteFor(auth()->user()))->with('success', 'Document added successfully!');
    }

    // Editing happens in a popup on the documents list (no separate edit page).
    public function update(Request $request, Document $document)
    {
        // Own error bag, so the Edit popup's errors don't reopen the Add Document popup
        $request->validateWithBag('editDocument', [
            'documentNo' => 'required|unique:documents,documentNo,' . $document->documentId . ',documentId',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'fromOffice' => 'required|string|max:255',
            'documentDate' => 'nullable|date',
        ], [], $this->fieldNames());

        $officeName = trim($request->fromOffice);
        $department = Department::where('depName', 'LIKE', $officeName)->first();
        if (!$department) {
            $department = Department::create([
                'depName' => $officeName,
                'description' => $officeName,
            ]);
        }

        // Capture the original department ID for history tracking
        $originalDepartmentID = $document->currentDepartmentID;

        $updateData = [
            'documentNo' => $request->documentNo,
            'title' => $request->title,
            'description' => $request->description,
            'documentDate' => $request->documentDate ?? $document->documentDate,
            'currentDepartmentID' => $department->depID,
        ];

        $document->update($updateData);

        // Create history record for the update
        \DB::table('document_histories')->insert([
            'documentId' => $document->documentId,
            'prevDepartmentID' => $originalDepartmentID,
            'currentDepartmentID' => $department->depID,
            'userID' => auth()->user()->userID,
            'action' => 'Document updated',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Stay on the list the user was looking at (keeps their year/month/day filters)
        return redirect()->to(url()->previous(route('staff.documents')))->with('success', 'Document updated successfully!');
    }

    // Upload a new version of the attached file (replaces the PDF only, no edit mode).
    public function uploadVersion(Request $request, Document $document)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
        ], [], ['file' => 'PDF file']);

        if ($document->filePath && \Storage::disk('public')->exists($document->filePath)) {
            \Storage::disk('public')->delete($document->filePath);
        }

        $file = $request->file('file');
        $fileName = \Illuminate\Support\Str::uuid() . '_' . $file->getClientOriginalName();
        $document->update(['filePath' => $file->storeAs('documents', $fileName, 'public')]);

        \DB::table('document_histories')->insert([
            'documentId' => $document->documentId,
            'prevDepartmentID' => $document->currentDepartmentID,
            'currentDepartmentID' => $document->currentDepartmentID,
            'userID' => auth()->user()->userID,
            'action' => 'Document uploaded new version',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->to(url()->previous(route('staff.documents')))->with('success', 'New version uploaded successfully!');
    }

    public function delete(Document $document)
    {
        $documentId = $document->documentId;
        $documentTitle = $document->title;

        // Create history record for the deletion before soft deleting
        \DB::table('document_histories')->insert([
            'documentId' => $document->documentId,
            'prevDepartmentID' => $document->currentDepartmentID,
            'currentDepartmentID' => $document->currentDepartmentID,
            'userID' => auth()->user()->userID,
            'action' => 'Document deleted',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $document->delete();

        return back()->with([
            'success' => 'Document deleted successfully.',
            'undo_delete_id' => $documentId,
            'undo_delete_title' => $documentTitle,
        ]);
    }

    public function undoDelete($id)
    {
        $document = Document::withTrashed()->where('documentId', $id)->firstOrFail();
        $document->restore();

        // Create history record for the restoration
        \DB::table('document_histories')->insert([
            'documentId' => $document->documentId,
            'prevDepartmentID' => $document->currentDepartmentID,
            'currentDepartmentID' => $document->currentDepartmentID,
            'userID' => auth()->user()->userID,
            'action' => 'Document restored',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Document retrieved successfully!');
    }

    public function import(Request $request)
    {
        $request->validate([
            'import_file' => 'required|file|mimes:csv,txt,xlsx|max:10240',
        ]);

        $file = $request->file('import_file');
        $path = $file->getRealPath();
        
        $handle = fopen($path, 'r');
        if ($handle === false) {
            return back()->withErrors(['import_file' => 'Could not read uploaded file.']);
        }

        $header = fgetcsv($handle); // skip header
        $importedCount = 0;

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 5) continue;
            [$documentNo, $fromOffice, $title, $documentDate] = array_pad($row, 4, '');

            $documentNo = trim($documentNo);
            if (empty($documentNo)) continue;

            if (Document::where('documentNo', $documentNo)->exists()) {
                continue;
            }

            $officeName = trim($fromOffice) ?: 'General Office';
            $department = Department::where('depName', 'LIKE', $officeName)->first();
            if (!$department) {
                $department = Department::create([
                    'depName' => $officeName,
                    'description' => $officeName,
                ]);
            }

            $newDoc = Document::create([
                'documentId' => (string) Str::uuid(),
                'documentNo' => $documentNo,
                'title' => trim($title) ?: 'Untitled Document',
                'description' => trim($title) ?: 'Untitled Document',
                'documentDate' => !empty($documentDate) ? date('Y-m-d', strtotime($documentDate)) : now()->toDateString(),
                'ownerID' => auth()->user()->userID,
                'currentDepartmentID' => $department->depID,
            ]);

            \DB::table('document_histories')->insert([
                'documentId' => $newDoc->documentId,
                'prevDepartmentID' => $department->depID,
                'currentDepartmentID' => $department->depID,
                'userID' => auth()->user()->userID,
                'action' => 'Imported via CSV/Excel',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $importedCount++;
        }
        fclose($handle);

        return back()->with('success', "Successfully imported {$importedCount} documents!");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="document_import_template.csv"',
        ];
        
        $callback = function() {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference Number', 'From Office', 'Subject', 'Date']);
            fputcsv($handle, ['DOC-2026-001', 'Engineering Division', 'Bridge Inspection Report', '2026-10-07']);
            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    // Download a document's PDF with its original file name
    public function download(Document $document)
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');

        abort_unless($document->filePath && $disk->exists($document->filePath), 404, 'File not found.');

        return $disk->download($document->filePath, $document->displayFileName());
    }
}
