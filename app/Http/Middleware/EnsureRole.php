<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Limit a route to one or more roles, e.g. ->middleware('role:Admin')
 * or ->middleware('role:Admin,Staff'). Use it after the "auth" middleware.
 */
class EnsureRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $role = $request->user()?->role?->roleName;

        if (! $role || ! in_array($role, $roles, true)) {
            abort(403, 'You do not have permission to do this.');
        }

        return $next($request);
    }
}
