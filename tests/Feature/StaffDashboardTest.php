<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class StaffDashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;
    private User $otherStaff;
    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        // Tuesday 13 October 2026, 2:35:07 PM in the Philippines (06:35:07 UTC)
        Carbon::setTestNow('2026-10-13 06:35:07');

        $this->staff = $this->makeUser('Staff', 'maria');
        $this->otherStaff = $this->makeUser('Staff', 'jose');
        $this->admin = $this->makeUser('Admin', 'boss');
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    private function makeUser(string $role, string $username): User
    {
        return User::create([
            'username' => $username, 'email' => "$username@example.com", 'password' => 'password',
            'firstName' => ucfirst($username), 'lastName' => 'Tester',
            'roleID' => Role::where('roleName', $role)->value('roleID'),
        ]);
    }

    private function upload(User $owner, string $createdAtUtc = '2026-10-05 02:00:00', bool $deleted = false): Document
    {
        static $n = 0;
        $n++;
        $dep = Department::firstOrCreate(['depName' => 'Engineering Division'], ['description' => 'x']);

        $doc = Document::create([
            'documentId' => (string) Str::uuid(), 'documentNo' => "DOC-$n", 'documentTitle' => "Title $n",
            'title' => "Subject $n", 'description' => "Subject $n",
            'documentDate' => '2026-10-01', 'ownerID' => $owner->userID,
            'currentDepartmentID' => $dep->depID, 'filePath' => "documents/f$n.pdf",
        ]);
        Document::where('documentId', $doc->documentId)->update(['created_at' => $createdAtUtc]);
        if ($deleted) {
            $doc->delete();
        }

        return $doc;
    }

    private function stat(string $html, string $id): int
    {
        $this->assertSame(1, preg_match('/id="' . $id . '">([\d,]+)</', $html, $m), "stat #$id not found");

        return (int) str_replace(',', '', $m[1]);
    }

    public function test_my_uploads_counts_only_the_logged_in_users_documents(): void
    {
        $this->upload($this->staff);
        $this->upload($this->staff);
        $this->upload($this->staff, '2026-08-10 02:00:00');                  // older upload, still theirs
        $this->upload($this->staff, '2026-10-06 02:00:00', deleted: true);   // deleted: not counted
        $this->upload($this->otherStaff);
        $this->upload($this->otherStaff);
        $this->upload($this->admin);

        $mine = $this->actingAs($this->staff)->get('/dashboard')->assertOk()->getContent();
        $this->assertSame(3, $this->stat($mine, 'statMyUploads'));
        $this->assertSame(2, $this->stat($mine, 'statMyUploadsMonth'));
        $this->assertStringContainsString('(October 2026)', $mine);

        $theirs = $this->actingAs($this->otherStaff)->get('/dashboard')->getContent();
        $this->assertSame(2, $this->stat($theirs, 'statMyUploads'));
        $this->assertSame(2, $this->stat($theirs, 'statMyUploadsMonth'));
    }

    public function test_a_user_with_no_uploads_sees_zeros(): void
    {
        $this->upload($this->otherStaff);

        $html = $this->actingAs($this->staff)->get('/dashboard')->getContent();

        $this->assertSame(0, $this->stat($html, 'statMyUploads'));
        $this->assertSame(0, $this->stat($html, 'statMyUploadsMonth'));
    }

    public function test_this_month_follows_philippine_time(): void
    {
        // 1 Oct 01:00 in Manila (still 30 Sept in UTC): counts for October
        $this->upload($this->staff, '2026-09-30 17:00:00');
        // 30 Sept 23:59 in Manila: counts for September
        $this->upload($this->staff, '2026-09-30 15:59:59');

        $html = $this->actingAs($this->staff)->get('/dashboard')->getContent();

        $this->assertSame(2, $this->stat($html, 'statMyUploads'));
        $this->assertSame(1, $this->stat($html, 'statMyUploadsMonth'));
    }

    public function test_all_three_stats_sit_in_a_single_row(): void
    {
        $html = $this->actingAs($this->staff)->get('/dashboard')->getContent();

        $start = strpos($html, 'Summary Metric Cards');
        $end = strpos($html, '<!-- Quick Access -->');
        $this->assertNotFalse($start);
        $this->assertGreaterThan($start, $end);
        $row = substr($html, $start, $end - $start);

        $this->assertSame(3, substr_count($row, 'class="metric-card"'));
        $this->assertSame(3, substr_count($row, 'class="col-12 col-md-4"'));   // 3 x 4 = one 12-column row
        $this->assertSame(1, substr_count($row, 'class="row g-4 mb-4"'));      // a single row
        foreach (['Department Documents', 'My Uploads', 'Uploaded This Month'] as $title) {
            $this->assertStringContainsString($title, $row);
        }
    }

    public function test_quick_access_sits_below_the_stats_and_links_to_settings(): void
    {
        $html = $this->actingAs($this->staff)->get('/dashboard')->getContent();

        $stats = strpos($html, 'id="statMyUploadsMonth"');
        $quick = strpos($html, 'id="quickAccess"');
        $chart = strpos($html, 'id="staffMonthlyChart"');
        $this->assertTrue($stats < $quick && $quick < $chart, 'order should be stats, quick access, chart');

        $this->assertStringContainsString('href="' . route('staff.documents') . '" class="quick-btn" id="quickDocuments"', $html);
        $this->assertStringContainsString('href="' . route('staff.document.create') . '" class="quick-btn" id="quickUpload"', $html);
        $this->assertStringContainsString('>Documents<', $html);
        $this->assertStringContainsString('>Upload Documents<', $html);

        // Settings opens the account settings page
        $this->assertStringContainsString('href="' . route('staff.settings') . '" class="quick-btn" id="quickSettings"', $html);
        $this->assertStringContainsString('>Settings<', $html);
    }

    public function test_quick_access_links_work(): void
    {
        $this->actingAs($this->staff)->get(route('staff.documents'))->assertOk();
        $this->get(route('staff.document.create'))->assertOk()->assertSee('Upload New Document');
    }

    public function test_card_header_shows_only_the_centered_date_and_time(): void
    {
        $html = $this->actingAs($this->staff)->get('/dashboard')->getContent();

        $this->assertSame(1, preg_match('/<div class="page-card-header page-card-header--center">(.*?)<\/div>/s', $html, $m));
        $header = trim(preg_replace('/\s+/', ' ', strip_tags($m[1])));

        $this->assertSame('Tuesday, October 13, 2026 | 2:35:07 PM', $header);
        $this->assertStringNotContainsString('Staff Dashboard', $m[1]);
        $this->assertStringContainsString('.page-card-header--center {', $html);
        $this->assertStringContainsString('justify-content: center;', $html);

        // the clock keeps ticking in the browser, in Philippine time
        $this->assertStringContainsString('id="dashClockDate"', $html);
        $this->assertStringContainsString('id="dashClockTime"', $html);
        $this->assertStringContainsString('setInterval(tick, 1000)', $html);
        $this->assertStringContainsString('"Asia\/Manila"', $html);
    }

    public function test_admin_dashboard_is_unchanged(): void
    {
        $this->actingAs($this->admin)->get('/dashboard')->assertOk()
            ->assertDontSee('id="quickAccess"', false)
            ->assertDontSee('page-card-header--center', false);
    }
}
