<?php

namespace App\Modules\Authentication\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Authentication\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('role')->get();
        return view('Admin.user_management', compact('users'));
    }

    public function create()
    {
        $roles = \DB::table('roles')->whereIn('roleID', [1, 3, 4])->get();
        $departments = \DB::table('departments')->get();
        return view('Admin.create_user', compact('roles', 'departments'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'roleID' => 'required|in:1,3,4',
        ]);
        $departmentId = $request->departmentID ?? 1;
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'roleID' => $request->roleID,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'departmentID' => $departmentId,
            // Add other fields as needed
        ]);

        return redirect()->route('admin.users.index')->with('success', 'User created!');
    }

    public function searchDocumentOwners(Request $request)
    {
        $query = $request->input('q');
        $owners = \App\Modules\Authentication\Models\User::where('roleID', 2)
            ->whereRaw('LOWER(username) LIKE ?', ['%' . strtolower($query) . '%'])
            ->select('userID', 'username', 'email')
            ->limit(10)
            ->get();

        return response()->json($owners);
    }
}