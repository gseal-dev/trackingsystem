<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Department;
use Illuminate\Http\Request;

/**
 * Account settings for staff. Staff can only change their own password here;
 * everything else about the account is read-only and managed by an admin.
 */
class StaffSettingsController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        $fullName = trim(implode(' ', array_filter([$user->firstName, $user->middleName, $user->lastName])));

        return view('staff.settings', [
            'account' => [
                'Username'   => $user->username,
                'Full Name'  => $fullName,
                'Department' => $user->departmentID
                    ? Department::where('depID', $user->departmentID)->value('depName')
                    : null,
                'Role'       => $user->role?->roleName,
                'Phone'      => $user->phoneNo,
            ],
        ]);
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.different'                => 'The new password must be different from your current password.',
        ]);

        // The User model hashes the password automatically ('hashed' cast).
        $user = $request->user();
        $wasForced = (bool) $user->must_change_password;
        $user->update([
            'password' => $validated['password'],
            'must_change_password' => false,
        ]);

        // If they were forced to change it at first login, send them to the
        // dashboard; otherwise return to the settings page.
        if ($wasForced) {
            return redirect()->route('dashboard')->with('success', 'Your password has been updated.');
        }

        return redirect()->route('staff.settings')->with('success', 'Your password has been updated.');
    }
}
