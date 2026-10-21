<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Support\CsvExport;
use App\Support\Paging;

class UserManagementController extends Controller
{
    public function index() {
        return view('admin.UserManagement.userManagement');
    }
    /** Every user, searched, in the order they were created. */
    private function usersQuery(Request $request) {
        return User::with('role')->search($request->input('q'))->orderBy('userID');
    }

    public function admins(Request $request) {
        $users = $this->usersQuery($request)->paginate(Paging::perPage($request))->withQueryString();

        // e.g. the last page emptied out after deletions: go to the new last page
        if ($users->isEmpty() && $users->currentPage() > 1) {
            return redirect()->to($users->url($users->lastPage()));
        }

        $roles = Role::whereIn('roleName', ['Admin', 'Staff'])->orderBy('roleName')->get();
        return view('admin.UserManagement.admins', compact('users', 'roles'));
    }

    /** CSV of every user matching the search (not only the page on screen). */
    public function exportUsers(Request $request) {
        return CsvExport::stream(
            $this->usersQuery($request),
            ['Name', 'Email', 'Role'],
            fn ($u) => [trim($u->firstName . ' ' . $u->lastName), $u->email, $u->role->roleName ?? 'Admin'],
            'admin-users.csv'
        );
    }

    public function staffs(Request $request) {
        $users = User::whereHas('role', fn($q) => $q->where('roleName', 'Staff'))
            ->orderBy('userID')
            ->paginate(Paging::perPage($request))
            ->withQueryString();

        if ($users->isEmpty() && $users->currentPage() > 1) {
            return redirect()->to($users->url($users->lastPage()));
        }

        return view('admin.UserManagement.staffs', compact('users'));
    }

    public function add(Request $request, $type) {
        abort_unless($type === 'admins', 404);

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'roleID' => 'required|exists:roles,roleID',
            'password' => 'required|string|min:8|confirmed',
        ]);
        $roleID = $request->input('roleID');
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
        $roles = Role::whereIn('roleName', ['Admin', 'Staff'])
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
        $redirectType = $type;
        return redirect()->route($redirectType === 'admins' || $redirectType === 'dashboard' ? 'dashboard' : "admin.userManagement.$redirectType")
            ->with('success', 'User updated!');
    }

    public function delete($type, User $user) {
        $user->delete();
        $redirectType = $type;
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