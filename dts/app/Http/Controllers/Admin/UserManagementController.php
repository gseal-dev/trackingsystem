<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index() {
        return view('admin.UserManagement.userManagement');
    }
    public function admins() {
        $users = User::whereHas('role', fn($q) => $q->where('roleName', 'Admin'))->get();
        return view('admin.UserManagement.admins', compact('users'));
    }
    public function owners() {
        $users = User::whereHas('role', fn($q) => $q->where('roleName', 'DocumentOwner'))->get();
        return view('admin.UserManagement.owners', compact('users'));
    }
    public function staffs() {
        $users = User::whereHas('role', fn($q) => $q->where('roleName', 'Staff'))->get();
        return view('admin.UserManagement.staffs', compact('users'));
    }
    public function auditors() {
        $users = User::whereHas('role', fn($q) => $q->where('roleName', 'Auditor'))->get();
        return view('admin.UserManagement.auditors', compact('users'));
    }

    public function addForm($type) {
        $departments = Department::all();
        return view('admin.UserManagement.addUser', compact('type', 'departments'));
    }

    public function add(Request $request, $type) {
        // Map plural type to singular roleName
        $roleMap = [
            'admins' => 'Admin',
            'owners' => 'DocumentOwner',
            'staffs' => 'Staff',
            'auditors' => 'Auditor',
        ];
        $roleName = $roleMap[$type] ?? ucfirst($type);
        $roleID = Role::where('roleName', $roleName)->value('roleID');

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'departmentID' => 'required|integer',
            'password' => 'required|string|min:8|confirmed',
        ]);
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'firstName' => $request->firstName,
            'middleName' => $request->middleName,
            'lastName' => $request->lastName,
            'roleID' => $roleID,
            'departmentID' => $request->departmentID,
            'password' => \Hash::make($request->password),
            'phoneNo' => $request->phoneNo ?? null,
        ]);
        return redirect()->route("admin.userManagement.$type")->with('success', 'User added!');
    }

    public function editForm($type, User $user) {
        $departments = Department::all();
        return view('admin.UserManagement.editUser', compact('user', 'type', 'departments'));
    }

    public function edit(Request $request, $type, User $user) {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->userID . ',userID',
            'email' => 'required|email|unique:users,email,' . $user->userID . ',userID',
            'firstName' => 'required|string|max:255',
            'middleName' => 'nullable|string|max:255',
            'lastName' => 'required|string|max:255',
            'departmentID' => 'required|integer',
            'phoneNo' => 'nullable|string|max:255',
        ]);
        $user->update($request->only([
            'username', 'email', 'firstName', 'middleName', 'lastName', 'departmentID', 'phoneNo'
        ]));
        if ($request->filled('password')) {
            $user->update(['password' => \Hash::make($request->password)]);
        }
        return redirect()->route("admin.userManagement.$type")->with('success', 'User updated!');
    }

    public function delete($type, User $user) {
        $user->delete();
        return redirect()->route("admin.userManagement.$type")->with('success', 'User deleted!');
    }

    public function searchUser(Request $request)
    {
        $query = $request->get('q', '');
        $users = User::where('username', 'LIKE', '%' . $query . '%')->get();

        return response()->json($users->map(function($user) {
            return [
                'userID' => $user->userID,
                'username' => $user->username,
            ];
        }));
    }
}