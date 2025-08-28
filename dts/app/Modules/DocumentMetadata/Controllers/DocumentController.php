<?php

namespace App\Modules\DocumentMetadata\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\DocumentMetadata\Requests\StoreDocumentRequest;
use App\Modules\DocumentMetadata\Services\DocumentService;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
        // Remove the middleware call from here
    }

    public function create()
    {
        // Check authentication manually
        if (!Session::has('user_data')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $userData = Session::get('user_data');
        return view('documentreg', compact('userData'));
    }

    public function store(StoreDocumentRequest $request)
    {
        // Check authentication manually
        if (!Session::has('user_data')) {
            return redirect()->route('login')->with('error', 'Please login first');
        }

        $userData = Session::get('user_data');

        $result = $this->documentService->store(
            $request->validated(), 
            $request->file('file'), 
            $userData['userID']
        );
        
        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }
}