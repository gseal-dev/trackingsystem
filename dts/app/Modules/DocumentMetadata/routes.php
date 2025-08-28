<?php

use Illuminate\Support\Facades\Route;
use App\Modules\DocumentMetadata\Controllers\DocumentController;
use App\Modules\DocumentMetadata\Controllers\Api\DocumentApiController;

// Web routes
Route::group(['prefix' => '', 'middleware' => 'web'], function () {
    Route::get('/docureg', [DocumentController::class, 'create'])->name('docuReg');
    Route::post('/docureg', [DocumentController::class, 'store'])->name('docuReg.post');
});

// API routes
Route::group(['prefix' => 'api/documents', 'middleware' => ['api', 'auth:sanctum']], function () {
    Route::get('/', [DocumentApiController::class, 'index']);
    Route::post('/', [DocumentApiController::class, 'store']);
    Route::get('/{id}', [DocumentApiController::class, 'show']);
});