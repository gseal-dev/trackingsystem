<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginBruteForceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->user = User::create([
            'username' => 'alice',
            'email' => 'alice@example.com',
            'password' => 'Correct-Pass-1',
            'firstName' => 'Alice',
            'lastName' => 'Tester',
            'roleID' => Role::where('roleName', 'Staff')->value('roleID'),
        ]);
    }

    private function login(array $data)
    {
        return $this->post('/login', $data);
    }

    /** An unknown username yields the same generic message as any other login failure. */
    public function test_unknown_username_gives_the_generic_message(): void
    {
        $response = $this->login(['login' => 'no-such-user', 'password' => 'anything']);

        $response->assertSessionHasErrors('login');

        $errors = $response->getSession()->get('errors')->getBag('default')->get('login');
        $this->assertContains('Invalid login credentials.', $errors);
        $this->assertStringNotContainsString('No account found', (string) $errors[0]);
        $this->assertStringNotContainsString('Incorrect password', (string) $errors[0]);
    }

    /** An unknown user and a wrong password for a known user produce one identical error message. */
    public function test_unknown_username_and_wrong_password_show_the_same_message(): void
    {
        $unknown = (string) $this->login(['login' => 'no-such-user', 'password' => 'anything'])
            ->getSession()->get('errors')->getBag('default')->get('login')[0];

        $wrong = (string) $this->login(['login' => 'alice', 'password' => 'Wrong-Pass-9'])
            ->getSession()->get('errors')->getBag('default')->get('login')[0];

        $this->assertSame('Invalid login credentials.', $unknown);
        $this->assertSame($wrong, $unknown);
    }

    /** The wrong-password error lands on the generic login field, never a dedicated password field. */
    public function test_wrong_password_uses_only_the_login_field(): void
    {
        $response = $this->login(['login' => 'alice', 'password' => 'Wrong-Pass-9']);
        $response->assertSessionHasErrors('login');

        $errors = $response->getSession()->get('errors')->getBag('default');

        $this->assertArrayNotHasKey('password', $errors->toArray());
    }

    /** Repeated failures auto-lock the account even without administrator interaction. */
    public function test_repeated_failures_auto_lock_the_account(): void
    {
        foreach (range(1, AuthControllerTestFixtures::MAX_ATTEMPTS()) as $i) {
            $this->login(['login' => 'alice', 'password' => 'Wrong-Pass-9']);
        }

        $this->assertTrue((bool) $this->user->fresh()->is_locked);

        // One more failure is rejected because the account is now locked.
        $this->login(['login' => 'alice', 'password' => 'Wrong-Pass-9'])
            ->assertSessionHasErrors('login');

        // The correct password no longer works while locked.
        $this->login(['login' => 'alice', 'password' => 'Correct-Pass-1'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    /** An account locked by an administrator cannot log in. */
    public function test_account_locked_by_admin_cannot_log_in(): void
    {
        $this->user->update(['is_locked' => true, 'locked_at' => now()]);

        $this->login(['login' => 'alice', 'password' => 'Correct-Pass-1'])
            ->assertSessionHasErrors('login');

        $this->assertGuest();
    }

    /** A successful login clears the failure counter and any lock state. */
    public function test_successful_login_clears_failed_attempts(): void
    {
        $this->login(['login' => 'alice', 'password' => 'Wrong-Pass-9']);
        $this->assertSame(1, $this->user->fresh()->failed_login_attempts);

        $this->login(['login' => 'alice', 'password' => 'Correct-Pass-1'])
            ->assertRedirect(route('dashboard'));

        $this->assertAuthenticated();
        $this->assertSame(0, $this->user->fresh()->failed_login_attempts);
        $this->assertFalse((bool) $this->user->fresh()->is_locked);
    }
}

/** Read-only helper so the test does not duplicate the controller constant. */
class AuthControllerTestFixtures
{
    public static function MAX_ATTEMPTS(): int
    {
        return (new \ReflectionClass(\App\Http\Controllers\Auth\AuthController::class))
            ->getConstant('MAX_LOGIN_ATTEMPTS');
    }
}
