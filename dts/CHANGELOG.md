# Changelog

## [Unreleased] - 2026-10-07

### Code Cleanup and Role System Simplification

#### Role System Changes
- Simplified role system to only two roles: Admin and Staff
- Removed DocumentOwner and Auditor roles from the system
- Admin role retains exclusive user management privileges
- Staff role limited to file upload and edit operations only

#### Files Modified

**Database Seeders**
- `database/seeders/RoleSeeder.php`: 
  - Removed DocumentOwner and Auditor role entries
  - Kept only Admin (roleID: 1) and Staff (roleID: 2) roles

**Controllers**
- `app/Http/Controllers/Admin/UserManagementController.php`:
  - Removed `owners()` and `auditors()` methods
  - Removed DocumentOwner and Auditor references in `addForm()`, `editForm()`, `edit()`, and `delete()` methods
  - Updated role queries to only reference Admin and Staff roles
- Removed entire controller directories:
  - `app/Http/Controllers/Auditor/`
  - `app/Http/Controllers/DocumentOwner/`
- Removed:
  - `app/Http/Controllers/Admin/DocumentRoutingController.php`
- `app/Http/Controllers/Staff/StaffDocumentController.php`:
  - Removed `processAndRouteDocument()`, `routeDocument()`, and `processDocumentForm()` methods
  - Removed processing and routing functionality for Staff role

**Routes**
- `routes/web.php`:
  - Removed routes for DocumentOwner and Auditor controllers
  - Kept only User Management routes for Admin and Staff
  - Kept Document Registration routes (Admin only)
  - Kept Staff document management routes

**Views**
- Removed entire view directories:
  - `resources/views/auditor/`
  - `resources/views/documentOwner/`
  - `resources/views/admin/DocumentRouting/`
  - `resources/views/staff/` (process_document.blade.php)
- Removed specific view files:
  - `resources/views/admin/UserManagement/auditors.blade.php`
  - `resources/views/admin/documents.blade.php`
  - `resources/views/admin/admin.blade.php`
  - `resources/views/staff/process_document.blade.php`
- Updated:
  - `resources/views/layouts/app.blade.php`:
    - Removed Auditor and DocumentOwner navigation links from horizontal navbar
  - `resources/views/admin/UserManagement/userManagement.blade.php`:
    - Removed Document Owners and Auditors cards, keeping only Admins and Staff
  - `app/Http/Controllers/DashboardController.php`:
    - Removed DocumentOwner and Auditor cases from the dashboard switch statement

**Additional Fixes**
- Fixed "Route [admin.documents] not found" error by:
  - Removing references to non-existent admin.documents route
  - Clearing Laravel view and route caches
  - Ensuring all route references point to existing routes

### Summary
The tracking system has been simplified to implement only the essential two-role functionality:
- **Admin**: Exclusive rights to manage and add users
- **Staff**: Limited to file upload and edit metadata only (cannot process/route documents or change statuses)

All unnecessary routing/status components and extra roles (DocumentOwner, Auditor) have been completely removed from the codebase, including:
- Removal of document processing and routing functionality for Staff role
- Elimination of status changing capabilities for Staff
- Removal of all related views, routes, and controller methods

Additionally, fixed UI color contrast issues in light mode by changing the primary text and button colors to black for better readability.