<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Staff\StaffDocumentController;
use App\Http\Controllers\Staff\StaffSettingsController;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('auth.login');
});

// Auth & Dashboard
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// One-time setup: create the first admin account on a fresh deploy.
Route::middleware('guest')->group(function () {
    Route::get('/setup', [SetupController::class, 'showSetup'])->name('setup');
    Route::post('/setup', [SetupController::class, 'createAdmin'])->name('setup.store');
});
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// User Management - Per Role
Route::middleware(['auth', 'role:Admin'])->group(function () {
    Route::get('/admin/user-management/admins', [UserManagementController::class, 'admins'])->name('admin.userManagement.admins');
    Route::get('/admin/user-management/history', [UserManagementController::class, 'adminHistory'])->name('admin.userManagement.history');
    Route::get('/admin/user-management/export', [UserManagementController::class, 'exportUsers'])->middleware('role:Admin')->name('admin.userManagement.export');
    Route::post('/admin/user-management/{type}/add', [UserManagementController::class, 'add'])
        ->where('type', 'admins')
        ->name('admin.userManagement.add');
    Route::post('/admin/user-management/{type}/edit/{user}', [UserManagementController::class, 'edit'])->name('admin.userManagement.edit');
    Route::post('/admin/user-management/{type}/delete/{user}', [UserManagementController::class, 'delete'])->name('admin.userManagement.delete');

    // Documents list (admin-only page)
    Route::get('/admin/documents', [DashboardController::class, 'documents'])->name('admin.documents');

    // Save the default password applied to newly created users
    Route::post('/admin/user-management/default-password', [UserManagementController::class, 'saveDefaultPassword'])->name('admin.userManagement.defaultPassword.save');
});

// Document routes (login required)
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/user-search', [UserManagementController::class, 'searchUser'])->name('admin.userSearch');

    // Staff
    Route::get('/documents/{document}/download', [StaffDocumentController::class, 'download'])->name('document.download');
    Route::get('/staff/history', [StaffDocumentController::class, 'history'])->name('staff.history');
    Route::get('/staff/history/export', [StaffDocumentController::class, 'exportHistory'])->name('staff.history.export');
    Route::get('/staff/documents', [StaffDocumentController::class, 'documents'])->name('staff.documents');
    Route::get('/staff/documents/create', [StaffDocumentController::class, 'create'])->name('staff.document.create');
    Route::post('/staff/documents/create', [StaffDocumentController::class, 'store'])->name('staff.document.store');

    // Staff can edit documents (in a popup) but not delete them
    Route::middleware('role:Staff')->group(function () {
        Route::post('/staff/documents/{document}/edit', [StaffDocumentController::class, 'update'])->name('staff.document.update');

        // Replace just the attached file (upload a new version) without editing metadata.
        Route::post('/staff/documents/{document}/upload-version', [StaffDocumentController::class, 'uploadVersion'])->name('staff.document.uploadVersion');

        // Staff settings: staff can change their own password (nothing else)
        Route::get('/staff/settings', [StaffSettingsController::class, 'show'])->name('staff.settings');
        Route::post('/staff/settings/password', [StaffSettingsController::class, 'updatePassword'])->name('staff.settings.password');
    });

    // Admins can delete documents (and undo a delete) but not edit them
    Route::middleware('role:Admin')->group(function () {
        Route::delete('/staff/documents/{document}', [StaffDocumentController::class, 'delete'])->name('staff.document.delete');
        Route::post('/staff/documents/{id}/undo', [StaffDocumentController::class, 'undoDelete'])->name('staff.document.undo');
        Route::get('/admin/documents/export', [DashboardController::class, 'exportDocuments'])->name('admin.documents.export');
    });

    Route::post('/staff/documents/import', [StaffDocumentController::class, 'import'])->name('staff.document.import');
    Route::get('/staff/documents/template', [StaffDocumentController::class, 'downloadTemplate'])->name('staff.document.template');
    Route::get('/staff/documents/export', [StaffDocumentController::class, 'exportDocuments'])->name('staff.documents.export');
    Route::get('/staff/user-search', [UserManagementController::class, 'searchUser'])->name('staff.userSearch');
});
