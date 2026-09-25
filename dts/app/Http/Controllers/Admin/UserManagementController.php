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
    public function admins(Request $request) {
        $users = User::whereHas('role', fn($q) => $q->where('roleName', 'Admin'))->get();
        $search = trim($request->query('search', ''));
        $users = User::whereHas('role', fn($q) => $q->where('roleName', 'Admin'))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('username', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('firstName', 'like', "%{$search}%")
                        ->orWhere('lastName', 'like', "%{$search}%");
                });
            })
            ->with('role')
            ->get();
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
        abort_unless($type === 'admins', 404);

        $departments = Department::all();
        $roles = Role::whereIn('roleName', ['Admin', 'Staff'])->orderBy('roleName')->get();
        return view('admin.UserManagement.addUser', compact('type', 'departments', 'roles'));
    }

    public function add(Request $request, $type) {
        abort_unless($type === 'admins', 404);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|ends_with:@gmail.com|unique:users,email',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'roleID' => 'nullable|exists:roles,roleID',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $roleID = $request->input('roleID') ?: Role::where('roleName', 'Admin')->value('roleID');
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'roleID' => $roleID,
            'password' => \Hash::make($request->password),
        ]);
        return redirect()->route($type === 'admins' ? 'dashboard' : "admin.userManagement.$type")
            ->with('success', 'User added!');
    }

    public function editForm($type, User $user) {
        $roles = Role::whereNotIn('roleName', ['Auditor', 'DocumentOwner'])
            ->orderBy('roleName')
            ->get();
        return view('admin.UserManagement.editUser', compact('user', 'type', 'roles'));
    }

    public function edit(Request $request, $type, User $user) {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username,' . $user->userID . ',userID',
            'email' => 'required|email|ends_with:@gmail.com|unique:users,email,' . $user->userID . ',userID',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'roleID' => 'required|exists:roles,roleID',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user->update($request->only([
            'username', 'email', 'firstName', 'lastName', 'roleID'
        ]));
        if ($request->filled('password')) {
            $user->update(['password' => \Hash::make($request->password)]);
        }
        $redirectType = $type === 'documentowners' ? 'dashboard' : $type;
        return redirect()->route($redirectType === 'admins' || $redirectType === 'dashboard' ? 'dashboard' : "admin.userManagement.$redirectType")
            ->with('success', 'User updated!');
    }

    public function delete($type, User $user) {
        $user->delete();
        $redirectType = $type === 'documentowners' ? 'dashboard' : $type;
        return redirect()->route($redirectType === 'admins' || $redirectType === 'staffs' || $redirectType === 'dashboard' ? 'dashboard' : "admin.userManagement.$redirectType")
            ->with('success', 'User deleted!');
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