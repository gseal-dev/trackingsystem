<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Staff\StaffDocumentController;

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

                // Full document list shown under the stats (admins see every document)
                $documentsQuery = \App\Models\Document::with(['department', 'owner'])
                    ->orderByDesc('documentDate')
                    ->orderByDesc('created_at');

                foreach (['year' => 'whereYear', 'month' => 'whereMonth', 'day' => 'whereDay'] as $field => $method) {
                    if ($request->filled($field)) {
                        $value = (int) $request->input($field);
                        $documentsQuery->where(function ($q) use ($method, $value) {
                            $q->$method('documentDate', $value)
                              ->orWhere(function ($sub) use ($method, $value) {
                                  $sub->whereNull('documentDate')->$method('created_at', $value);
                              });
                        });
                    }
                }

                $documents = $documentsQuery->get();

                return view('admin.dashboard', compact('totalDocuments', 'totalUsers', 'documentsByDept', 'usersByRole', 'documents'));
            case 'Staff':
                return app(StaffDocumentController::class)->index(request());
            default:
                abort(403, 'Unauthorized or role not set.');
        }
    }
}
