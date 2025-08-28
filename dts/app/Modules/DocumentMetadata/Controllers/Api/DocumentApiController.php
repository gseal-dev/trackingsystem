<?php

namespace App\Modules\DocumentMetadata\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Modules\DocumentMetadata\Requests\StoreDocumentRequest;
use App\Modules\DocumentMetadata\Services\DocumentService;
use App\Modules\DocumentMetadata\Resources\DocumentResource;
use App\Modules\DocumentMetadata\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocumentApiController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    public function index()
    {
        $documents = Document::where('ownerID', Auth::user()->userID)->get();
        
        return response()->json([
            'success' => true,
            'documents' => DocumentResource::collection($documents)
        ]);
    }

    public function store(StoreDocumentRequest $request)
    {
        $result = $this->documentService->store(
            $request->validated(), 
            $request->file('file'), 
            Auth::user()->userID
        );
        
        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'document' => new DocumentResource($result['document'])
            ], 201);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], 500);
    }

    public function show($id)
    {
        $document = Document::where('documentId', $id)
                           ->where('ownerID', Auth::user()->userID)
                           ->first();

        if (!$document) {
            return response()->json([
                'success' => false,
                'message' => 'Document not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'document' => new DocumentResource($document)
        ]);
    }
}