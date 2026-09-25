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
    public function index()
    {
        $user = auth()->user();
        $documents = \App\Models\Document::with('department')
            ->when($user->departmentID, function($q) use ($user) {
                return $q->where('currentDepartmentID', $user->departmentID);
            })
            ->get();
        $departments = \App\Models\Department::all();
        return view('staff.staff', compact('documents', 'departments'));
    }

    // Staff processes document (reupload and update status)
    public function processAndRouteDocument(Request $request, Document $document)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf|max:10240',
            'statusID' => 'required|exists:document_statuses,statusID',
            'departmentID' => 'required|exists:departments,depID',
        ]);

        // Delete old file if exists
        if ($document->filePath && \Storage::disk('public')->exists($document->filePath)) {
            \Storage::disk('public')->delete($document->filePath);
        }

        // Upload new file
        $file = $request->file('file');
        $fileName = \Illuminate\Support\Str::uuid() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        $prevDepartment = $document->currentDepartmentID;
        $document->filePath = $filePath;
        $document->currentStatus = $request->statusID;
        $document->currentDepartmentID = $request->departmentID;
        $document->save();

        $owner = $document->owner;
        if ($owner && $owner->email) {
            $owner->notify(new \App\Notifications\DocumentProcessedNotification($document));
        }

        \DB::table('document_histories')->insert([
            'documentId' => $document->documentId,
            'prevDepartmentID' => $prevDepartment,
            'currentDepartmentID' => $document->currentDepartmentID,
            'statusID' => $request->statusID,
            'userID' => auth()->user()->userID,
            'action' => 'Processed, status updated, and routed',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Document processed, updated, and sent!');
    }

    // Staff routes document to another department or admin
    public function routeDocument(Request $request, Document $document)
    {
        $request->validate([
            'departmentID' => 'required|exists:departments,depID',
        ]);

        $prevDepartment = $document->currentDepartmentID;
        $document->currentDepartmentID = $request->departmentID;
        $document->currentStatus = 4; // In Review
        $document->save();

        \DB::table('document_histories')->insert([
            'documentId' => $document->documentId,
            'prevDepartmentID' => $prevDepartment,
            'currentDepartmentID' => $document->currentDepartmentID, // <-- should be updated value
            'statusID' => 4,
            'userID' => auth()->user()->userID,
            'action' => 'Routed to another department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Document routed!');
    }

    public function history(Request $request)
    {
        $user = auth()->user();
        $search = $request->input('search');

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

        $histories = $historiesQuery->orderByDesc('created_at')->get();

        return view('staff.history', compact('histories'));
    }

    public function processDocumentForm(Document $document)
    {
        $departments = \App\Models\Department::all();
        return view('staff.process_document', compact('document', 'departments'));
    }

    public function create()
    {
        $departments = \App\Models\Department::all();
        return view('staff.create_document', compact('departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'documentNo' => 'required|unique:documents,documentNo',
            'title' => 'required|string|max:255',
            'documentType' => 'required|string|max:255',
            'departmentID' => 'required|exists:departments,depID',
            'documentDate' => 'nullable|date',
            'file' => 'required|file|mimes:pdf|max:10240',
        ]);

        $file = $request->file('file');
        $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        Document::create([
            'documentId' => (string) Str::uuid(),
            'documentNo' => $request->documentNo,
            'title' => $request->title,
            'description' => $request->title,
            'documentType' => $request->documentType,
            'documentDate' => $request->documentDate ?? now()->toDateString(),
            'ownerID' => auth()->user()->userID,
            'currentStatus' => 1, // Pending
            'currentDepartmentID' => $request->departmentID,
            'filePath' => $filePath,
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
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'documentType' => 'required|string|max:255',
        ]);

        $document->update($request->only(['title', 'description', 'documentType']));

        return redirect()->route('dashboard')->with('success', 'Document updated successfully!');
    }

    public function delete(Document $document)
    {
        // Delete file if exists
        if ($document->filePath && \Storage::disk('public')->exists($document->filePath)) {
            \Storage::disk('public')->delete($document->filePath);
        }

        $document->delete();

        return back()->with('success', 'Document deleted successfully.');
    }
}