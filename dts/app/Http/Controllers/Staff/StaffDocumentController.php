<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Document;
use App\Models\Department;
use App\Notifications\DocumentProcessedNotification;

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

        $statusCounts = $documents->groupBy(function($doc) {
            return $doc->status->statusName ?? 'Pending';
        })->map->count();

        $monthlyCounts = $documents->groupBy(function($doc) {
            return $doc->documentDate ? date('Y-m', strtotime($doc->documentDate)) : date('Y-m', strtotime($doc->created_at));
        })->map->count();

        return view('staff.staff', compact('statusCounts', 'monthlyCounts'));
    }

    public function documents(Request $request)
    {
        $user = auth()->user();
        $query = \App\Models\Document::with(['department', 'owner'])
            ->when($user->departmentID, function($q) use ($user) {
                return $q->where('currentDepartmentID', $user->departmentID);
            });

        if ($request->filled('year')) {
            $query->where(function($q) use ($request) {
                $q->whereYear('documentDate', $request->year)
                  ->orWhere(function($sub) use ($request) {
                      $sub->whereNull('documentDate')->whereYear('created_at', $request->year);
                  });
            });
        }
        if ($request->filled('month')) {
            $query->where(function($q) use ($request) {
                $q->whereMonth('documentDate', $request->month)
                  ->orWhere(function($sub) use ($request) {
                      $sub->whereNull('documentDate')->whereMonth('created_at', $request->month);
                  });
            });
        }
        if ($request->filled('day')) {
            $query->where(function($q) use ($request) {
                $q->whereDay('documentDate', $request->day)
                  ->orWhere(function($sub) use ($request) {
                      $sub->whereNull('documentDate')->whereDay('created_at', $request->day);
                  });
            });
        }

        $documents = $query->get();
        $departments = \App\Models\Department::all();

        return view('staff.documents', compact('documents', 'departments'));
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
                    'statusID' => $doc->currentStatus ?? 1,
                    'userID' => $doc->ownerID ?? $user->userID,
                    'action' => 'Document added / registered',
                    'created_at' => $doc->created_at,
                    'updated_at' => $doc->updated_at ?? $doc->created_at,
                ]
            );
        }

        $historiesQuery = \App\Models\DocumentHistory::with(['document.owner', 'document.status', 'document.department'])
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
                ->orWhere('documentNo', 'LIKE', "%$search%")
                ->orWhereHas('owner', function ($oq) use ($search) {
                    $oq->where('username', 'LIKE', "%$search%")
                        ->orWhere('firstName', 'LIKE', "%$search%")
                        ->orWhere('lastName', 'LIKE', "%$search%");
                });
            });
        }

        if ($request->filled('year')) {
            $historiesQuery->whereYear('created_at', $request->year);
        }
        if ($request->filled('month')) {
            $historiesQuery->whereMonth('created_at', $request->month);
        }
        if ($request->filled('day')) {
            $historiesQuery->whereDay('created_at', $request->day);
        }

        $histories = $historiesQuery->orderByDesc('created_at')->get();

        return view('staff.history', compact('histories'));
    }


    public function create()
    {
        return view('staff.create_document');
    }

    public function store(Request $request)
    {
        $request->validate([
            'documentNo' => 'required|unique:documents,documentNo',
            'title' => 'required|string|max:255',
            'documentType' => 'required|string|max:255',
            'fromOffice' => 'required|string|max:255',
            'documentDate' => 'nullable|date',
            'currentStatus' => 'nullable|exists:document_statuses,statusID',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

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

        $statusID = $request->input('currentStatus', 1);
        $newDoc = Document::create([
            'documentId' => (string) Str::uuid(),
            'documentNo' => $request->documentNo,
            'title' => $request->title,
            'description' => $request->title,
            'documentType' => $request->documentType,
            'documentDate' => $request->documentDate ?? now()->toDateString(),
            'ownerID' => auth()->user()->userID,
            'currentStatus' => $statusID,
            'currentDepartmentID' => $department->depID,
            'filePath' => $filePath,
        ]);

        \DB::table('document_histories')->insert([
            'documentId' => $newDoc->documentId,
            'prevDepartmentID' => $department->depID,
            'currentDepartmentID' => $department->depID,
            'statusID' => $statusID,
            'userID' => auth()->user()->userID,
            'action' => 'Document added / registered',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Document added successfully!');
    }

    public function edit(Document $document)
    {
        return view('staff.edit_document', compact('document'));
    }

    public function update(Request $request, Document $document)
    {
        $request->validate([
            'documentNo' => 'required|unique:documents,documentNo,' . $document->documentId . ',documentId',
            'title' => 'required|string|max:255',
            'documentType' => 'required|string|max:255',
            'fromOffice' => 'required|string|max:255',
            'documentDate' => 'nullable|date',
            'currentStatus' => 'nullable|exists:document_statuses,statusID',
            'file' => 'nullable|file|mimes:pdf|max:10240',
        ]);

        $officeName = trim($request->fromOffice);
        $department = Department::where('depName', 'LIKE', $officeName)->first();
        if (!$department) {
            $department = Department::create([
                'depName' => $officeName,
                'description' => $officeName,
            ]);
        }

        $updateData = [
            'documentNo' => $request->documentNo,
            'title' => $request->title,
            'description' => $request->title,
            'documentType' => $request->documentType,
            'documentDate' => $request->documentDate ?? $document->documentDate,
            'currentStatus' => $request->input('currentStatus', $document->currentStatus),
            'currentDepartmentID' => $department->depID,
        ];

        if ($request->hasFile('file')) {
            if ($document->filePath && \Storage::disk('public')->exists($document->filePath)) {
                \Storage::disk('public')->delete($document->filePath);
            }
            $file = $request->file('file');
            $fileName = \Illuminate\Support\Str::uuid() . '_' . $file->getClientOriginalName();
            $updateData['filePath'] = $file->storeAs('documents', $fileName, 'public');
        }

        $document->update($updateData);

        return redirect()->route('dashboard')->with('success', 'Document updated successfully!');
    }

    public function delete(Document $document)
    {
        $documentId = $document->documentId;
        $documentTitle = $document->title;

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
            [$documentNo, $fromOffice, $documentType, $title, $documentDate] = array_pad($row, 5, '');

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
                'documentType' => trim($documentType) ?: 'Memo',
                'documentDate' => !empty($documentDate) ? date('Y-m-d', strtotime($documentDate)) : now()->toDateString(),
                'ownerID' => auth()->user()->userID,
                'currentStatus' => 1,
                'currentDepartmentID' => $department->depID,
            ]);

            \DB::table('document_histories')->insert([
                'documentId' => $newDoc->documentId,
                'prevDepartmentID' => $department->depID,
                'currentDepartmentID' => $department->depID,
                'statusID' => 1,
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
            fputcsv($handle, ['Reference Number', 'From Office', 'Type', 'Subject', 'Date']);
            fputcsv($handle, ['DOC-2026-001', 'Engineering Division', 'Memo', 'Bridge Inspection Report', '2026-10-07']);
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
