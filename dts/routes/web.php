<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthManager;

Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('docuReg');
    }
    return view('welcome');
})->name('home');



Route::get('/login', [AuthManager::class, 'login'])->name('login');
Route::post('/login', [AuthManager::class, 'loginPost'])->name('login.post');
Route::get('/registration', [AuthManager::class, 'registration'])->name('registration');
Route::post('/registration', [AuthManager::class, 'registrationPost'])->name('registration.post');
Route::get('/logout', [AuthManager::class, 'logout'])->name('logout');
Route::get('/docureg', [AuthManager::class, 'documentRegistration'])->name('docuReg')->middleware('auth');
Route::post('/docureg', [AuthManager::class, 'documentRegistrationPost'])->name('docuReg.post')->middleware('auth');