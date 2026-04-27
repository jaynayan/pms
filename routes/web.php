<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [App\Http\Controllers\AuthController::class, 'logout']);

Route::get('/auth/{provider}/redirect', [App\Http\Controllers\AuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [App\Http\Controllers\AuthController::class, 'callback']);
