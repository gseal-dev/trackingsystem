<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DocumentRegistrationController;
use App\Http\Controllers\Staff\StaffDocumentController;

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
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

// User Management - Per Role
Route::middleware(['auth'])->group(function () {
    Route::get('/admin/user-management/admins', [UserManagementController::class, 'admins'])->name('admin.userManagement.admins');
    Route::get('/admin/user-management/staffs', [UserManagementController::class, 'staffs'])->name('admin.userManagement.staffs');
    Route::get('/admin/user-management/{type}/add', [UserManagementController::class, 'addForm'])
        ->where('type', 'admins')
        ->name('admin.userManagement.addForm');
    Route::post('/admin/user-management/{type}/add', [UserManagementController::class, 'add'])
        ->where('type', 'admins')
        ->name('admin.userManagement.add');
    Route::get('/admin/user-management/{type}/edit/{user}', [UserManagementController::class, 'editForm'])->name('admin.userManagement.editForm');
    Route::post('/admin/user-management/{type}/edit/{user}', [UserManagementController::class, 'edit'])->name('admin.userManagement.edit');
    Route::post('/admin/user-management/{type}/delete/{user}', [UserManagementController::class, 'delete'])->name('admin.userManagement.delete');
});

// Document Registration (Admin only)
Route::get('/admin/document-registration', [DocumentRegistrationController::class, 'showForm'])->name('admin.documentRegistration');
Route::post('/admin/document-registration', [DocumentRegistrationController::class, 'register'])->name('admin.documentRegistration.submit');
Route::get('/admin/user-search', [UserManagementController::class, 'searchUser'])->name('admin.userSearch');

// Staff
Route::get('/staff/history', [StaffDocumentController::class, 'history'])->name('staff.history');
Route::get('/staff/documents', [StaffDocumentController::class, 'documents'])->name('staff.documents');
Route::get('/staff/documents/create', [StaffDocumentController::class, 'create'])->name('staff.document.create');
Route::post('/staff/documents/create', [StaffDocumentController::class, 'store'])->name('staff.document.store');
Route::get('/staff/documents/{document}/edit', [StaffDocumentController::class, 'edit'])->name('staff.document.edit');
Route::post('/staff/documents/{document}/edit', [StaffDocumentController::class, 'update'])->name('staff.document.update');
Route::delete('/staff/documents/{document}', [StaffDocumentController::class, 'delete'])->name('staff.document.delete');
Route::post('/staff/documents/{id}/undo', [StaffDocumentController::class, 'undoDelete'])->name('staff.document.undo');
Route::post('/staff/documents/import', [StaffDocumentController::class, 'import'])->name('staff.document.import');
Route::get('/staff/documents/template', [StaffDocumentController::class, 'downloadTemplate'])->name('staff.document.template');
Route::get('/staff/user-search', [UserManagementController::class, 'searchUser'])->name('staff.userSearch');