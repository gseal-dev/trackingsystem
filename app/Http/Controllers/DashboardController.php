<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Staff\StaffDocumentController;
use App\Support\DocumentList;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $role = $user->role ? $user->role->roleName : null;

        switch ($role) {
            case 'Admin':
                $users = \App\Models\User::with('role')->get();
                $totalDocuments = \App\Models\Document::count();
                $totalUsers = $users->count();
                // Documents per department (one aggregate query for the chart)
                $documentsByDept = \App\Models\Document::query()
                    ->leftJoin('departments', 'departments.depID', '=', 'documents.currentDepartmentID')
                    ->selectRaw("COALESCE(departments.depName, 'Unassigned') as dep, COUNT(*) as total")
                    ->groupBy('dep')
                    ->pluck('total', 'dep');
                $usersByRole = $users->groupBy(function($u) {
                    return $u->role->roleName ?? 'Unassigned';
                })->map->count();

                return view('admin.dashboard', compact('totalDocuments', 'totalUsers', 'documentsByDept', 'usersByRole'));
            case 'Staff':
                return app(StaffDocumentController::class)->index(request());
            default:
                abort(403, 'Unauthorized or role not set.');
        }
    }

    /** Every document (admins see all departments), filtered and searched, newest first. */
    private function adminDocumentsQuery(Request $request)
    {
        return \App\Models\Document::with(['department', 'owner'])
            ->dateFilter($request->input('year'), $request->input('month'), $request->input('day'))
            ->search($request->input('q'), true)
            ->orderByDesc('documentDate')
            ->orderByDesc('created_at')
            ->orderByDesc('documentId');
    }

    /** Admin documents page: every document, filtered and searched, newest first. */
    public function documents(Request $request)
    {
        $query = $this->adminDocumentsQuery($request)
            ->dateRange($request->input('date_from'), $request->input('date_to'));

        if ($request->filled('department')) {
            $query->where('currentDepartmentID', $request->input('department'));
        }

        $documents = $query
            ->paginate(DocumentList::perPage($request))
            ->withQueryString();

        // e.g. the last page emptied out after deletions: go to the new last page
        if ($documents->isEmpty() && $documents->currentPage() > 1) {
            return redirect()->to($documents->url($documents->lastPage()));
        }

        $departments = \App\Models\Department::orderBy('depName')->get();

        return view('admin.documents', compact('documents', 'departments'));
    }

    /** CSV of every document matching the current filters (not only the page on screen). */
    public function exportDocuments(Request $request)
    {
        return DocumentList::csv(
            $this->adminDocumentsQuery($request),
            ['Reference Number', 'Document Title', 'From Office', 'Subject', 'Uploaded By', 'Date'],
            function ($d) {
                $uploader = $d->owner
                    ? ($d->owner->username ?: trim(($d->owner->firstName ?? '') . ' ' . ($d->owner->lastName ?? '')))
                    : 'Unknown';

                return [
                    $d->documentNo,
                    $d->documentTitle,
                    $d->department->depName ?? '',
                    $d->title,
                    $uploader,
                    $d->documentDate ? \Carbon\Carbon::parse($d->documentDate)->format('F d, Y') : $d->created_at?->format('F d, Y'),
                ];
            }
        );
    }
}
