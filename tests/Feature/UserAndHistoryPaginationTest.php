<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserAndHistoryPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = $this->makeUser('Admin', 'boss', 'boss@example.com');
        $this->staff = $this->makeUser('Staff', 'worker', 'worker@example.com');
    }

    private function makeUser(string $role, string $username, string $email, string $first = null): User
    {
        return User::create([
            'username' => $username, 'email' => $email, 'password' => 'password',
            'firstName' => $first ?? ucfirst($username), 'lastName' => 'Tester',
            'roleID' => Role::where('roleName', $role)->value('roleID'),
        ]);
    }

    /** person01 ... person30 (all Staff); the last one is "Zelda" so it lands on the last page. */
    private function makeStaffUsers(int $count = 30): void
    {
        for ($i = 1; $i <= $count; $i++) {
            $n = sprintf('%02d', $i);
            $this->makeUser('Staff', "person$n", "person$n@example.com", $i === $count ? 'Zelda' : "Person$n");
        }
    }

    private function userRows(string $html): int
    {
        return substr_count($html, 'title="Edit user"');
    }

    private function csvLines(string $content): array
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        return array_values(array_filter(array_map('str_getcsv', preg_split('/\r?\n/', trim($content)))));
    }

    // ---- Users List ----------------------------------------------------------------------

    public function test_users_list_shows_25_rows_per_page_by_default(): void
    {
        $this->makeStaffUsers();
        $total = User::count();

        $page1 = $this->actingAs($this->admin)->get(route('admin.userManagement.admins'))->assertOk();
        $this->assertSame(25, $this->userRows($page1->getContent()));
        $page1->assertSee('Showing 1&ndash;25 of ' . $total . ' users', false);

        $page2 = $this->get(route('admin.userManagement.admins', ['page' => 2]));
        $this->assertSame($total - 25, $this->userRows($page2->getContent()));
        $page2->assertSee('Zelda');
    }

    public function test_users_list_keeps_creation_order(): void
    {
        $this->makeStaffUsers(3);

        $html = $this->actingAs($this->admin)->get(route('admin.userManagement.admins'))->getContent();

        $this->assertTrue(strpos($html, 'Boss Tester') < strpos($html, 'Worker Tester'));
        $this->assertTrue(strpos($html, 'Worker Tester') < strpos($html, 'Person01 Tester'));
    }

    public function test_users_rows_per_page_choices(): void
    {
        $this->makeStaffUsers();
        $this->actingAs($this->admin);

        $this->assertSame(10, $this->userRows($this->get(route('admin.userManagement.admins', ['per_page' => 10]))->getContent()));
        $this->assertSame(25, $this->userRows($this->get(route('admin.userManagement.admins', ['per_page' => 7]))->getContent()));
        $this->assertSame(25, $this->userRows($this->get(route('admin.userManagement.admins', ['per_page' => 100000]))->getContent()));
        $this->assertSame(User::count(), $this->userRows($this->get(route('admin.userManagement.admins', ['per_page' => 50]))->getContent()));

        $html = $this->get(route('admin.userManagement.admins', ['per_page' => 50]))->getContent();
        $this->assertMatchesRegularExpression('/<option value="50" selected>50<\/option>/', $html);
    }

    public function test_users_search_looks_through_every_page(): void
    {
        $this->makeStaffUsers();

        $page = $this->actingAs($this->admin)->get(route('admin.userManagement.admins', ['q' => 'zelda']))->assertOk();
        $this->assertSame(1, $this->userRows($page->getContent()));
        $page->assertSee('Zelda')->assertSee('Showing 1&ndash;1 of 1 users', false);
        $this->assertStringNotContainsString('class="pagination', $page->getContent());   // one page, no pager
    }

    public function test_users_search_matches_role_email_and_several_words(): void
    {
        $this->makeStaffUsers();
        $staffCount = User::whereHas('role', fn ($q) => $q->where('roleName', 'Staff'))->count();

        $this->actingAs($this->admin);
        $this->get(route('admin.userManagement.admins', ['q' => 'staff']))->assertSee('of ' . $staffCount . ' users', false);
        $this->get(route('admin.userManagement.admins', ['q' => 'person07']))->assertSee('Showing 1&ndash;1 of 1 users', false);
        $this->get(route('admin.userManagement.admins', ['q' => 'tester person07']))->assertSee('Showing 1&ndash;1 of 1 users', false);
        $this->get(route('admin.userManagement.admins', ['q' => 'nobody-at-all']))->assertSee('No users found.');
    }

    public function test_users_page_links_keep_the_search_and_page_size(): void
    {
        $this->makeStaffUsers();

        $html = $this->actingAs($this->admin)->get(route('admin.userManagement.admins', ['q' => 'person', 'per_page' => 10]))->getContent();

        $this->assertMatchesRegularExpression('/href="[^"]*q=person[^"]*"/', $html);
        $this->assertMatchesRegularExpression('/href="[^"]*per_page=10[^"]*page=2|href="[^"]*page=2[^"]*per_page=10/', $html);
        $this->assertMatchesRegularExpression('/<input type="text" id="admin-user-search" name="q" value="person"/', $html);
    }

    public function test_users_page_past_the_end_goes_to_the_last_page(): void
    {
        $this->makeStaffUsers();

        $this->actingAs($this->admin)->get(route('admin.userManagement.admins', ['page' => 99]))
            ->assertRedirect(route('admin.userManagement.admins', ['page' => 2]));
    }

    public function test_users_export_has_every_matching_user_not_only_the_page(): void
    {
        $this->makeStaffUsers();
        $total = User::count();

        $response = $this->actingAs($this->admin)->get(route('admin.userManagement.export'))->assertOk();
        $this->assertStringContainsString('admin-users.csv', $response->headers->get('content-disposition'));

        $lines = $this->csvLines($response->streamedContent());
        $this->assertSame(['Name', 'Role'], $lines[0]);
        $this->assertCount($total + 1, $lines);                       // all users, not just 25
        $this->assertContains(['Zelda Tester', 'Staff'], $lines);

        $filtered = $this->csvLines($this->get(route('admin.userManagement.export', ['q' => 'zelda']))->streamedContent());
        $this->assertCount(2, $filtered);
    }

    public function test_users_export_button_carries_the_search_and_export_is_admin_only(): void
    {
        $html = $this->actingAs($this->admin)->get(route('admin.userManagement.admins', ['q' => 'person', 'per_page' => 50]))->getContent();
        $this->assertStringContainsString(route('admin.userManagement.export', ['q' => 'person']), html_entity_decode($html));

        $this->actingAs($this->staff)->get(route('admin.userManagement.export'))->assertForbidden();
        auth()->logout();
        $this->get(route('admin.userManagement.export'))->assertRedirect(route('login'));
    }

    // ---- History ----------------------------------------------------------------------------

    /** HIS-001 is the oldest, HIS-030 the newest. */
    private function makeHistoryDocuments(int $count = 30): void
    {
        $dep = Department::firstOrCreate(['depName' => 'Engineering Division'], ['description' => 'x']);

        for ($i = 1; $i <= $count; $i++) {
            $no = sprintf('HIS-%03d', $i);
            $id = (string) Str::uuid();
            Document::create([
                'documentId' => $id, 'documentNo' => $no, 'documentTitle' => "Title $no", 'title' => "Subject $no",
                'description' => "Subject $no", 'documentDate' => '2026-01-01',
                'ownerID' => $this->staff->userID, 'currentDepartmentID' => $dep->depID, 'filePath' => "documents/$no.pdf",
            ]);
            DB::table('documents')->where('documentId', $id)->update(['created_at' => Carbon::parse('2026-02-01')->addMinutes($i)]);
        }
    }

    private function historyRows(string $html): int
    {
        return substr_count($html, '<td class="fw-bold">HIS-');
    }

    public function test_history_shows_25_rows_per_page_by_default_newest_first(): void
    {
        $this->makeHistoryDocuments();

        $page1 = $this->actingAs($this->staff)->get(route('staff.history'))->assertOk();
        $this->assertSame(25, $this->historyRows($page1->getContent()));
        $page1->assertSee('Showing 1&ndash;25 of 30 records', false)->assertSee('HIS-030')->assertSee('HIS-006')->assertDontSee('HIS-005');

        $page2 = $this->get(route('staff.history', ['page' => 2]));
        $this->assertSame(5, $this->historyRows($page2->getContent()));
        $page2->assertSee('HIS-005')->assertSee('HIS-001')->assertDontSee('HIS-006');
    }

    public function test_history_rows_per_page_choices(): void
    {
        $this->makeHistoryDocuments();
        $this->actingAs($this->staff);

        $this->assertSame(10, $this->historyRows($this->get(route('staff.history', ['per_page' => 10]))->getContent()));
        $this->assertSame(30, $this->historyRows($this->get(route('staff.history', ['per_page' => 50]))->getContent()));
        $this->assertSame(25, $this->historyRows($this->get(route('staff.history', ['per_page' => 3]))->getContent()));
        $this->assertSame(25, $this->historyRows($this->get(route('staff.history', ['per_page' => 100000]))->getContent()));
    }

    public function test_history_search_and_date_filters_apply_across_pages_and_stay_in_the_links(): void
    {
        $this->makeHistoryDocuments();
        Document::where('documentNo', 'HIS-001')->update(['title' => 'Quarterly zebra inspection']);

        $this->actingAs($this->staff)->get(route('staff.history', ['q' => 'zebra']))
            ->assertSee('Showing 1&ndash;1 of 1 records', false)->assertSee('Quarterly zebra inspection')->assertDontSee('HIS-030');

        $this->get(route('staff.history', ['date_from' => '2026-01-01', 'date_to' => '2026-01-31']))->assertSee('of 30 records', false);
        $this->get(route('staff.history', ['date_from' => '2020-01-01', 'date_to' => '2020-12-31']))->assertSee('No history records found.');

        $html = $this->get(route('staff.history', ['q' => 'HIS', 'date_from' => '2026-01-01', 'per_page' => 10]))->getContent();
        $this->assertStringContainsString('date_from=2026-01-01', $html);
        $this->assertMatchesRegularExpression('/href="[^"]*q=HIS[^"]*"/', $html);
        $this->assertMatchesRegularExpression('/href="[^"]*per_page=10[^"]*page=2|href="[^"]*page=2[^"]*per_page=10/', $html);
    }

    public function test_history_page_past_the_end_goes_to_the_last_page_and_empty_list_works(): void
    {
        $this->actingAs($this->staff)->get(route('staff.history'))->assertOk()->assertSee('No history records found.');

        $this->makeHistoryDocuments();
        $this->get(route('staff.history', ['page' => 99]))->assertRedirect(route('staff.history', ['page' => 2]));
    }

    public function test_history_page_size_selector_is_in_the_form(): void
    {
        $html = $this->actingAs($this->staff)->get(route('staff.history', ['per_page' => 50]))->getContent();

        $this->assertMatchesRegularExpression('/<select name="per_page" id="staff-history-per-page"/', $html);
        $this->assertMatchesRegularExpression('/<option value="50" selected>50<\/option>/', $html);
    }
}
