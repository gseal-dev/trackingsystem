<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $role = $user->role ? $user->role->roleName : null;

        switch ($role) {
            case 'Admin':
                $users = \App\Models\User::with('role')
                    ->get();
                $roles = \App\Models\Role::whereIn('roleName', ['Admin', 'Staff'])
                    ->orderBy('roleName')
                    ->get();
                return view('admin.admin', compact('users', 'roles'));
            case 'DocumentOwner':
                return view('documentOwner.document_owner');
            case 'Staff':
                $documents = \App\Models\Document::with('department')
                    ->when($user->departmentID, function($q) use ($user) {
                        return $q->where('currentDepartmentID', $user->departmentID);
                    })
                    ->get();
                $departments = \App\Models\Department::all();
                return view('staff.staff', compact('documents', 'departments'));
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