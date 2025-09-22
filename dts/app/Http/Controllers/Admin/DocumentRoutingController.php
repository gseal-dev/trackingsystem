<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Department;

class DocumentRoutingController extends Controller
{
    // List all documents for sending
    public function listDocuments()
    {
        $documents = \App\Models\Document::where('currentDepartmentID', 1)->get();
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

    public function processedDocuments()
    {
        // Only show documents currently in Admin that have history where prevDepartmentID != 1
        $documents = \App\Models\Document::where('currentDepartmentID', 1)
            ->whereHas('histories', function($q) {
                $q->where('prevDepartmentID', '!=', 1);
            })
            ->get();

        return view('admin.DocumentRouting.processedDocument', compact('documents'));
    }
}