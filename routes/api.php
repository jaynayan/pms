<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirect']);
Route::get('/auth/{provider}/callback', [AuthController::class, 'callback']);

use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user()->load('role');
    });

    Route::apiResource('projects', ProjectController::class);
    
    Route::get('tasks/backlog', [TaskController::class, 'backlog']);
    Route::post('tasks/{task}/claim', [TaskController::class, 'claim']);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::apiResource('tasks', TaskController::class);
});

Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});