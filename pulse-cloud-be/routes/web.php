<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('unauthorized', [AuthController::class, 'unauthorized'])->name('login');
