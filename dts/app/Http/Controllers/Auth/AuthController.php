<?php
namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use App\Models\User;

class AuthController extends Controller
{
    /** Maximum number of login attempts allowed per window. */
    public const MAX_LOGIN_ATTEMPTS = 5;

    /** Length of the rate-limit window in seconds. */
    public const RATE_LIMIT_WINDOW = 60;

    public function showLoginForm()
    {
        // On a fresh deploy with no accounts, the admin must set up
        // their first account via /setup instead of logging in.
        if (User::count() === 0) {
            return redirect()->route('setup');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $login = $credentials['login'];

        // Hard rate limit: MAX_LOGIN_ATTEMPTS tries per window per login+IP.
        // This is applied before any account lookup so the response does not
        // reveal whether a username exists.
        $rateLimitKey = 'login.' . $request->ip() . '.' . $login;
        if (RateLimiter::tooManyAttempts($rateLimitKey, self::MAX_LOGIN_ATTEMPTS)) {
            $retryAfter = now()->addSeconds(RateLimiter::availableIn($rateLimitKey));

            return back()->withErrors([
                'login' => 'Too many login attempts. Please try again later.',
            ])->withInput(['login' => $login])
                ->with('login_retry_after', $retryAfter);
        }
        RateLimiter::hit($rateLimitKey, self::RATE_LIMIT_WINDOW);

        // Find user by username.
        $user = User::where('username', $login)->first();

        // A locked account rejects every login (admin-locked or auto-locked below).
        if ($user && $this->isLocked($user)) {
            return back()->withErrors([
                'login' => 'Your account is locked. Please contact an administrator to unlock it.',
            ])->withInput(['login' => $login]);
        }

        $passwordCorrect = $user && Hash::check($credentials['password'], $user->password);

        if (!$passwordCorrect) {
            // Record the failure against a real account only; unknown usernames
            // get the same generic message so they cannot be enumerated.
            if ($user) {
                $this->recordFailedAttempt($user);
            }

            return back()->withErrors([
                'login' => 'Invalid login credentials.',
            ])->withInput(['login' => $login]);
        }

        // Successful login: clear any failure tracking and the rate limit.
        if ($user) {
            $user->update([
                'failed_login_attempts' => 0,
                'locked_at' => null,
                'locked_until' => null,
                'is_locked' => false,
            ]);
            RateLimiter::clear($rateLimitKey);
        }

        Auth::login($user);
        $request->session()->regenerate();

        // Force a password change on first login before allowing any other action.
        if ($user->must_change_password) {
            return redirect()->route('staff.first-password-change')
                ->with('info', 'You must change your password before accessing any other page.');
        }

        return redirect()->route('dashboard');
    }

    /** Whether the account is currently locked (admin or auto). */
    private function isLocked(User $user): bool
    {
        return $user->is_locked
            || ($user->locked_until && $user->locked_until->isFuture());
    }

    /** Increment the failure counter and auto-lock once the threshold is reached. */
    private function recordFailedAttempt(User $user): void
    {
        $attempts = (int) $user->failed_login_attempts + 1;

        $update = ['failed_login_attempts' => $attempts];

        if ($attempts >= self::MAX_LOGIN_ATTEMPTS) {
            // Auto-lock the account; an administrator can clear it via user management.
            $update['is_locked'] = true;
            $update['locked_at'] = now();
            $update['locked_until'] = null; // permanent until an admin unlocks it
        }

        $user->update($update);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
