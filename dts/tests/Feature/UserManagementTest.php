<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Guards around deleting/locking administrator accounts:
 * an admin cannot delete or lock themselves, and the last remaining
 * active administrator cannot be deleted or locked (otherwise /setup
 * re-opens to anyone on the network).
 */
class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private Department $dep;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->dep = Department::firstOrCreate(['depName' => 'Engineering Division'], ['description' => 'x']);

        $this->admin = User::create([
            'username' => 'boss',
            'email' => 'boss@example.com',
            'password' => 'Boss-Pass-1',
            'firstName' => 'Ana',
            'lastName' => 'Boss',
            'departmentID' => $this->dep->depID,
            'roleID' => $this->roleId('Admin'),
        ]);
    }

    private function roleId(string $name): int
    {
        return Role::where('roleName', $name)->value('roleID');
    }

    private function createAdmin(array $overrides = []): User
    {
        return User::create(array_merge([
            'username' => 'another-admin',
            'email' => 'another@example.com',
            'password' => 'Another-Pass-1',
            'firstName' => 'Another',
            'lastName' => 'Admin',
            'departmentID' => $this->dep->depID,
            'roleID' => $this->roleId('Admin'),
        ], $overrides));
    }

    private function createStaff(array $overrides = []): User
    {
        return User::create(array_merge([
            'username' => 'worker',
            'email' => 'worker@example.com',
            'password' => 'Worker-Pass-1',
            'firstName' => 'Wes',
            'lastName' => 'Worker',
            'departmentID' => $this->dep->depID,
            'roleID' => $this->roleId('Staff'),
        ], $overrides));
    }

    /** An admin cannot delete their own account. */
    public function test_admin_cannot_delete_themselves(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.delete', ['type' => 'admins', 'user' => $this->admin->userID]))
            ->assertSessionHas('error');

        $this->assertNotNull(User::find($this->admin->userID), 'the admin still exists');
    }

    /** An admin cannot lock their own account. */
    public function test_admin_cannot_lock_themselves(): void
    {
        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.lock', ['type' => 'admins', 'user' => $this->admin->userID]), [
                'confirm_password' => 'Boss-Pass-1',
            ])
            ->assertSessionHas('error');

        $this->assertFalse((bool) User::find($this->admin->userID)->is_locked, 'the admin is still not locked');
    }

    /** The last active administrator cannot be deleted by another admin. */
    public function test_cannot_delete_the_last_active_admin(): void
    {
        $locked = $this->createAdmin(['username' => 'locked-admin', 'is_locked' => true, 'locked_at' => now()]);

        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.delete', ['type' => 'admins', 'user' => $this->admin->userID]))
            ->assertSessionHas('error');

        $this->assertNotNull(User::find($this->admin->userID), 'the last active admin still exists');
        $this->assertFalse((bool) User::find($this->admin->userID)->is_locked);
        $this->assertTrue((bool) User::find($locked->userID)->is_locked, 'the other admin stays locked');
    }

    /** The last active administrator cannot be locked by another admin. */
    public function test_cannot_lock_the_last_active_admin(): void
    {
        $locked = $this->createAdmin(['username' => 'locked-admin', 'is_locked' => true, 'locked_at' => now()]);

        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.lock', ['type' => 'admins', 'user' => $this->admin->userID]), [
                'confirm_password' => 'Boss-Pass-1',
            ])
            ->assertSessionHas('error');

        $this->assertFalse((bool) User::find($this->admin->userID)->is_locked, 'the last active admin stays unlocked');
    }

    /** An admin can delete another admin when more than one active administrator remains. */
    public function test_another_admin_can_delete_an_admin_when_multiple_active_admins_exist(): void
    {
        $otherAdmin = $this->createAdmin(['username' => 'deletable-admin']);

        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.delete', ['type' => 'admins', 'user' => $otherAdmin->userID]))
            ->assertSessionHas('success');

        $this->assertNull(User::find($otherAdmin->userID), 'the other admin was deleted');
        $this->assertNotNull(User::find($this->admin->userID), 'the acting admin still exists');
    }

    /** An admin can lock another admin when more than one active administrator remains. */
    public function test_another_admin_can_lock_an_admin_when_multiple_active_admins_exist(): void
    {
        $otherAdmin = $this->createAdmin(['username' => 'lockable-admin']);

        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.lock', ['type' => 'admins', 'user' => $otherAdmin->userID]), [
                'confirm_password' => 'Boss-Pass-1',
            ])
            ->assertSessionHas('success');

        $this->assertTrue((bool) User::find($otherAdmin->userID)->is_locked, 'the other admin is now locked');
    }

    /** A staff member can always be deleted (last-active-admin rule only applies to admins). */
    public function test_staff_can_be_deleted_even_when_the_only_admin_is_the_actor(): void
    {
        $staff = $this->createStaff();

        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.delete', ['type' => 'admins', 'user' => $staff->userID]))
            ->assertSessionHas('success');

        $this->assertNull(User::find($staff->userID));
    }

    /** A staff member can always be locked. */
    public function test_staff_can_be_locked(): void
    {
        $staff = $this->createStaff();

        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.lock', ['type' => 'admins', 'user' => $staff->userID]), [
                'confirm_password' => 'Boss-Pass-1',
            ])
            ->assertSessionHas('success');

        $this->assertTrue((bool) User::find($staff->userID)->is_locked);
    }

    /** Staff users cannot search usernames via the admin user-search endpoint. */
    public function test_staff_cannot_use_admin_user_search(): void
    {
        $staff = $this->createStaff();

        $this->actingAs($staff)
            ->get(route('admin.userSearch', ['q' => 'boss']))
            ->assertForbidden();
    }

    /** Staff users cannot search usernames via the staff user-search endpoint. */
    public function test_staff_cannot_use_staff_user_search(): void
    {
        $staff = $this->createStaff();

        $this->actingAs($staff)
            ->get(route('staff.userSearch', ['q' => 'boss']))
            ->assertForbidden();
    }

    /** Admin users can search usernames via both user-search endpoints. */
    public function test_admin_can_use_user_search_endpoints(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.userSearch', ['q' => 'boss']))
            ->assertOk();

        $this->actingAs($this->admin)
            ->get(route('staff.userSearch', ['q' => 'boss']))
            ->assertOk();
    }

    /** Locking requires the correct admin password. */
    public function test_lock_requires_correct_password(): void
    {
        $staff = $this->createStaff();

        $this->actingAs($this->admin)
            ->post(route('admin.userManagement.lock', ['type' => 'admins', 'user' => $staff->userID]), [
                'confirm_password' => 'Wrong-Password',
            ])
            ->assertSessionHas('error');

        $this->assertFalse((bool) User::find($staff->userID)->is_locked);
    }
}
