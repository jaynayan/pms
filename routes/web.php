<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
    Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
});

Route::get('/auth/{provider}/redirect', [App\Http\Controllers\AuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [App\Http\Controllers\AuthController::class, 'callback']);

// Development testing routes
Route::get('/test/login', [App\Http\Controllers\TestController::class, 'showLoginForm']);
Route::post('/test/login', [App\Http\Controllers\TestController::class, 'login']);
