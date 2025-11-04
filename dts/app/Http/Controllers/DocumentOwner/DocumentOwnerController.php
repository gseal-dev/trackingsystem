<?php
namespace App\Http\Controllers\DocumentOwner;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Document;

class DocumentOwnerController extends Controller
{
    public function submittedDocuments()
    {
        $user = Auth::user();
        // Only show "In Review" documents (statusID = 4)
        $documents = \App\Models\Document::where('ownerID', $user->userID)
            ->where('currentStatus', 4)
            ->with(['department', 'status'])
            ->get();

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
    
    public function completedDocuments(Request $request)
    {
        $user = Auth::user();
        $search = $request->input('search');

        $documentsQuery = \App\Models\Document::where('ownerID', $user->userID)
            ->whereIn('currentStatus', [2, 3]); // 2 = Approved, 3 = Rejected

        if ($search) {
            $documentsQuery->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                ->orWhere('documentNo', 'LIKE', "%$search%");
            });
        }

        $documents = $documentsQuery->get();

        return view('documentOwner.completed_documents', compact('documents'));
    }
}