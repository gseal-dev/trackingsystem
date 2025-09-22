<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Authentication\Controllers\AuthController;
use App\Modules\Authentication\Controllers\AdminUserController;


Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        if ($user->roleID == 1) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->roleID == 2) {
            return redirect()->route('owner.dashboard');
        } elseif ($user->roleID == 3) {
            return redirect()->route('staff.dashboard');
        }
    }
    return view('welcome');
})->name('home');

Route::get('/admin/document-owners/search', [App\Modules\Authentication\Controllers\AdminUserController::class, 'searchDocumentOwners'])->name('admin.document-owners.search');

// Auth routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/registration', [AuthController::class, 'registration'])->name('registration');
Route::post('/registration', [AuthController::class, 'registrationPost'])->name('registration.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin user management
Route::middleware(['web', 'auth', 'is_admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('/users/create', [AdminUserController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminUserController::class, 'store'])->name('admin.users.store');
});

// Admin Dashboard (includes document registration)
Route::get('/admin/dashboard', function () {
    return view('Admin.adminDashboard');
})->name('admin.dashboard')->middleware(['auth', 'is_admin']);

// Document Owner Dashboard
Route::get('/owner/dashboard', function () {
    return view('dashboard', ['role' => 'owner']);
})->name('owner.dashboard')->middleware(['auth']);

// Staff Dashboard
Route::get('/staff/dashboard', function () {
    return view('dashboard', ['role' => 'staff']);
})->name('staff.dashboard')->middleware(['auth']);