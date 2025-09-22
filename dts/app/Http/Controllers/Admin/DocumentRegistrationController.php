<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;

class DocumentRegistrationController extends Controller
{
    public function showForm()
    {
        // You can pass users or departments if needed for selection
        return view('admin.DocumentRegistration.docuReg');
    }

    public function register(Request $request)
    {
        $request->validate([
            'documentNo' => 'required|unique:documents,documentNo',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'documentType' => 'required|string|max:255',
            'ownerID' => 'required|exists:users,userID',
            'file' => 'required|file|mimes:pdf,doc,docx,jpg,png|max:10240', // 10MB max
        ]);

        // Handle file upload
        $file = $request->file('file');
        $fileName = Str::uuid() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('documents', $fileName, 'public');

        // Save metadata
        \DB::table('documents')->insert([
            'documentId' => Str::uuid(),
            'documentNo' => $request->documentNo,
            'title' => $request->title,
            'description' => $request->description,
            'documentType' => $request->documentType,
            'ownerID' => $request->ownerID,
            'currentStatus' => 1, // default to Pending
            'currentDepartmentID' => 1, // default to Admin
            'filePath' => $filePath,
        ]);

        return redirect()->route('admin.documentRegistration')->with('success', 'Document registered successfully!');
    }
}