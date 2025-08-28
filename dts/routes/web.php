<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    if (Session::has('user_data')) {
        return redirect()->route('docuReg');
    }
    return view('welcome');
})->name('home');

// Module routes are loaded automatically via service providers