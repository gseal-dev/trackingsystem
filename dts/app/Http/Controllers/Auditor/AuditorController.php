<?php

namespace App\Http\Controllers\Auditor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Document;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AuditorController extends Controller
{
    public function documentTransactions(Request $request)
    {
        $query = Document::with(['owner', 'status', 'department'])
            ->where('currentStatus', '!=', 1); // Exclude "Pending" status

        // Apply search filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                  ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                      $ownerQuery->where('username', 'LIKE', "%$search%");
                  });
            });
        }

        // Paginate the results
        $documents = $query->paginate(10);

        return view('auditor.document_transactions', compact('documents'));
    }

     public function exportDocumentTransactions(Request $request)
    {
        $query = Document::with(['owner', 'status', 'department'])
            ->where('currentStatus', '!=', 1); // Exclude "Pending" status

        // Apply search filters
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                  ->orWhereHas('owner', function ($ownerQuery) use ($search) {
                      $ownerQuery->where('username', 'LIKE', "%$search%");
                  });
            });
        }

        $documents = $query->get();

        $response = new StreamedResponse(function () use ($documents) {
            $handle = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($handle, ['Document No', 'Title', 'Owner', 'Status', 'Department', 'Last Updated']);

            // Add document data
            foreach ($documents as $doc) {
                fputcsv($handle, [
                    $doc->documentNo,
                    $doc->title,
                    $doc->owner->username ?? 'Unknown',
                    $doc->status->statusName ?? 'Unknown',
                    $doc->department->depName ?? 'Unknown',
                    $doc->updated_at ? $doc->updated_at->format('Y-m-d H:i:s') : 'N/A',
                ]);
            }

            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="document_transactions.csv"');

        return $response;
    }
}