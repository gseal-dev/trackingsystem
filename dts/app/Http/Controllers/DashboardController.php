<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Staff\StaffDocumentController;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role ? $user->role->roleName : null;

        switch ($role) {
            case 'Admin':
                $users = \App\Models\User::with('role')->get();
                $totalDocuments = \App\Models\Document::count();
                $totalUsers = $users->count();
                $documentsByStatus = \App\Models\Document::with('status')
                    ->get()
                    ->groupBy(function($doc) {
                        return $doc->status->statusName ?? 'Unknown';
                    })->map->count();
                $documentsByDept = \App\Models\Document::with('department')
                    ->get()
                    ->groupBy(function($doc) {
                        return $doc->department->depName ?? 'Unassigned';
                    })->map->count();
                $usersByRole = $users->groupBy(function($u) {
                    return $u->role->roleName ?? 'Unassigned';
                })->map->count();

                return view('admin.dashboard', compact('totalDocuments', 'totalUsers', 'documentsByStatus', 'documentsByDept', 'usersByRole'));
            case 'Staff':
                return app(StaffDocumentController::class)->index(request());
            default:
                abort(403, 'Unauthorized or role not set.');
        }
    }
}
