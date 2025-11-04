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
                return view('admin.admin');
            case 'DocumentOwner':
                return view('documentOwner.document_owner');
            case 'Staff':
                $documents = \App\Models\Document::where('currentDepartmentID', $user->departmentID)->get();
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