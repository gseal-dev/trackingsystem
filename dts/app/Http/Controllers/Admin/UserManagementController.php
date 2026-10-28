<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\Setting;
use App\Models\User;
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
        $search = $request->input('q');

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

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $historiesQuery->whereHas('document', function ($q) use ($request) {
                $q->dateRange($request->input('date_from'), $request->input('date_to'));
            });
        }

        if ($request->filled('department')) {
            $historiesQuery->whereHas('document', function ($q) use ($request) {
                $q->where('currentDepartmentID', $request->input('department'));
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

        $departments = Department::all();

        return view('admin.UserManagement.history', compact('histories', 'departments'));
    }


    /** CSV export of admin history matching the current filters. */
    public function exportHistory(Request $request)
    {
        $search = $request->input('q');

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

        if ($request->filled('date_from') || $request->filled('date_to')) {
            $historiesQuery->whereHas('document', function ($q) use ($request) {
                $q->dateRange($request->input('date_from'), $request->input('date_to'));
            });
        }

        if ($request->filled('department')) {
            $historiesQuery->whereHas('document', function ($q) use ($request) {
                $q->where('currentDepartmentID', $request->input('department'));
            });
        }

        return CsvExport::stream(
            $historiesQuery->orderByDesc('created_at')->orderByDesc('historyID'),
            ['Reference Number', 'From Office', 'Subject', 'Action', 'User', 'Timestamp'],
            fn ($h) => [
                $h->document?->documentNo ?? '-',
                $h->document?->department?->depName ?? '-',
                $h->document?->title ?? '-',
                $h->action ?? '-',
                self::formatHistoryUserName($h->user ?? null),
                $h->created_at
                    ->timezone(\App\Models\Document::DISPLAY_TIMEZONE)
                    ->format('F d, Y \\a\\t h:i A'),
            ],
            'admin-document-history.csv'
        );
    }


    /** CSV of every user matching the search (not only the page on screen). */
    public function exportUsers(Request $request) {
        return CsvExport::stream(
            $this->usersQuery($request),
            ['Name', 'Role'],
            fn ($u) => [trim($u->firstName . ' ' . $u->lastName), $u->role->roleName ?? 'Admin'],
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
        // When a default/admin-assigned password is used, force the user to
        // change it at first login so the plain-text default never persists.
        // Only Staff users can reach the password-change page (staff.settings),
        // so only they are forced to change it.
        $isStaffRole = \App\Models\Role::where('roleID', $roleID)->value('roleName') === 'Staff';
        $mustChange = $isStaffRole && !$request->filled('password');

        User::create([
            'username' => $request->username,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'roleID' => $roleID,
            'password' => \Hash::make($password),
            'must_change_password' => $mustChange,
        ]);

        $isStaff = \App\Models\Role::where('roleID', $roleID)->value('roleName') === 'Staff';

        return redirect()->route($type === 'admins' ? 'dashboard' : "admin.userManagement.$type")
            ->with('success', $isStaff
                ? 'User added! Give them the password - they must change it at first login.'
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
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'roleID' => 'required|exists:roles,roleID',
            'password' => 'nullable|string|min:8|confirmed',
        ]);
        $user->update($request->only([
            'username', 'firstName', 'lastName', 'roleID'
        ]));
        if ($request->filled('password')) {
            $user->update(['password' => \Hash::make($request->password)]);
        }
        $redirectType = $type;
        return redirect()->route($redirectType === 'admins' || $redirectType === 'dashboard' ? 'dashboard' : "admin.userManagement.$redirectType")
            ->with('success', 'User updated!');
    }

    /** Delete a user account. */
    public function delete($type, User $user) {
        if ($user->getKey() === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($this->isLastActiveAdmin($user)) {
            return back()->with('error', 'At least one administrator must remain active. The account was not deleted.');
        }

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

    /** Lock a user account so they cannot log in. Requires the admin's own password. */
    public function lock(Request $request, $type, User $user) {
        $request->validate([
            'confirm_password' => 'required|string',
        ]);

        if (! \Hash::check($request->input('confirm_password'), auth()->user()->password)) {
            return back()->with('error', 'Incorrect password. The account was not locked.');
        }

        if ($user->getKey() === auth()->id()) {
            return back()->with('error', 'You cannot lock your own account.');
        }

        if ($this->isLastActiveAdmin($user)) {
            return back()->with('error', 'At least one administrator must remain active. The account was not locked.');
        }

        $user->update(['is_locked' => true, 'locked_at' => now()]);
        return back()->with('success', $user->firstName . ' ' . $user->lastName . ' has been locked.');
    }

    /** Unlock a user account so they can log in again. */
    public function unlock(Request $request, $type, User $user) {
        $user->update(['is_locked' => false, 'locked_at' => null]);
        return back()->with('success', $user->firstName . ' ' . $user->lastName . ' has been unlocked.');
    }

    /** Whether removing/locking this admin would leave zero active administrators. */
    private function isLastActiveAdmin(User $user): bool {
        if ($user->role?->roleName !== 'Admin') {
            return false;
        }

        return User::where('roleID', $user->roleID)
            ->where('is_locked', false)
            ->count() <= 1;
    }

    /** Reset a user's password. Falls back to the default password when none is typed. */
    public function resetPassword(Request $request, $type, User $user) {
        $defaultPassword = Setting::value('default_password');
        $passwordRule = $defaultPassword ? 'nullable|string|min:8|max:72' : 'required|string|min:8|max:72';

        $request->validate([
            'password' => $passwordRule,
        ]);

        $password = $request->input('password');
        $usingDefault = !$password;
        if ($usingDefault) {
            if (!$defaultPassword) {
                return back()->withErrors(['password' => 'No default password is configured. Please type a new password.']);
            }
            $password = $defaultPassword;
        }

        // Only Staff users can reach the password-change page (staff.settings),
        // so only they are forced to change a default password at next login.
        $isStaffRole = $user->role?->roleName === 'Staff';
        $mustChange = $isStaffRole && $usingDefault;

        $user->update([
            'password' => \Hash::make($password),
            'must_change_password' => $mustChange,
        ]);

        return back()->with('success', 'Password reset for ' . $user->firstName . ' ' . $user->lastName . '.');
    }

    /** Format a history record's user for CSV export. */
    private static function formatHistoryUserName(?User $user): string
    {
        if (! $user) {
            return 'Unknown';
        }
        $firstName = trim($user->firstName ?? '');
        $lastName = trim($user->lastName ?? '');
        $name = trim($firstName . ' ' . $lastName);

        return $name ?: ($user->username ?? 'Unknown');
    }
}