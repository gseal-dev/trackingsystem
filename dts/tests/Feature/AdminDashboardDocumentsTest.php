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
        // each row has a single View button (no inline edit / delete)
        $this->assertSame(2, substr_count($html, 'title="View details"'));
        $this->assertStringNotContainsString('title="Edit Details"', $html);
        $this->assertStringNotContainsString('title="Delete"', $html);
        // admins can delete (from the popup) but not edit
        $this->assertStringContainsString('id="documentDeleteForm"', $html);
        $this->assertStringNotContainsString('id="documentEditBtn"', $html);
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

    // ---- Upload page ---------------------------------------------------------------------

    public function test_upload_page_has_the_form_and_read_only_metadata(): void
    {
        foreach ([$this->admin, $this->staff] as $user) {
            $this->actingAs($user)->get(route('staff.document.create'))
                ->assertOk()
                ->assertSee('Upload New Document')
                ->assertSee('Document Information')
                ->assertSee('File Upload')
                ->assertSee('Document Metadata')
                ->assertSee('name="documentType"', false)
                ->assertSee('name="documentNo"', false)
                ->assertSee('name="fromOffice"', false)
                ->assertSee('name="documentDate"', false)
                ->assertSee('name="title"', false)
                ->assertSee('name="file"', false)
                ->assertSee('enctype="multipart/form-data"', false)
                ->assertSee('Click to browse')
                ->assertSee($user->username)          // "Uploaded By"
                ->assertSee(route('staff.document.store'), false);
        }
    }

    public function test_upload_page_suggests_existing_types_and_offices(): void
    {
        $this->makeDocument('D-1', 'x', 'Finance Office');

        $this->actingAs($this->staff)->get(route('staff.document.create'))
            ->assertSee('<option value="Memo"></option>', false)
            ->assertSee('<option value="Finance Office"></option>', false);
    }

    public function test_cancel_goes_back_to_the_right_list(): void
    {
        $this->actingAs($this->staff)->get(route('staff.document.create'))
            ->assertSee('href="' . route('staff.documents') . '" class="up-btn up-btn-cancel"', false);
        $this->actingAs($this->admin)->get(route('staff.document.create'))
            ->assertSee('href="' . route('dashboard') . '" class="up-btn up-btn-cancel"', false);
    }

    public function test_lists_link_to_the_upload_page_instead_of_a_popup(): void
    {
        $link = 'href="' . route('staff.document.create') . '" class="btn-create"';

        $this->actingAs($this->admin)->get('/dashboard')
            ->assertSee($link, false)->assertDontSee('addDocumentModal', false);
        $this->actingAs($this->staff)->get('/staff/documents')
            ->assertSee($link, false)->assertDontSee('addDocumentModal', false);
    }

    public function test_staff_upload_returns_to_the_staff_list_with_a_popup(): void
    {
        Storage::fake('public');

        $this->actingAs($this->staff)->from(route('staff.document.create'))->post(route('staff.document.store'), [
            'documentNo' => 'UP-001', 'fromOffice' => 'Finance', 'documentType' => 'Letter',
            'title' => 'Uploaded from the page', 'documentDate' => '2026-10-12',
            'file' => UploadedFile::fake()->create('letter.pdf', 50, 'application/pdf'),
        ])->assertRedirect(route('staff.documents'));

        $this->assertSame($this->staff->userID, Document::where('documentNo', 'UP-001')->firstOrFail()->ownerID);

        $this->get('/staff/documents')
            ->assertSee('Document added successfully!')
            ->assertSee('id="flashModal"', false)
            ->assertSee('Uploaded from the page');
    }

    public function test_upload_errors_come_back_to_the_upload_page_with_what_was_typed(): void
    {
        Storage::fake('public');
        $this->makeDocument('TAKEN-1', 'Existing');

        $this->actingAs($this->staff)->from(route('staff.document.create'))->post(route('staff.document.store'), [
            'documentNo' => 'TAKEN-1', 'fromOffice' => '', 'documentType' => 'Letter', 'title' => 'Typed subject',
            'file' => UploadedFile::fake()->create('notes.txt', 10, 'text/plain'),
        ])->assertRedirect(route('staff.document.create'))
          ->assertSessionHasErrors(['documentNo', 'fromOffice', 'file']);

        $this->get(route('staff.document.create'))
            ->assertSee('value="TAKEN-1"', false)
            ->assertSee('value="Typed subject"', false)
            ->assertSee('The reference number has already been taken.')
            ->assertSee('The from office field is required')
            ->assertSee('is-invalid', false);
    }

    public function test_upload_page_needs_login(): void
    {
        $this->get(route('staff.document.create'))->assertRedirect('/login');
    }

    public function test_staff_can_edit_a_document_in_a_popup(): void
    {
        Storage::fake('public');
        $doc = $this->makeDocument('D-1', 'Old title', 'Finance');

        // the Edit popup lives on the list page and is filled from the document's data
        $html = $this->actingAs($this->staff)->get('/staff/documents')->assertOk()
            ->assertSee('id="editDocumentModal"', false)
            ->assertSee('name="fromOffice"', false)
            ->assertSee('name="documentNo"', false)
            ->assertSee('enctype="multipart/form-data"', false)
            ->getContent();
        $data = $this->popupData($html);
        $this->assertSame(route('staff.document.update', $doc->documentId), $data['updateUrl']);
        $this->assertSame(
            ['documentNo' => 'D-1', 'fromOffice' => 'Finance', 'documentType' => 'Memo', 'title' => 'Old title', 'documentDate' => '2026-10-01'],
            $data['edit']
        );

        $this->from('/staff/documents')->post(route('staff.document.update', $doc->documentId), [
            'documentNo' => 'D-1',
            'fromOffice' => 'Legal Office',
            'documentType' => 'Report',
            'title' => 'New title',
            'documentDate' => '2026-10-03',
        ])->assertRedirect('/staff/documents')->assertSessionHas('success', 'Document updated successfully!');

        $doc->refresh();
        $this->assertSame('New title', $doc->title);
        $this->assertSame('Report', $doc->documentType);
        $this->assertSame('Legal Office', $doc->department->depName);
    }

    public function test_there_is_no_separate_edit_page(): void
    {
        $doc = $this->makeDocument('D-1', 'No page');

        $this->actingAs($this->staff)->get('/staff/documents/' . $doc->documentId . '/edit')->assertStatus(405);
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('staff.document.edit'));
    }

    public function test_failed_edit_reopens_the_edit_popup_with_its_errors(): void
    {
        $doc = $this->makeDocument('D-1', 'Keep me');

        $this->actingAs($this->staff)->from('/staff/documents')->post(route('staff.document.update', $doc->documentId), [
            '_editing_id' => $doc->documentId,
            'documentNo' => 'D-1', 'fromOffice' => '', 'documentType' => 'Report', 'title' => 'Typed but invalid',
        ])->assertRedirect('/staff/documents')
          ->assertSessionHasErrorsIn('editDocument', ['fromOffice']);

        $html = $this->get('/staff/documents')->getContent();
        // the Edit popup reopens for this document, with the errors and the typed values...
        $this->assertStringContainsString('action="' . route('staff.document.update', $doc->documentId) . '"', $html);
        $this->assertStringContainsString('Typed but invalid', $html);
        $this->assertStringContainsString('openEditModal', $html);
        $this->assertStringContainsString('The from office field is required', $html);
        // ...and the Add Document popup is not triggered by it
        $this->assertStringNotContainsString("getElementById('addDocumentModal')", $html);
        $this->assertSame('Keep me', $doc->fresh()->title);
    }

    public function test_staff_edit_can_replace_the_pdf(): void
    {
        Storage::fake('public');
        $doc = $this->makeDocument('D-1', 'With file');
        Storage::disk('public')->put($doc->filePath, 'old');

        $this->actingAs($this->staff)->from('/staff/documents')->post(route('staff.document.update', $doc->documentId), [
            'documentNo' => 'D-1', 'fromOffice' => 'Finance', 'documentType' => 'Memo', 'title' => 'With file',
            'file' => UploadedFile::fake()->create('new.pdf', 20, 'application/pdf'),
        ])->assertRedirect('/staff/documents');

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

        $this->actingAs($this->staff)->from('/staff/documents')->post(route('staff.document.update', $doc->documentId), [
            'documentNo' => 'D-1', 'fromOffice' => 'Finance', 'documentType' => 'Memo', 'title' => 'Edited',
        ])->assertRedirect('/staff/documents');

        $this->get('/staff/documents')
            ->assertSee('id="flashModal"', false)
            ->assertSee('Document updated successfully!')
            ->assertSee('>OK</button>', false)
            ->assertDontSee('alert-success', false)
            ->assertDontSee('>Undo</button>', false);

        // shown once only: the next page has no popup
        $this->get('/staff/documents')->assertDontSee('id="flashModal"', false);
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

    // ---- "View" popup ------------------------------------------------------------

    private function popupData(string $html, int $index = 0): array
    {
        preg_match_all('/data-doc-view="([^"]+)"/', $html, $m);

        return json_decode(html_entity_decode($m[1][$index]), true);
    }

    public function test_staff_list_has_only_a_view_button_and_an_edit_only_popup(): void
    {
        $this->makeDocument('D-1', 'Readable');

        $html = $this->actingAs($this->staff)->get('/staff/documents')->assertOk()->getContent();

        $this->assertSame(1, substr_count($html, 'title="View details"'));
        $this->assertStringNotContainsString('title="Edit Details"', $html);
        $this->assertStringNotContainsString('title="Delete"', $html);
        $this->assertStringContainsString('id="documentViewModal"', $html);
        $this->assertStringContainsString('id="documentViewBtn"', $html);
        $this->assertStringContainsString('id="documentDownloadBtn"', $html);
        // staff can edit (from the popup) but not delete
        $this->assertStringContainsString('id="documentEditBtn"', $html);
        $this->assertStringNotContainsString('id="documentDeleteForm"', $html);
        // layout: info card with labelled rows, and no document ID shown
        $this->assertStringContainsString('Document Information', $html);
        foreach (['Filename', 'Uploaded By', 'Upload Date', 'Reference No.', 'From Office', 'Description'] as $label) {
            $this->assertStringContainsString('>' . $label . '</div>', $html);
        }
        $this->assertStringNotContainsString('Document ID', $html);
    }

    public function test_popup_carries_the_document_metadata(): void
    {
        $doc = $this->makeDocument('REF-77', 'Road widening permit', 'Finance', '2026-10-01');
        $doc->forceFill([
            'filePath' => 'documents/3f2b8c1e-9a4d-4e7b-8c55-0a1b2c3d4e5f_Road Permit.pdf',
            'description' => 'Permit for the road widening project',
        ])->save();
        // stored in UTC; the popup shows Philippine time (UTC+8)
        Document::where('documentId', $doc->documentId)->update(['created_at' => '2026-10-01 02:30:00']);

        foreach ([$this->admin, $this->staff] as $user) {
            $url = $user->is($this->admin) ? '/dashboard' : '/staff/documents';
            $data = $this->popupData($this->actingAs($user)->get($url)->getContent());

            $this->assertSame($doc->documentId, $data['id']);
            $this->assertSame('REF-77', $data['referenceNo']);
            $this->assertSame('Road widening permit', $data['subject']);
            $this->assertSame('Permit for the road widening project', $data['description']);
            $this->assertSame('Finance', $data['office']);
            $this->assertSame('Memo', $data['type']);
            $this->assertSame('Road Permit.pdf', $data['fileName']);          // uuid prefix removed
            $this->assertSame('Worker Tester (worker)', $data['uploadedBy']);
            $this->assertSame('October 01, 2026 at 10:30 AM', $data['uploadedAt']);
            $this->assertSame(route('document.download', $doc->documentId), $data['downloadUrl']);
            $this->assertStringContainsString('documents/3f2b8c1e', $data['viewUrl']);
        }
    }

    public function test_popup_handles_a_document_without_a_file(): void
    {
        $doc = $this->makeDocument('D-1', 'No file');
        $doc->forceFill(['filePath' => null])->save();

        $data = $this->popupData($this->actingAs($this->admin)->get('/dashboard')->getContent());

        $this->assertNull($data['viewUrl']);
        $this->assertNull($data['downloadUrl']);
        $this->assertNull($data['fileName']);
    }

    // ---- Download -------------------------------------------------------------------

    public function test_document_can_be_downloaded_with_its_original_name(): void
    {
        Storage::fake('public');
        $doc = $this->makeDocument('D-1', 'Downloadable');
        $doc->forceFill(['filePath' => 'documents/3f2b8c1e-9a4d-4e7b-8c55-0a1b2c3d4e5f_Budget Plan.pdf'])->save();
        Storage::disk('public')->put($doc->filePath, '%PDF-1.4 test');

        foreach ([$this->admin, $this->staff] as $user) {
            $this->actingAs($user)->get(route('document.download', $doc->documentId))
                ->assertOk()
                ->assertDownload('Budget Plan.pdf');
        }
    }

    public function test_download_needs_login_and_an_existing_file(): void
    {
        Storage::fake('public');
        $doc = $this->makeDocument('D-1', 'Missing file');   // file was never stored

        $this->get(route('document.download', $doc->documentId))->assertRedirect('/login');
        $this->actingAs($this->admin)->get(route('document.download', $doc->documentId))->assertNotFound();
    }

    // ---- Who can edit / delete ---------------------------------------------------------

    public function test_staff_can_edit_but_not_delete(): void
    {
        $doc = $this->makeDocument('D-1', 'Staff cannot delete');

        $this->actingAs($this->staff)->post(route('staff.document.update', $doc->documentId), [
            'documentNo' => 'D-1', 'fromOffice' => 'Finance', 'documentType' => 'Memo', 'title' => 'Staff edit',
        ])->assertRedirect();
        $this->assertSame('Staff edit', $doc->fresh()->title);
        $this->delete(route('staff.document.delete', $doc->documentId))->assertForbidden();
        $this->post(route('staff.document.undo', $doc->documentId))->assertForbidden();
        $this->assertNotSoftDeleted($doc);
    }

    public function test_admin_can_delete_but_not_edit(): void
    {
        $doc = $this->makeDocument('D-1', 'Admin cannot edit');

        $this->actingAs($this->admin)->post(route('staff.document.update', $doc->documentId), [
            'documentNo' => 'D-1', 'fromOffice' => 'Finance', 'documentType' => 'Memo', 'title' => 'Hacked title',
        ])->assertForbidden();
        $this->assertSame('Admin cannot edit', $doc->fresh()->title);

        $this->delete(route('staff.document.delete', $doc->documentId))->assertRedirect();
        $this->assertSoftDeleted($doc);
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
