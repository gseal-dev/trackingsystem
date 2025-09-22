<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        // Assuming roleID 1 is Admin
        if (!Auth::check() || Auth::user()->roleID != 1) {
            abort(403, 'Unauthorized');
        }
        return $next($request);
    }
}