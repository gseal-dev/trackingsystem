<?php
namespace App\Http\Controllers\DocumentOwner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Document;

class DocumentOwnerController extends Controller
{
    public function submittedDocuments()
    {
        $user = Auth::user();
        $documents = Document::where('ownerID', $user->userID)->get();

        return view('documentOwner.submitted_documents', compact('documents'));
    }

    public function pendingDocuments()
    {
        $user = Auth::user();
        $documents = Document::where('ownerID', $user->userID)
            ->where('currentStatus', 1)
            ->get();

        return view('documentOwner.pending_documents', compact('documents'));
    }
    
    public function completedDocuments()
    {
        $user = Auth::user();
        $documents = Document::where('ownerID', $user->userID)
            ->where('currentStatus', '!=', 1) // Exclude "Pending" status
            ->get();

        return view('documentOwner.completed_documents', compact('documents'));
    }
}