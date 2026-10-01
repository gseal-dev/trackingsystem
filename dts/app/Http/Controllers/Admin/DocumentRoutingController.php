<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Department;

class DocumentRoutingController extends Controller
{
    // List all documents for admin documents page
    public function index(Request $request)
    {
        $query = Document::with(['department', 'owner', 'status']);

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
        return view('admin.documents', compact('documents'));
    }

    // List all documents for sending
    public function listDocuments()
    {
        $documents = \App\Models\Document::where('currentDepartmentID', 1)
            ->where('currentStatus', 1) // 1 = Pending
            ->get();
        return view('admin.DocumentRouting.listDocuments', compact('documents'));
    }

    // Show send form for a specific document
    public function showSendForm(Document $document)
    {
        $departments = Department::where('depID', '!=', 1)->get();
        return view('admin.DocumentRouting.sendDocu', compact('document', 'departments'));
    }

    // Send document to selected department
    public function send(Request $request, Document $document)
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
            'currentDepartmentID' => $request->departmentID,
            'statusID' => 4,
            'userID' => auth()->user()->userID,
            'action' => 'Sent to department',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('admin.sendDocumentList')->with('success', 'Document sent!');
    }

    public function processedDocuments(Request $request)
    {
        $search = $request->input('search');

        $documentsQuery = \App\Models\Document::where('currentDepartmentID', 1)
            ->whereHas('histories', function($q) {
                $q->where('prevDepartmentID', '!=', 1);
            })
            ->with(['owner', 'status', 'department']);

        if ($search) {
            $documentsQuery->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                ->orWhere('documentNo', 'LIKE', "%$search%")
                ->orWhereHas('owner', function ($oq) use ($search) {
                    $oq->where('username', 'LIKE', "%$search%")
                        ->orWhere('firstName', 'LIKE', "%$search%")
                        ->orWhere('lastName', 'LIKE', "%$search%");
                });
            });
        }

        $documents = $documentsQuery->paginate(10);

        return view('admin.DocumentRouting.processedDocument', compact('documents'));
    }
}