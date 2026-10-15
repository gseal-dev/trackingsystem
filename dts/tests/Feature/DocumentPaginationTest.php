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

class DocumentPaginationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();

        $this->admin = $this->makeUser('Admin', 'boss');
        $this->staff = $this->makeUser('Staff', 'worker');
    }

    private function makeUser(string $role, string $username, ?int $depID = null): User
    {
        return User::create([
            'username' => $username, 'email' => "$username@example.com", 'password' => 'password',
            'firstName' => ucfirst($username), 'lastName' => 'Tester', 'departmentID' => $depID,
            'roleID' => Role::where('roleName', $role)->value('roleID'),
        ]);
    }

    /** Document number PREFIX-001 is the oldest; the higher the number, the newer the date. */
    private function makeDocuments(int $count, string $office = 'Engineering Division', ?User $owner = null, string $start = '2026-01-01', string $prefix = 'DOC'): void
    {
        $dep = Department::firstOrCreate(['depName' => $office], ['description' => $office]);

        for ($i = 1; $i <= $count; $i++) {
            $no = sprintf('%s-%03d', $prefix, $i);
            Document::create([
                'documentId' => (string) Str::uuid(), 'documentNo' => $no, 'documentTitle' => "Title $no",
                'title' => "Subject $no", 'description' => "Subject $no", 'documentType' => 'Memo',
                'documentDate' => Carbon::parse($start)->addDays($i)->toDateString(),
                'ownerID' => ($owner ?? $this->staff)->userID, 'currentDepartmentID' => $dep->depID,
                'filePath' => "documents/$no.pdf",
            ]);
        }
    }

    private function rows(string $html): int
    {
        return substr_count($html, 'title="View details"');
    }

    private function csvLines(string $content): array
    {
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);

        return array_values(array_filter(array_map('str_getcsv', preg_split('/\r?\n/', trim($content)))));
    }

    // ---- default of 25 per page -----------------------------------------------------

    public function test_staff_list_shows_25_rows_per_page_by_default_newest_first(): void
    {
        $this->makeDocuments(30);

        $page1 = $this->actingAs($this->staff)->get('/staff/documents')->assertOk();
        $html = $page1->getContent();
        $this->assertSame(25, $this->rows($html));
        $page1->assertSee('30 Documents Found')->assertSee('Showing 1&ndash;25 of 30 documents', false);
        $page1->assertSee('DOC-030')->assertSee('DOC-006')->assertDontSee('DOC-005');   // newest first

        $page2 = $this->get('/staff/documents?page=2');
        $this->assertSame(5, $this->rows($page2->getContent()));
        $page2->assertSee('DOC-005')->assertSee('DOC-001')->assertDontSee('DOC-006')
            ->assertSee('Showing 26&ndash;30 of 30 documents', false);
    }

    public function test_admin_list_shows_25_rows_per_page_by_default_and_links_back_to_the_list(): void
    {
        $this->makeDocuments(30);

        $page1 = $this->actingAs($this->admin)->get('/dashboard')->assertOk();
        $this->assertSame(25, $this->rows($page1->getContent()));
        $page1->assertSee('30 Documents Found')->assertSee('Showing 1&ndash;25 of 30 documents', false);
        // page links jump straight back to the list instead of the top of the dashboard
        $page1->assertSee('page=2#documents', false)->assertSee('id="documents"', false);

        $page2 = $this->get('/dashboard?page=2');
        $this->assertSame(5, $this->rows($page2->getContent()));
    }

    public function test_no_pager_when_everything_fits_on_one_page(): void
    {
        $this->makeDocuments(3);

        $html = $this->actingAs($this->staff)->get('/staff/documents')->assertSee('Showing 1&ndash;3 of 3 documents', false)->getContent();
        $this->assertStringNotContainsString('class="pagination', $html);

        $this->assertStringNotContainsString('class="pagination', $this->actingAs($this->admin)->get('/dashboard')->getContent());
    }

    public function test_empty_list_still_shows_the_empty_message(): void
    {
        $this->actingAs($this->staff)->get('/staff/documents')->assertOk()->assertSee('No records found.')->assertSee('0 Documents Found');
        $this->actingAs($this->admin)->get('/dashboard')->assertOk()->assertSee('No documents found.')->assertSee('0 Documents Found');
    }

    // ---- rows per page ----------------------------------------------------------------

    public function test_rows_per_page_can_be_changed_but_only_to_the_allowed_choices(): void
    {
        $this->makeDocuments(30);
        $this->actingAs($this->staff);

        $this->assertSame(10, $this->rows($this->get('/staff/documents?per_page=10')->getContent()));
        $this->assertSame(30, $this->rows($this->get('/staff/documents?per_page=50')->getContent()));
        // anything else falls back to 25 (nobody can ask for 100000 rows at once)
        $this->assertSame(25, $this->rows($this->get('/staff/documents?per_page=7')->getContent()));
        $this->assertSame(25, $this->rows($this->get('/staff/documents?per_page=100000')->getContent()));

        $this->actingAs($this->admin);
        $this->assertSame(10, $this->rows($this->get('/dashboard?per_page=10')->getContent()));
        $this->assertSame(25, $this->rows($this->get('/dashboard?per_page=abc')->getContent()));
    }

    public function test_the_page_size_selector_shows_the_current_choice(): void
    {
        $html = $this->actingAs($this->staff)->get('/staff/documents')->getContent();
        $this->assertMatchesRegularExpression('/<option value="25" selected>25<\/option>/', $html);

        $html = $this->get('/staff/documents?per_page=50')->getContent();
        $this->assertMatchesRegularExpression('/<option value="50" selected>50<\/option>/', $html);
    }

    // ---- filters and search work across pages ------------------------------------------------

    public function test_page_links_keep_the_filters_and_page_size(): void
    {
        $this->makeDocuments(30);

        $html = $this->actingAs($this->staff)->get('/staff/documents?year=2026&per_page=10')->getContent();
        $this->assertStringContainsString('year=2026', $html);
        $this->assertMatchesRegularExpression('/href="[^"]*per_page=10[^"]*page=2|href="[^"]*page=2[^"]*per_page=10/', $html);
    }

    public function test_date_filters_apply_before_paging(): void
    {
        $this->makeDocuments(30, 'Engineering Division', null, '2026-01-01');   // Jan 2 - Jan 31, 2026
        $this->makeDocuments(30, 'Finance', null, '2024-01-01', 'OLD');         // Jan 2 - Jan 31, 2024

        $page = $this->actingAs($this->staff)->get('/staff/documents?year=2024');
        $page->assertSee('30 Documents Found');
        $this->assertSame(25, $this->rows($page->getContent()));
        $this->assertSame(5, $this->rows($this->get('/staff/documents?year=2024&page=2')->getContent()));
    }

    public function test_search_looks_through_every_page_not_just_the_visible_rows(): void
    {
        $this->makeDocuments(30);
        Document::where('documentNo', 'DOC-001')->update(['title' => 'Quarterly zebra inspection']);   // oldest = last page

        $staffHtml = $this->actingAs($this->staff)->get('/staff/documents?q=zebra');
        $staffHtml->assertSee('1 Documents Found')->assertSee('Quarterly zebra inspection')->assertDontSee('DOC-030');

        $this->actingAs($this->admin)->get('/dashboard?q=zebra')
            ->assertSee('1 Documents Found')->assertSee('Quarterly zebra inspection')->assertDontSee('DOC-030');
    }

    public function test_search_matches_office_type_and_several_words_in_any_order(): void
    {
        $this->makeDocuments(3, 'Engineering Division');
        $this->makeDocuments(2, 'Finance Office', null, '2025-01-01', 'FIN');

        $this->actingAs($this->staff)->get('/staff/documents?q=finance')->assertSee('2 Documents Found');
        $this->get('/staff/documents?q=memo+finance')->assertSee('2 Documents Found');
        $this->get('/staff/documents?q=finance+FIN-002')->assertSee('1 Documents Found');
        $this->get('/staff/documents?q=nothing-matches-this')->assertSee('0 Documents Found');
    }

    public function test_admin_can_search_by_uploader_but_staff_search_ignores_it(): void
    {
        $other = $this->makeUser('Staff', 'carlos');
        $this->makeDocuments(2);                           // uploaded by "worker"
        $this->makeDocuments(1, 'Finance Office', $other, '2025-01-01', 'CAR');   // uploaded by "carlos"

        $this->actingAs($this->admin)->get('/dashboard?q=carlos')->assertSee('1 Documents Found');
        $this->actingAs($this->staff)->get('/staff/documents?q=carlos')->assertSee('0 Documents Found');
    }

    public function test_staff_only_sees_their_own_department_when_paging_and_searching(): void
    {
        $eng = Department::firstOrCreate(['depName' => 'Engineering Division'], ['description' => 'x']);
        $this->staff->update(['departmentID' => $eng->depID]);

        $this->makeDocuments(30, 'Engineering Division');
        $this->makeDocuments(10, 'Finance Office', null, '2025-01-01', 'FIN');

        $this->actingAs($this->staff)->get('/staff/documents')->assertSee('30 Documents Found');
        $this->get('/staff/documents?q=Finance')->assertSee('0 Documents Found');
    }

    public function test_a_page_past_the_end_goes_to_the_last_page(): void
    {
        $this->makeDocuments(30);

        $this->actingAs($this->staff)->get('/staff/documents?page=99')
            ->assertRedirect(url('/staff/documents?page=2'));
        $this->actingAs($this->admin)->get('/dashboard?page=99')->assertRedirect();
    }

    // ---- export: everything that matches, not just the page on screen -----------------

    public function test_staff_export_contains_every_matching_document_not_only_the_page(): void
    {
        $this->makeDocuments(30);

        $response = $this->actingAs($this->staff)->get(route('staff.documents.export'))->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('documents_export.csv', $response->headers->get('content-disposition'));

        $content = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $content);

        $lines = $this->csvLines($content);
        $this->assertSame(['Reference Number', 'Document Title', 'From Office', 'Type', 'Subject', 'Date'], $lines[0]);
        $this->assertCount(31, $lines);                      // header + all 30, not just 25
        $this->assertSame('DOC-030', $lines[1][0]);          // newest first
        $this->assertSame('DOC-001', $lines[30][0]);
        $this->assertSame(['DOC-030', 'Title DOC-030', 'Engineering Division', 'MEMO', 'Subject DOC-030'], array_slice($lines[1], 0, 5));
    }

    public function test_export_follows_the_filters_and_search(): void
    {
        $this->makeDocuments(30, 'Engineering Division', null, '2026-01-01');
        $this->makeDocuments(4, 'Finance Office', null, '2024-01-01', 'OLD');

        $byYear = $this->csvLines($this->actingAs($this->staff)->get(route('staff.documents.export', ['year' => 2024]))->streamedContent());
        $this->assertCount(5, $byYear);

        $bySearch = $this->csvLines($this->get(route('staff.documents.export', ['q' => 'DOC-030']))->streamedContent());
        $this->assertCount(2, $bySearch);
        $this->assertSame('DOC-030', $bySearch[1][0]);
    }

    public function test_export_button_carries_the_current_filters(): void
    {
        $html = $this->actingAs($this->staff)->get('/staff/documents?year=2026&month=3&q=memo&per_page=50&page=1')->getContent();

        $this->assertStringContainsString(route('staff.documents.export', ['year' => 2026, 'month' => 3, 'q' => 'memo']), html_entity_decode($html));
        $this->assertStringNotContainsString('export?year=2026&month=3&q=memo&per_page', html_entity_decode($html));   // page size is not a filter

        $adminHtml = $this->actingAs($this->admin)->get('/dashboard?year=2026')->getContent();
        $this->assertStringContainsString(route('admin.documents.export', ['year' => 2026]), html_entity_decode($adminHtml));
    }

    public function test_admin_export_has_everything_and_an_uploaded_by_column(): void
    {
        $other = $this->makeUser('Staff', 'carlos');
        $this->makeDocuments(30);
        $this->makeDocuments(2, 'Finance Office', $other, '2025-01-01', 'CAR');

        $lines = $this->csvLines($this->actingAs($this->admin)->get(route('admin.documents.export'))->streamedContent());

        $this->assertSame(['Reference Number', 'Document Title', 'From Office', 'Type', 'Subject', 'Uploaded By', 'Date'], $lines[0]);
        $this->assertCount(33, $lines);
        $this->assertSame(['carlos'], array_values(array_unique(array_column(array_filter($lines, fn ($l) => $l[2] === 'Finance Office'), 5))));

        $filtered = $this->csvLines($this->get(route('admin.documents.export', ['q' => 'carlos']))->streamedContent());
        $this->assertCount(3, $filtered);
    }

    public function test_export_does_not_let_text_run_as_a_spreadsheet_formula(): void
    {
        $this->makeDocuments(1);
        Document::query()->update(['title' => '=HYPERLINK("http://evil.example","click")']);

        $lines = $this->csvLines($this->actingAs($this->staff)->get(route('staff.documents.export'))->streamedContent());

        $this->assertSame("'=HYPERLINK(\"http://evil.example\",\"click\")", $lines[1][4]);
    }

    public function test_export_needs_the_right_login(): void
    {
        $this->get(route('staff.documents.export'))->assertRedirect(route('login'));
        $this->get(route('admin.documents.export'))->assertRedirect(route('login'));

        $this->actingAs($this->staff)->get(route('admin.documents.export'))->assertForbidden();
    }

    // ---- live refresh keeps working with pages ---------------------------------------------------

    public function test_pager_is_part_of_the_live_refresh(): void
    {
        $this->makeDocuments(30);

        $this->actingAs($this->staff)->get('/staff/documents')->assertSee('id="staff-docs-pager" data-live-refresh', false);
        $this->actingAs($this->admin)->get('/dashboard')->assertSee('id="admin-docs-pager" data-live-refresh', false);
    }

    public function test_search_box_is_part_of_the_form_so_it_searches_on_the_server(): void
    {
        $html = $this->actingAs($this->staff)->get('/staff/documents?q=memo')->getContent();
        $this->assertMatchesRegularExpression('/<input type="text" id="staff-record-search" name="q" value="memo"/', $html);

        $html = $this->actingAs($this->admin)->get('/dashboard?q=memo')->getContent();
        $this->assertMatchesRegularExpression('/<input type="text" id="admin-doc-search" name="q" value="memo"/', $html);
    }
}
