<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Block a staff member who still must change their password from reaching
 * any page except the first-password-change page and logout.
 */
class EnsurePasswordChanged
{
    /** Route names the forced-change user is allowed to reach. */
    private const ALLOWED_ROUTES = [
        'staff.first-password-change',
        'staff.first-password-change.update',
        'logout',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (bool) Auth::user()->must_change_password) {
            if (! in_array($request->route()->getName(), self::ALLOWED_ROUTES, true)) {
                return redirect()->route('staff.first-password-change')
                    ->with('info', 'You must change your password before accessing any other page.');
            }
        }

        return $next($request);
    }
}
