<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * Single-purpose page: force a staff member to change their admin-assigned
 * password at first login before any other page is reachable.
 */
class FirstPasswordChangeController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user();

        if (! (bool) $user->must_change_password) {
            return redirect()->route('dashboard');
        }

        return view('auth.first-password-change', [
            'username' => $user->username,
            'fullName' => trim(implode(' ', array_filter([$user->firstName, $user->middleName, $user->lastName]))),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        if (! (bool) $user->must_change_password) {
            return redirect()->route('dashboard');
        }

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password'         => ['required', 'string', 'min:8', 'max:72', 'confirmed', 'different:current_password'],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.different'                => 'The new password must be different from your current password.',
        ]);

        $user->update([
            'password'           => Hash::make($validated['password']),
            'must_change_password' => false,
        ]);

        return redirect()->route('dashboard')
            ->with('success', 'Your password has been changed. You can now access the system.');
    }
}
