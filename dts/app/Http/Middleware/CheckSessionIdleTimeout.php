<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Log the user off after N minutes of inactivity.
 *
 * Runs on every authenticated request and forces a logout when the gap
 * since the last genuine navigation exceeds the idle ceiling.
 *
 * AJAX / live-refresh polls (marked with `X-Requested-With`) only check the
 * timeout — they do NOT extend the activity window — so an unattended tab
 * can still time out even though it polls the server every few seconds.
 */
class CheckSessionIdleTimeout
{
    /** Minutes of inactivity before the session is considered expired. */
    public const IDLE_TIMEOUT_MINUTES = 15;

    /** Session key used to record the last authenticated navigation timestamp. */
    public const ACTIVITY_KEY = 'dts.last_activity';

    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $lastActivity = session(self::ACTIVITY_KEY);

            if ($lastActivity && now()->diffInMinutes($lastActivity) >= self::IDLE_TIMEOUT_MINUTES) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('info', 'Your session has expired due to inactivity. Please log in again.');
            }

            // Only genuine navigation extends the idle window; AJAX polls don't.
            if (! $request->ajax()) {
                session([self::ACTIVITY_KEY => now()]);
            }
        }

        return $next($request);
    }
}
