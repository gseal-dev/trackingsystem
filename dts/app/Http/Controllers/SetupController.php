<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SetupController extends Controller
{
    /** Show the one-time setup page that creates the first admin account. */
    public function showSetup(Request $request)
    {
        if (User::count() > 0) {
            return redirect()->route('dashboard')->with('info', 'Setup has already been completed.');
        }

        return view('auth.setup');
    }

    /** Create the first admin account on a fresh deploy. */
    public function createAdmin(Request $request)
    {
        if (User::count() > 0) {
            return redirect()->route('dashboard')->with('info', 'Setup has already been completed.');
        }

        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ], [], [
            'username' => 'username',
            'firstName' => 'first name',
            'lastName' => 'last name',
            'password' => 'password',
        ]);

        $adminRole = Role::where('roleName', 'Admin')->first();
        if (!$adminRole) {
            return back()->withErrors(['setup' => 'The Admin role is missing. Run the seeders first.'])->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'firstName' => $request->firstName,
            'lastName' => $request->lastName,
            'roleID' => $adminRole->roleID,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Admin account created. Welcome, ' . $user->firstName . '!');
    }
}
