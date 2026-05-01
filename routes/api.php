<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\TaskCommentController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (\Illuminate\Http\Request $request) {
        return $request->user()->load('role');
    });

    Route::apiResource('projects', ProjectController::class);
    Route::post('projects/{project}/invite', [ProjectController::class, 'invite']);
    
    Route::get('tasks/backlog', [TaskController::class, 'backlog']);
    Route::post('tasks/{task}/claim', [TaskController::class, 'claim']);
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus']);
    Route::post('tasks/reorder', [TaskController::class, 'reorder']);
    Route::get('tasks/{task}/comments', [TaskCommentController::class, 'index']);
    Route::post('tasks/{task}/comments', [TaskCommentController::class, 'store']);
    Route::delete('tasks/comments/{comment}', [TaskCommentController::class, 'destroy']);
    
    Route::get('tasks/{task}/history', [TaskController::class, 'history']);
    
    Route::apiResource('tasks', TaskController::class);
});

Route::get('/test', function () {
    return response()->json(['status' => 'ok']);
});