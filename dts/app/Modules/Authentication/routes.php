<?php

use Illuminate\Support\Facades\Route;
use App\Modules\Authentication\Controllers\AuthController;
use App\Modules\Authentication\Controllers\Api\AuthApiController;

// Web routes
Route::group(['prefix' => '', 'middleware' => 'web'], function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'loginPost'])->name('login.post');
    Route::get('/registration', [AuthController::class, 'registration'])->name('registration');
    Route::post('/registration', [AuthController::class, 'registrationPost'])->name('registration.post');
    Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
});

// API routes
Route::group(['prefix' => 'api/auth', 'middleware' => 'api'], function () {
    Route::post('/login', [AuthApiController::class, 'login']);
    Route::post('/register', [AuthApiController::class, 'register']);
    Route::post('/logout', [AuthApiController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthApiController::class, 'user'])->middleware('auth:sanctum');
});