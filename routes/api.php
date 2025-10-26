<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AimTrainerController;
use App\Http\Controllers\Api\TypeSpeedResultController;

Route::get('/aimtrainer/leaderboard', [AimTrainerController::class, 'leaderboard']);

// Public memory leaderboard (per-difficulty)
Route::get('/memory/leaderboard', [\App\Http\Controllers\Api\MemoryResultController::class, 'leaderboard']);

Route::get('/typespeed/leaderboard', [TypeSpeedResultController::class, 'leaderboard']);

// Admin API endpoints (require auth; controllers will check is_admin)
Route::get('/admin/users', [\App\Http\Controllers\Api\AdminUserController::class, 'index']);
Route::post('/admin/users/{id}/toggle', [\App\Http\Controllers\Api\AdminUserController::class, 'toggle']);
Route::delete('/admin/users/{id}', [\App\Http\Controllers\Api\AdminUserController::class, 'destroy']);

Route::get('/admin/memory-results', [\App\Http\Controllers\Api\AdminMemoryController::class, 'index']);
Route::delete('/admin/memory-results/{id}', [\App\Http\Controllers\Api\AdminMemoryController::class, 'destroy']);

// Combined admin results endpoint (supports ?game=memory|typespeed|aimtrainer)
Route::get('/admin/results', [\App\Http\Controllers\Api\AdminResultsController::class, 'index']);
Route::delete('/admin/results/{game}/{id}', [\App\Http\Controllers\Api\AdminResultsController::class, 'destroy']);

Route::middleware(['auth:sanctum', 'web'])->group(function () {
    Route::post('/aimtrainer/result', [AimTrainerController::class, 'store']);
    Route::post('/typespeed/result', [TypeSpeedResultController::class, 'store']);
    Route::post('/memory/result', [\App\Http\Controllers\Api\MemoryResultController::class, 'store']);
    Route::get('/memory/personal', [\App\Http\Controllers\Api\MemoryResultController::class, 'personal']);
});
