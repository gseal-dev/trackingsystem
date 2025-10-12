<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $admins = User::whereHas('role', fn($q) => $q->where('roleName', 'Admin'))->get();
        $owners = User::whereHas('role', fn($q) => $q->where('roleName', 'DocumentOwner'))->get();
        $staffs = User::whereHas('role', fn($q) => $q->where('roleName', 'Staff'))->get();
        $auditors = User::whereHas('role', fn($q) => $q->where('roleName', 'Auditor'))->get();

        // FIXED: Use correct case for folder name
        return view('admin.UserManagement.userManagement', compact('admins', 'owners', 'staffs', 'auditors'));
    }

    public function addForm()
    {
        return view('admin.UserManagement.addUser');
    }

    public function editForm(User $user)
    {
        return view('admin.UserManagement.editUser', compact('user'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'roleID' => 'required|exists:roles,roleID',
            'departmentID' => 'required|integer',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'firstName' => $request->firstName,
            'middleName' => $request->middleName,
            'lastName' => $request->lastName,
            'roleID' => $request->roleID,
            'departmentID' => $request->departmentID,
            'password' => \Hash::make($request->password),
            'phoneNo' => $request->phoneNo ?? null,
        ]);

        return redirect()->route('admin.userManagement')->with('success', 'User added!');
    }

    public function edit(Request $request, User $user)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->userID . ',userID',
            'email' => 'required|email|unique:users,email,' . $user->userID . ',userID',
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'roleID' => 'required|exists:roles,roleID',
            'departmentID' => 'required|integer',
            'phoneNo' => 'nullable|string|max:255',
        ]);

        $user->update($request->only([
            'username', 'email', 'firstName', 'middleName', 'lastName', 'roleID', 'departmentID', 'phoneNo'
        ]));

        if ($request->filled('password')) {
            $user->update(['password' => \Hash::make($request->password)]);
        }

        return redirect()->route('admin.userManagement')->with('success', 'User updated!');
    }

    public function delete(User $user)
    {
        $user->delete();
        return redirect()->route('admin.userManagement')->with('success', 'User deleted!');
    }

    public function searchUser(Request $request)
    {
        $query = $request->get('q', '');
        $users = \App\Models\User::where('username', 'LIKE', '%' . $query . '%')->get();

        return response()->json($users->map(function($user) {
            return [
                'userID' => $user->userID,
                'username' => $user->username,
            ];
        }));
    }
}