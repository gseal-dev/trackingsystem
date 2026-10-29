<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Admins create a new account with a DEFAULT password (one field, no confirmation) and hand it to the user,
 * who can then change it in Settings after logging in.
 */
class AdminDefaultPasswordTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = User::create([
            'username' => 'boss', 'password' => 'boss-password-1',
            'firstName' => 'Ana', 'lastName' => 'Boss', 'roleID' => $this->roleId('Admin'),
        ]);
    }

    private function roleId(string $name): int
    {
        return Role::where('roleName', $name)->value('roleID');
    }

    private function addUser(array $overrides = [])
    {
        return $this->actingAs($this->admin)->post(route('admin.userManagement.add', ['type' => 'admins']), array_merge([
            'username' => 'newstaff', 'firstName' => 'Nina', 'lastName' => 'Newhire',
            'roleID' => $this->roleId('Staff'), 'password' => 'Welcome-2026',
        ], $overrides));
    }

    private function modal(): string
    {
        $html = $this->actingAs($this->admin)->get(route('admin.userManagement.admins'))->assertOk()->getContent();
        $start = strpos($html, 'id="addUserModal"');
        $this->assertNotFalse($start, 'the add-user modal is on the users page');

        return substr($html, $start, strpos($html, '</form>', $start) - $start + 7);
    }

    // ---- the server side ---------------------------------------------------------------------

    public function test_an_admin_can_create_a_user_with_just_a_default_password(): void
    {
        $this->addUser()->assertSessionHasNoErrors()->assertRedirect(route('admin.userManagement.admins'));

        $user = User::where('username', 'newstaff')->firstOrFail();
        $this->assertSame($this->roleId('Staff'), $user->roleID);
        $this->assertTrue(Hash::check('Welcome-2026', $user->password));
        $this->assertNotSame('Welcome-2026', $user->password, 'stored hashed, never as typed');
    }

    public function test_no_confirmation_is_needed_and_a_stray_one_is_ignored(): void
    {
        $this->addUser(['password_confirmation' => 'something-else'])->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('Welcome-2026', User::where('username', 'newstaff')->value('password')));
    }

    public function test_the_default_password_must_be_between_8_and_72_characters(): void
    {
        $this->addUser(['password' => 'short7!'])->assertSessionHasErrors('password');
        $this->addUser(['password' => ''])->assertSessionHasErrors('password');
        $this->addUser(['password' => str_repeat('a', 73)])->assertSessionHasErrors('password');
        $this->assertNull(User::where('username', 'newstaff')->first(), 'nothing was created');

        $this->addUser(['password' => 'exactly8'])->assertSessionHasNoErrors();
        $this->addUser(['username' => 'longpw', 'password' => str_repeat('a', 72)])->assertSessionHasNoErrors();
        $this->assertNotNull(User::where('username', 'longpw')->first());
    }

    public function test_the_other_rules_still_apply(): void
    {
        $this->addUser()->assertSessionHasNoErrors();

        $this->addUser(['username' => 'newstaff'])->assertSessionHasErrors('username');            // same username
        $this->addUser(['username' => 'x1', 'roleID' => 9999])->assertSessionHasErrors('roleID');
        $this->assertSame(1 + 1, User::count(), 'only the admin and the first new user exist');
    }

    public function test_adding_a_staff_user_with_default_password_sets_must_change_password(): void
{
    \App\Models\Setting::updateOrCreate(
        ['key' => 'default_password'],
        ['value' => 'Default-Pass-1']
    );

    // No password typed -> falls back to default -> Staff user is forced to change.
    $this->addUser(['password' => null])->assertSessionHasNoErrors()->assertRedirect(route('admin.userManagement.admins'));

    $user = User::where('username', 'newstaff')->firstOrFail();
    $this->assertTrue((bool) $user->must_change_password);

    // A new admin typed a custom password, so no force change.
    $this->addUser([
        'username' => 'newadmin',
        'roleID' => $this->roleId('Admin'), 'password' => 'Admin-Pass-999',
    ]);

    $adminUser = User::where('username', 'newadmin')->firstOrFail();
    $this->assertFalse((bool) $adminUser->must_change_password);
}

public function test_the_success_message_tells_the_admin_to_hand_the_password_over(): void
    {
        $this->addUser()->assertSessionHas('success', function ($m) {
            return stripos($m, 'give them the password') !== false
                && stripos($m, 'change it at first login') !== false
                && stripos($m, 'default password') === false;
        });

        $this->addUser(['username' => 'newadmin', 'email' => 'newadmin@example.com', 'roleID' => $this->roleId('Admin')])
            ->assertSessionHas('success', 'User added!');           // admins cannot use Settings, so no promise is made
    }

    public function test_the_password_is_never_put_in_the_session_or_the_message(): void
    {
        $response = $this->addUser(['password' => 'Secret-Default-1']);

        $this->assertStringNotContainsString('Secret-Default-1', json_encode(session()->all()));
        $this->assertStringNotContainsString('Secret-Default-1', (string) $response->headers->get('Location'));
    }

    public function test_only_admins_can_create_users(): void
    {
        auth()->logout();
        $this->post(route('admin.userManagement.add', ['type' => 'admins']), [])->assertRedirect(route('login'));

        $staff = User::create([
            'username' => 'worker', 'password' => 'worker-password-1',
            'firstName' => 'Wes', 'lastName' => 'Worker', 'roleID' => $this->roleId('Staff'),
        ]);
        $this->actingAs($staff)->post(route('admin.userManagement.add', ['type' => 'admins']), [
            'username' => 'sneaky', 'firstName' => 'S', 'lastName' => 'S',
            'roleID' => $this->roleId('Admin'), 'password' => 'Sneaky-pass-1',
        ])->assertForbidden();
        $this->assertNull(User::where('username', 'sneaky')->first());
    }

    // ---- the whole journey ---------------------------------------------------------------------

    public function test_the_new_user_logs_in_with_the_default_password_and_then_changes_it(): void
    {
        $this->addUser();
        $this->post(route('logout'));
        $this->assertGuest();

        // 1) logs in with the default password the admin set
        $this->post('/login', ['login' => 'newstaff', 'password' => 'Welcome-2026'])->assertRedirect();
        $this->assertAuthenticated();

        // 2) changes it in Settings
        $this->post(route('staff.settings.password'), [
            'current_password' => 'Welcome-2026', 'password' => 'My-Own-Password-9', 'password_confirmation' => 'My-Own-Password-9',
        ])->assertSessionHasNoErrors()->assertSessionHas('success');

        // 3) the default password no longer works, the new one does
        $this->post(route('logout'));
        $this->post('/login', ['login' => 'newstaff', 'password' => 'Welcome-2026']);
        $this->assertGuest();
        $this->post('/login', ['login' => 'newstaff', 'password' => 'My-Own-Password-9']);
        $this->assertAuthenticated();
    }

    // ---- the form ----------------------------------------------------------------------------------

    public function test_the_form_has_a_single_default_password_field_with_helpers(): void
    {
        $modal = $this->modal();

        $this->assertMatchesRegularExpression('/<label[^>]*for="password"[^>]*>\s*Password\s*<\/label>/', $modal);
        $this->assertSame(1, preg_match_all('/<input[^>]*type="password"/', $modal), 'one password field, not two');
        $this->assertStringNotContainsString('password_confirmation', $modal);
        $this->assertStringNotContainsString('Confirm Password', $modal);

        $this->assertMatchesRegularExpression('/<input[^>]*name="password"[^>]*\srequired[\s>]/', $modal);
        $this->assertMatchesRegularExpression('/<input[^>]*name="password"[^>]*minlength="8"/', $modal);
        $this->assertMatchesRegularExpression('/<input[^>]*name="password"[^>]*maxlength="72"/', $modal);
        $this->assertMatchesRegularExpression('/<input[^>]*name="password"[^>]*autocomplete="new-password"/', $modal);   // browsers must not fill in the admin's own password
        $this->assertDoesNotMatchRegularExpression('/<input[^>]*name="password"[^>]*\svalue=/', $modal);                 // never pre-filled

        foreach (['togglePasswordBtn', 'generatePasswordBtn', 'copyPasswordBtn'] as $button) {
            $this->assertMatchesRegularExpression('/<button type="button" id="' . $button . '"/', $modal, $button);
        }
        $this->assertStringContainsString('id="defaultPasswordHint"', $modal);
    }

    public function test_the_page_script_carries_the_helpers_and_cleans_up(): void
    {
        $html = $this->actingAs($this->admin)->get(route('admin.userManagement.admins'))->getContent();

        $this->assertStringContainsString('crypto.getRandomValues', $html);                 // a secure random source, not Math.random
        $this->assertStringNotContainsString('Math.random', substr($html, strpos($html, 'Default password helpers')));
        $this->assertStringContainsString("hidden.bs.modal", $html);                         // cleared when the form is closed
        $this->assertStringContainsString('Staff can change this password themselves', $html);
        $this->assertStringContainsString('Admins cannot change their own password', $html);
    }

    public function test_the_edit_form_is_unchanged(): void
    {
        $html = $this->actingAs($this->admin)->get(route('admin.userManagement.admins'))->getContent();

        $this->assertStringContainsString('id="editPassword"', $html);
        $this->assertStringContainsString('id="editPasswordConfirmation"', $html);
    }
}
