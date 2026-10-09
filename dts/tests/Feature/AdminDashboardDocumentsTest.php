<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Document;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminDashboardDocumentsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(); // roles, departments, document statuses, default admin

        $this->admin = $this->makeUser('Admin', 'boss');
        $this->staff = $this->makeUser('Staff', 'worker');
    }

    private function makeUser(string $role, string $username): User
    {
        return User::create([
            'username' => $username,
            'email' => "$username@example.com",
            'password' => 'password',
            'firstName' => ucfirst($username),
            'lastName' => 'Tester',
            'roleID' => Role::where('roleName', $role)->value('roleID'),
        ]);
    }

    private function makeDocument(string $no, string $title, string $office = 'Engineering Division', string $date = '2026-10-01'): Document
    {
        $dep = Department::firstOrCreate(['depName' => $office], ['description' => $office]);

        return Document::create([
            'documentId' => (string) Str::uuid(),
            'documentNo' => $no,
            'title' => $title,
            'description' => $title,
            'documentType' => 'Memo',
            'documentDate' => $date,
            'ownerID' => $this->staff->userID,
            'currentStatus' => 1,
            'currentDepartmentID' => $dep->depID,
            'filePath' => "documents/$no.pdf",
        ]);
    }

    public function test_admin_dashboard_shows_stats_with_document_list_below(): void
    {
        $this->makeDocument('D-1', 'First subject', 'Engineering Division');
        $this->makeDocument('D-2', 'Second subject', 'Finance');

        $html = $this->actingAs($this->admin)->get('/dashboard')
            ->assertOk()
            ->assertSee('Total Documents')
            ->assertSee('First subject')
            ->assertSee('Second subject')
            ->assertSee('2 Documents Found')
            ->assertSee('Add Document')
            ->getContent();

        // the list comes after the stats cards
        $this->assertGreaterThan(strpos($html, 'Total Documents'), strpos($html, 'Reference Number'));
        // every row has view / edit / delete controls
        $this->assertSame(2, substr_count($html, 'title="Edit Details"'));
        $this->assertSame(2, substr_count($html, 'title="Delete"'));
    }

    public function test_admin_sees_documents_from_every_department_and_uploader(): void
    {
        $this->makeDocument('D-1', 'Eng doc', 'Engineering Division');
        $this->makeDocument('D-2', 'Fin doc', 'Finance');

        // even if the admin account itself belongs to a department
        $this->admin->update(['departmentID' => Department::where('depName', 'Finance')->value('depID')]);

        $this->actingAs($this->admin)->get('/dashboard')->assertSee('Eng doc')->assertSee('Fin doc')->assertSee('worker');
    }

    public function test_dashboard_list_can_be_filtered_by_date(): void
    {
        $this->makeDocument('D-1', 'October doc', 'Finance', '2026-10-05');
        $this->makeDocument('D-2', 'Old doc', 'Finance', '2024-03-02');

        $this->actingAs($this->admin)->get('/dashboard?year=2026&month=10')
            ->assertSee('October doc')->assertDontSee('Old doc')->assertSee('1 Documents Found');
    }

    public function test_admin_can_add_a_document_from_the_dashboard(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin)->post(route('staff.document.store'), [
            'documentNo' => 'ADM-001',
            'fromOffice' => 'Legal Office',
            'documentType' => 'Letter',
            'title' => 'Added by admin',
            'documentDate' => '2026-10-07',
            'file' => UploadedFile::fake()->create('letter.pdf', 50, 'application/pdf'),
        ])->assertRedirect(route('dashboard'));

        $doc = Document::where('documentNo', 'ADM-001')->firstOrFail();
        $this->assertSame($this->admin->userID, $doc->ownerID);
        $this->assertSame('Legal Office', $doc->department->depName);
        Storage::disk('public')->assertExists($doc->filePath);

        $this->get('/dashboard')->assertSee('Added by admin')->assertSee('Legal Office');
    }

    public function test_add_document_validation_errors_come_back_to_the_dashboard(): void
    {
        $this->actingAs($this->admin)->from('/dashboard')->post(route('staff.document.store'), [])
            ->assertRedirect('/dashboard')
            ->assertSessionHasErrors(['documentNo', 'title', 'documentType', 'fromOffice', 'file']);
    }

    public function test_admin_can_edit_a_document(): void
    {
        Storage::fake('public');
        $doc = $this->makeDocument('D-1', 'Old title', 'Finance');

        $this->actingAs($this->admin)->get(route('staff.document.edit', $doc->documentId))
            ->assertOk()
            ->assertSee('name="fromOffice"', false)
            ->assertSee('name="documentNo"', false)
            ->assertSee('value="Finance"', false)
            ->assertSee('enctype="multipart/form-data"', false);

        $this->post(route('staff.document.update', $doc->documentId), [
            'documentNo' => 'D-1',
            'fromOffice' => 'Legal Office',
            'documentType' => 'Report',
            'title' => 'New title',
            'documentDate' => '2026-10-03',
        ])->assertRedirect(route('dashboard'));

        $doc->refresh();
        $this->assertSame('New title', $doc->title);
        $this->assertSame('Report', $doc->documentType);
        $this->assertSame('Legal Office', $doc->department->depName);
    }

    public function test_edit_can_replace_the_pdf(): void
    {
        Storage::fake('public');
        $doc = $this->makeDocument('D-1', 'With file');
        Storage::disk('public')->put($doc->filePath, 'old');

        $this->actingAs($this->admin)->post(route('staff.document.update', $doc->documentId), [
            'documentNo' => 'D-1', 'fromOffice' => 'Finance', 'documentType' => 'Memo', 'title' => 'With file',
            'file' => UploadedFile::fake()->create('new.pdf', 20, 'application/pdf'),
        ])->assertRedirect(route('dashboard'));

        $doc->refresh();
        Storage::disk('public')->assertMissing('documents/D-1.pdf');
        Storage::disk('public')->assertExists($doc->filePath);
    }

    public function test_admin_can_delete_and_undo(): void
    {
        $doc = $this->makeDocument('D-1', 'To delete');

        $this->actingAs($this->admin)->from('/dashboard')->delete(route('staff.document.delete', $doc->documentId))
            ->assertRedirect('/dashboard')
            ->assertSessionHas('undo_delete_id', $doc->documentId);
        $this->assertSoftDeleted($doc);

        // gone from the list (the undo banner may still mention its title)
        $this->get('/dashboard')->assertSee('0 Documents Found');

        $this->post(route('staff.document.undo', $doc->documentId))->assertRedirect();
        $this->assertNotSoftDeleted($doc);
        $this->get('/dashboard')->assertSee('To delete');
    }

    public function test_success_message_is_a_single_popup_with_ok(): void
    {
        $doc = $this->makeDocument('D-1', 'Editable');

        $this->actingAs($this->admin)->post(route('staff.document.update', $doc->documentId), [
            'documentNo' => 'D-1', 'fromOffice' => 'Finance', 'documentType' => 'Memo', 'title' => 'Edited',
        ])->assertRedirect(route('dashboard'));

        $this->get('/dashboard')
            ->assertSee('id="flashModal"', false)
            ->assertSee('Document updated successfully!')
            ->assertSee('>OK</button>', false)
            ->assertDontSee('alert-success', false)
            ->assertDontSee('>Undo</button>', false);

        // shown once only: the next page has no popup
        $this->get('/dashboard')->assertDontSee('id="flashModal"', false);
    }

    public function test_delete_popup_has_undo_and_ok(): void
    {
        $doc = $this->makeDocument('D-1', 'Gone soon');

        $this->actingAs($this->admin)->from('/dashboard')->delete(route('staff.document.delete', $doc->documentId));

        $html = $this->get('/dashboard')
            ->assertSee('Document deleted successfully.')
            ->assertSee('Gone soon')
            ->assertSee('>Undo</button>', false)
            ->assertSee('>OK</button>', false)
            ->assertSee(route('staff.document.undo', $doc->documentId), false)
            ->assertDontSee('alert-info', false)
            ->getContent();

        $this->assertSame(1, substr_count($html, 'id="flashModal"'));
    }

    public function test_no_page_shows_inline_stacking_alerts(): void
    {
        foreach ([$this->admin, $this->staff] as $user) {
            foreach (['/dashboard', '/staff/documents'] as $url) {
                $this->actingAs($user)->withSession(['success' => 'Saved!'])->get($url)
                    ->assertSee('id="flashModal"', false)
                    ->assertDontSee('alert-success', false);
            }
        }
        $this->actingAs($this->admin)->withSession(['success' => 'User added!'])->get('/admin/user-management/admins')
            ->assertSee('id="flashModal"', false)->assertDontSee('alert-success', false);
    }

    public function test_lists_are_wired_for_live_refresh(): void
    {
        $this->makeDocument('D-1', 'Live doc');

        $this->actingAs($this->admin)->get('/dashboard')
            ->assertSee('id="admin-docs-body" data-live-refresh', false);

        $this->actingAs($this->staff)->get('/staff/documents')
            ->assertOk()
            ->assertSee('Live doc')
            ->assertSee('id="staff-records-body" data-live-refresh', false);
    }

    public function test_staff_pages_still_work(): void
    {
        $this->actingAs($this->staff)->get('/dashboard')->assertOk()->assertSee('Staff Dashboard');
        $this->actingAs($this->staff)->get('/staff/documents')->assertOk();
    }

    public function test_document_actions_require_login(): void
    {
        $doc = $this->makeDocument('D-1', 'Protected');

        $this->delete(route('staff.document.delete', $doc->documentId))->assertRedirect('/login');
        $this->post(route('staff.document.store'))->assertRedirect('/login');
        $this->post(route('staff.document.update', $doc->documentId))->assertRedirect('/login');
        $this->assertNotSoftDeleted($doc);
    }
}
