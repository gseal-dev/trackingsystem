<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Newly created staff accounts with a default/admin-assigned password must
 * change it on a dedicated first-login page before reaching any other page.
 */
class FirstPasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function roleId(string $name): int
    {
        return Role::where('roleName', $name)->value('roleID');
    }

    private function createForcedStaffUser(string $username, ?string $password = null): User
    {
        return User::create([
            'username' => $username,
            'email'    => $username . '@example.com',
            'password' => \Hash::make($password ?? 'Default-Pass-1'),
            'firstName' => 'New',
            'lastName'  => 'Staff',
            'roleID'    => $this->roleId('Staff'),
            'must_change_password' => true,
        ]);
    }

    /** The login handler sends forced users to the new dedicated page, not settings. */
    public function test_login_redirects_forced_user_to_first_password_change_page(): void
    {
        $user = $this->createForcedStaffUser('newstaff');

        $this->post('/login', [
            'login' => 'newstaff',
            'password' => 'Default-Pass-1',
        ])->assertRedirect(route('staff.first-password-change'))
          ->assertSessionHas('info');
    }

    /** The page is reachable only by authenticated users who must change their password. */
    public function test_first_password_change_page_shows_for_forced_user(): void
    {
        $user = $this->createForcedStaffUser('newstaff');

        $this->actingAs($user)
            ->get(route('staff.first-password-change'))
            ->assertOk()
            ->assertSee('Change Your Password')
            ->assertSee('You must change your password before you can access any other part of the system.');
    }

    /** A user who does not need to change their password is sent to the dashboard. */
    public function test_non_forced_user_cannot_reach_the_page(): void
    {
        $user = $this->createForcedStaffUser('customstaff');
        $user->update(['must_change_password' => false, 'password' => \Hash::make('Custom-Pass-1')]);

        $this->actingAs($user)
            ->get(route('staff.first-password-change'))
            ->assertRedirect(route('dashboard'));
    }

    /** Changing the password on the page clears the flag and goes to the dashboard. */
    public function test_changing_password_clears_flag_and_goes_to_dashboard(): void
    {
        $user = $this->createForcedStaffUser('newstaff');

        $this->actingAs($user)
            ->post(route('staff.first-password-change.update'), [
                'current_password' => 'Default-Pass-1',
                'password' => 'Brand-New-Pass-2',
                'password_confirmation' => 'Brand-New-Pass-2',
            ])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $this->assertFalse((bool) $user->fresh()->must_change_password);
        $this->assertTrue(\Hash::check('Brand-New-Pass-2', $user->fresh()->password));
    }

    /** Wrong current password is rejected. */
    public function test_wrong_current_password_is_rejected(): void
    {
        $user = $this->createForcedStaffUser('newstaff');

        $this->actingAs($user)
            ->post(route('staff.first-password-change.update'), [
                'current_password' => 'Wrong-Pass-1',
                'password' => 'Brand-New-Pass-2',
                'password_confirmation' => 'Brand-New-Pass-2',
            ])
            ->assertSessionHasErrors('current_password');

        $this->assertTrue((bool) $user->fresh()->must_change_password);
    }

    /** The new password must differ from the current one. */
    public function test_new_password_must_differ_from_current(): void
    {
        $user = $this->createForcedStaffUser('newstaff');

        $this->actingAs($user)
            ->post(route('staff.first-password-change.update'), [
                'current_password' => 'Default-Pass-1',
                'password' => 'Default-Pass-1',
                'password_confirmation' => 'Default-Pass-1',
            ])
            ->assertSessionHasErrors('password');

        $this->assertTrue((bool) $user->fresh()->must_change_password);
    }

    /** A forced user cannot reach the dashboard before changing their password. */
    public function test_forced_user_cannot_reach_dashboard(): void
    {
        $user = $this->createForcedStaffUser('newstaff');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('staff.first-password-change'));
    }

    /** A forced user cannot reach staff settings before changing their password. */
    public function test_forced_user_cannot_reach_staff_settings(): void
    {
        $user = $this->createForcedStaffUser('newstaff');

        $this->actingAs($user)
            ->get(route('staff.settings'))
            ->assertRedirect(route('staff.first-password-change'));
    }
}
