<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ForcePasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    private Department $dep;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->dep = Department::firstOrCreate(['depName' => 'Engineering Division'], ['description' => 'x']);
    }

    private function roleId(string $name): int
    {
        return Role::where('roleName', $name)->value('roleID');
    }

    /** A user created with a default/admin-assigned password must change it at first login. */
    public function test_user_created_with_default_password_forces_change_at_login(): void
    {
        $user = User::create([
            'username' => 'newstaff',
            'email' => 'newstaff@example.com',
            'password' => \Hash::make('Default-Pass-1'),
            'firstName' => 'New',
            'lastName' => 'Staff',
            'departmentID' => $this->dep->depID,
            'roleID' => $this->roleId('Staff'),
            'must_change_password' => true,
        ]);

        $this->assertTrue((bool) $user->must_change_password);

        // Logs in with the default password.
        $response = $this->post('/login', [
            'login' => 'newstaff',
            'password' => 'Default-Pass-1',
        ]);

        // Rejected route to dashboard, sent to first-password-change instead.
        $response->assertRedirect(route('staff.first-password-change'));
        $this->assertNotRedirectsToDashboard($response);
    }

    /** A user created with a custom password is not forced to change it. */
    public function test_user_created_with_custom_password_not_forced(): void
    {
        $user = User::create([
            'username' => 'customstaff',
            'email' => 'customstaff@example.com',
            'password' => \Hash::make('Custom-Pass-1'),
            'firstName' => 'Custom',
            'lastName' => 'Staff',
            'departmentID' => $this->dep->depID,
            'roleID' => $this->roleId('Staff'),
            'must_change_password' => false,
        ]);

        $this->assertFalse((bool) $user->must_change_password);

        $this->post('/login', [
            'login' => 'customstaff',
            'password' => 'Custom-Pass-1',
        ])->assertRedirect(route('dashboard'));
    }

    /** A user forced to change their password can do so and is then sent to the dashboard. */
    public function test_forced_user_changes_password_and_goes_to_dashboard(): void
    {
        $user = User::create([
            'username' => 'forcestaff',
            'email' => 'forcestaff@example.com',
            'password' => \Hash::make('Default-Pass-1'),
            'firstName' => 'Force',
            'lastName' => 'Staff',
            'departmentID' => $this->dep->depID,
            'roleID' => $this->roleId('Staff'),
            'must_change_password' => true,
        ]);

        $this->post('/login', [
            'login' => 'forcestaff',
            'password' => 'Default-Pass-1',
        ])->assertRedirect(route('staff.first-password-change'));

        // Change the password on the first-password-change page.
        $this->actingAs($user)->post(route('staff.first-password-change.update'), [
            'current_password' => 'Default-Pass-1',
            'password' => 'Brand-New-Pass-2',
            'password_confirmation' => 'Brand-New-Pass-2',
        ])->assertRedirect(route('dashboard'));

        $this->assertFalse((bool) $user->fresh()->must_change_password);
        $this->assertTrue(\Hash::check('Brand-New-Pass-2', $user->fresh()->password));
    }

    /** A password reset using the default password forces change at next login. */
    public function test_reset_with_default_password_forces_change(): void
    {
        $this->seed();

        \App\Models\Setting::updateOrCreate(
            ['key' => 'default_password'],
            ['value' => 'Default-Pass-999']
        );

        $admin = User::create([
            'username' => 'boss', 'email' => 'boss@example.com', 'password' => 'Boss-Pass-1',
            'firstName' => 'Ana', 'lastName' => 'Boss', 'roleID' => $this->roleId('Admin'),
        ]);

        $user = User::create([
            'username' => 'resetstaff',
            'email' => 'resetstaff@example.com',
            'password' => \Hash::make('Old-Pass-1'),
            'firstName' => 'Reset',
            'lastName' => 'Staff',
            'departmentID' => $this->dep->depID,
            'roleID' => $this->roleId('Staff'),
        ]);

        $this->actingAs($admin)->post(route('admin.userManagement.resetPassword', [
            'type' => 'admins', 'user' => $user,
        ]), [
            // No password typed -> falls back to default password -> force change.
        ])->assertSessionHas('success');

        $this->assertTrue((bool) $user->fresh()->must_change_password);
    }

    /** A password reset with a custom password does not force change. */
    public function test_reset_with_custom_password_does_not_force_change(): void
    {
        $this->seed();

        $admin = User::create([
            'username' => 'boss', 'email' => 'boss@example.com', 'password' => 'Boss-Pass-1',
            'firstName' => 'Ana', 'lastName' => 'Boss', 'roleID' => $this->roleId('Admin'),
        ]);

        $user = User::create([
            'username' => 'resetstaff2',
            'email' => 'resetstaff2@example.com',
            'password' => \Hash::make('Old-Pass-1'),
            'firstName' => 'Reset2',
            'lastName' => 'Staff',
            'departmentID' => $this->dep->depID,
            'roleID' => $this->roleId('Staff'),
        ]);

        $this->actingAs($admin)->post(route('admin.userManagement.resetPassword', [
            'type' => 'admins', 'user' => $user,
        ]), [
            'password' => 'Custom-Reset-Pass-3',
        ])->assertSessionHas('success');

        $this->assertFalse((bool) $user->fresh()->must_change_password);
    }

    private function assertNotRedirectsToDashboard($response): void
    {
        $location = $response->headers->get('Location');
        $this->assertStringNotContainsString(route('dashboard'), $location);
    }
}
