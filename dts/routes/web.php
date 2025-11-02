<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\DocumentRegistrationController;
use App\Http\Controllers\Admin\DocumentRoutingController;
use App\Http\Controllers\Staff\StaffDocumentController;
use App\Http\Controllers\DocumentOwner\DocumentOwnerController;
use App\Http\Controllers\Auditor\AuditorController;



Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('auth');

Route::get('/admin/user-management', [UserManagementController::class, 'index'])->name('admin.userManagement');
Route::post('/admin/user-management/add', [UserManagementController::class, 'add'])->name('admin.userManagement.add');
Route::post('/admin/user-management/edit/{user}', [UserManagementController::class, 'edit'])->name('admin.userManagement.edit');
Route::post('/admin/user-management/delete/{user}', [UserManagementController::class, 'delete'])->name('admin.userManagement.delete');

Route::get('/admin/user-management/add', [UserManagementController::class, 'addForm'])->name('admin.userManagement.addForm');
Route::post('/admin/user-management/add', [UserManagementController::class, 'add'])->name('admin.userManagement.add');

Route::get('/admin/user-management/edit/{user}', [UserManagementController::class, 'editForm'])->name('admin.userManagement.editForm');
Route::post('/admin/user-management/edit/{user}', [UserManagementController::class, 'edit'])->name('admin.userManagement.edit');

Route::get('/admin/document-registration', [DocumentRegistrationController::class, 'showForm'])->name('admin.documentRegistration');
Route::post('/admin/document-registration', [DocumentRegistrationController::class, 'register'])->name('admin.documentRegistration.submit');
Route::get('/admin/user-search', [UserManagementController::class, 'searchUser'])->name('admin.userSearch');

Route::get('/admin/send-document', [DocumentRoutingController::class, 'listDocuments'])->name('admin.sendDocumentList');
Route::get('/admin/send-document/{document}', [DocumentRoutingController::class, 'showSendForm'])->name('admin.sendDocumentForm');
Route::post('/admin/send-document/{document}', [DocumentRoutingController::class, 'send'])->name('admin.sendDocument');

Route::get('/staff/documents', [StaffDocumentController::class, 'index'])->name('staff.documents');
Route::post('/staff/documents/{document}/process', [StaffDocumentController::class, 'processDocument'])->name('staff.processDocument');
Route::post('/staff/documents/{document}/route', [StaffDocumentController::class, 'routeDocument'])->name('staff.routeDocument');
Route::post('/staff/documents/{document}/process-route', [StaffDocumentController::class, 'processAndRouteDocument'])->name('staff.processAndRouteDocument');
Route::get('/staff/documents', [StaffDocumentController::class, 'documents'])->name('staff.documents');
Route::get('/staff/history', [StaffDocumentController::class, 'history'])->name('staff.history');
Route::get('/staff/documents/{document}/process', [StaffDocumentController::class, 'processDocumentForm'])->name('staff.processDocumentForm');

Route::get('/admin/processed-documents', [DocumentRoutingController::class, 'processedDocuments'])->name('admin.processedDocuments');

Route::get('/document-owner/submitted-documents', [\App\Http\Controllers\DocumentOwner\DocumentOwnerController::class, 'submittedDocuments'])->name('documentOwner.submittedDocuments');
Route::get('/document-owner/pending-documents', [\App\Http\Controllers\DocumentOwner\DocumentOwnerController::class, 'pendingDocuments'])->name('documentOwner.pendingDocuments');
Route::get('/document-owner/completed-documents', [\App\Http\Controllers\DocumentOwner\DocumentOwnerController::class, 'completedDocuments'])->name('documentOwner.completedDocuments');

Route::get('/auditor/document-transactions', [AuditorController::class, 'documentTransactions'])->name('auditor.documentTransactions');
Route::get('/auditor/document-transactions/export', [AuditorController::class, 'exportDocumentTransactions'])->name('auditor.documentTransactions.export');

Route::get('/', function () {
    return view('welcome');
});
