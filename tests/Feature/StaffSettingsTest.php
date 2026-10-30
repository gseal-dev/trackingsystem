<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StaffSettingsTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $dep = Department::firstOrCreate(['depName' => 'Engineering Division'], ['description' => 'x']);

        $this->staff = $this->makeUser('Staff', 'maria', $dep->depID);
        $this->admin = $this->makeUser('Admin', 'boss');
    }

    private function makeUser(string $role, string $username, ?int $depID = null): User
    {
        return User::create([
            'username' => $username, 'email' => "$username@example.com", 'password' => 'old-password-1',
            'firstName' => ucfirst($username), 'lastName' => 'Tester', 'departmentID' => $depID,
            'roleID' => Role::where('roleName', $role)->value('roleID'),
        ]);
    }

    private function change(array $data)
    {
        return $this->actingAs($this->staff)->from(route('staff.settings'))
            ->post(route('staff.settings.password'), $data);
    }

    public function test_staff_see_read_only_account_info_and_a_password_form(): void
    {
        $this->actingAs($this->staff)->get(route('staff.settings'))
            ->assertOk()
            ->assertSee('Account Settings')
            ->assertSee('Account Information')
            ->assertSee('Change Password')
            ->assertSee('maria')
            ->assertSee('Engineering Division')
            ->assertSee('Update Password')
            ->assertSee('requested through the system administrator');
    }

    public function test_only_the_password_form_is_editable(): void
    {
        $html = $this->actingAs($this->staff)->get(route('staff.settings'))->getContent();

        // exactly one form, and it posts to the password route only
        $this->assertSame(1, substr_count($html, 'id="passwordForm"'));
        $this->assertStringContainsString('action="' . route('staff.settings.password') . '"', $html);
        $this->assertSame(3, preg_match_all('/<input type="password"/', $html));
    }

    public function test_staff_can_change_their_password(): void
    {
        $this->change([
            'current_password' => 'old-password-1',
            'password' => 'brand-new-pass-2',
            'password_confirmation' => 'brand-new-pass-2',
        ])->assertRedirect(route('staff.settings'))->assertSessionHas('success');

        $this->assertTrue(Hash::check('brand-new-pass-2', $this->staff->fresh()->password));
        $this->assertFalse(Hash::check('old-password-1', $this->staff->fresh()->password));
    }

    public function test_wrong_current_password_is_rejected(): void
    {
        $this->change([
            'current_password' => 'not-my-password',
            'password' => 'brand-new-pass-2',
            'password_confirmation' => 'brand-new-pass-2',
        ])->assertSessionHasErrors('current_password');

        $this->assertTrue(Hash::check('old-password-1', $this->staff->fresh()->password));
    }

    public function test_new_password_must_be_confirmed_long_enough_and_different(): void
    {
        $this->change(['current_password' => 'old-password-1', 'password' => 'brand-new-pass-2', 'password_confirmation' => 'nope'])
            ->assertSessionHasErrors('password');
        $this->change(['current_password' => 'old-password-1', 'password' => 'short', 'password_confirmation' => 'short'])
            ->assertSessionHasErrors('password');
        $this->change(['current_password' => 'old-password-1', 'password' => 'old-password-1', 'password_confirmation' => 'old-password-1'])
            ->assertSessionHasErrors('password');

        $this->assertTrue(Hash::check('old-password-1', $this->staff->fresh()->password));
    }

    public function test_other_account_fields_cannot_be_changed_through_this_route(): void
    {
        $this->change([
            'current_password' => 'old-password-1',
            'password' => 'brand-new-pass-2',
            'password_confirmation' => 'brand-new-pass-2',
            'username' => 'hacker', 'email' => 'hacker@example.com', 'roleID' => $this->admin->roleID,
        ]);

        $fresh = $this->staff->fresh();
        $this->assertSame('maria', $fresh->username);
        $this->assertNotSame($this->admin->roleID, $fresh->roleID);
    }

    public function test_admins_and_guests_cannot_use_staff_settings(): void
    {
        $this->get(route('staff.settings'))->assertRedirect(route('login'));
        $this->post(route('staff.settings.password'), [])->assertRedirect(route('login'));

        $this->actingAs($this->admin)->get(route('staff.settings'))->assertForbidden();
        $this->post(route('staff.settings.password'), [
            'current_password' => 'old-password-1', 'password' => 'brand-new-pass-2', 'password_confirmation' => 'brand-new-pass-2',
        ])->assertForbidden();
    }

    public function test_dashboard_quick_access_settings_button_opens_settings(): void
    {
        $html = $this->actingAs($this->staff)->get(route('dashboard'))->getContent();

        $this->assertStringContainsString('href="' . route('staff.settings') . '" class="quick-btn" id="quickSettings"', $html);
        $this->get(route('staff.settings'))->assertOk();
    }

    public function test_sidebar_profile_row_links_to_settings_for_staff_only(): void
    {
        $link = 'href="' . route('staff.settings') . '"';

        $this->actingAs($this->staff)->get(route('dashboard'))
            ->assertSee('class="sidebar-user sidebar-profile', false)->assertSee($link, false);

        $this->actingAs($this->admin)->get(route('dashboard'))
            ->assertDontSee('class="sidebar-user sidebar-profile', false)->assertDontSee($link, false);
    }
}
