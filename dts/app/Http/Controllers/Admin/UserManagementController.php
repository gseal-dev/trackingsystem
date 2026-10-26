<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Support\CsvExport;
use App\Support\DocumentList;
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
        $defaultPassword = Setting::value('default_password');
        return view('admin.UserManagement.admins', compact('users', 'roles', 'defaultPassword'));
    }

    /** Admin sees every document transaction history (not department-scoped). */
    public function adminHistory(Request $request)
    {
        $search = $request->input('search');

        $historiesQuery = \App\Models\DocumentHistory::with(['document.owner', 'document.department', 'user']);

        if ($search) {
            $historiesQuery->whereHas('document', function ($q) use ($search) {
                $q->where('title', 'LIKE', "%$search%")
                    ->orWhere('documentTitle', 'LIKE', "%$search%")
                    ->orWhere('documentNo', 'LIKE', "%$search%")
                    ->orWhereHas('owner', function ($oq) use ($search) {
                        $oq->where('username', 'LIKE', "%$search%")
                            ->orWhere('firstName', 'LIKE', "%$search%")
                            ->orWhere('lastName', 'LIKE', "%$search%");
                    });
            });
        }

        if ($request->filled('year') || $request->filled('month') || $request->filled('day')) {
            $historiesQuery->whereHas('document', function ($q) use ($request) {
                $q->dateFilter($request->input('year'), $request->input('month'), $request->input('day'));
            });
        }

        $histories = $historiesQuery
            ->orderByDesc('created_at')
            ->orderByDesc('historyID')
            ->paginate(DocumentList::perPage($request))
            ->withQueryString();

        // e.g. the last page emptied out: go to the new last page
        if ($histories->isEmpty() && $histories->currentPage() > 1) {
            return redirect()->to($histories->url($histories->lastPage()));
        }

        return view('admin.UserManagement.history', compact('histories'));
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

    public function add(Request $request, $type) {
        abort_unless($type === 'admins', 404);

        // If no default password is configured yet, require the admin to type one.
        $defaultPassword = Setting::value('default_password');
        $passwordRule = $defaultPassword ? 'nullable|string|min:8|max:72' : 'required|string|min:8|max:72';

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'roleID' => 'required|exists:roles,roleID',
            'password' => $passwordRule,
        ]);

        $password = $request->input('password');
        if (!$password) {
            $password = $defaultPassword;
        }

        $roleID = $request->input('roleID');
        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'roleID' => $roleID,
            'password' => \Hash::make($password),
        ]);

        $isStaff = \App\Models\Role::where('roleID', $roleID)->value('roleName') === 'Staff';

        return redirect()->route($type === 'admins' ? 'dashboard' : "admin.userManagement.$type")
            ->with('success', $isStaff
                ? 'User added! Give them the password - they can change it in Settings after logging in.'
                : 'User added!');
    }

    /** Save the default password applied to newly created users. */
    public function saveDefaultPassword(Request $request)
    {
        $request->validate([
            'default_password' => 'nullable|string|min:8|max:72',
        ]);

        Setting::updateOrCreate(
            ['key' => 'default_password'],
            ['value' => $request->input('default_password')]
        );

        $hasPassword = (bool) $request->input('default_password');

        return redirect()->route('admin.userManagement.admins')
            ->with('success', $hasPassword
                ? 'Default password saved. New users will be created with this password.'
                : 'Default password cleared. You must set a password when adding each user.');
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

    /** Lock a user account so they cannot log in. */
    public function lock(Request $request, $type, User $user) {
        $user->update(['is_locked' => true, 'locked_at' => now()]);
        return back()->with('success', $user->firstName . ' ' . $user->lastName . ' has been locked.');
    }

    /** Unlock a user account so they can log in again. */
    public function unlock(Request $request, $type, User $user) {
        $user->update(['is_locked' => false, 'locked_at' => null]);
        return back()->with('success', $user->firstName . ' ' . $user->lastName . ' has been unlocked.');
    }

    /** Reset a user's password. Falls back to the default password when none is typed. */
    public function resetPassword(Request $request, $type, User $user) {
        $defaultPassword = Setting::value('default_password');
        $passwordRule = $defaultPassword ? 'nullable|string|min:8|max:72' : 'required|string|min:8|max:72';

        $request->validate([
            'password' => $passwordRule,
        ]);

        $password = $request->input('password');
        if (!$password) {
            if (!$defaultPassword) {
                return back()->withErrors(['password' => 'No default password is configured. Please type a new password.']);
            }
            $password = $defaultPassword;
        }

        $user->update(['password' => \Hash::make($password)]);

        return back()->with('success', 'Password reset for ' . $user->firstName . ' ' . $user->lastName . '.');
    }
}