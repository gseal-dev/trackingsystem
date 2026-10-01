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
                $roles = \App\Models\Role::whereIn('roleName', ['Admin', 'Staff'])->orderBy('roleName')->get();
                return view('admin.UserManagement.admins', compact('users', 'roles'));
            case 'DocumentOwner':
                return view('documentOwner.document_owner');
            case 'Staff':
                return app(StaffDocumentController::class)->index(request());
            case 'Auditor':
                $documents = \App\Models\Document::with(['owner', 'status', 'department'])
                    ->where('currentStatus', '!=', 1) // Exclude "Pending"
                    ->paginate(10);
                $departments = \App\Models\Department::all();
                return view('auditor.auditor', compact('documents', 'departments'));
            default:
                abort(403, 'Unauthorized or role not set.');
        }
    }
}