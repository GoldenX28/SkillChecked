<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AimTrainerController;
use App\Http\Controllers\Api\TypeSpeedResultController;

Route::get('/aimtrainer/leaderboard', [AimTrainerController::class, 'leaderboard']);

// Public memory leaderboard (per-difficulty)
Route::get('/memory/leaderboard', [\App\Http\Controllers\Api\MemoryResultController::class, 'leaderboard']);

Route::get('/typespeed/leaderboard', [TypeSpeedResultController::class, 'leaderboard']);

// Admin API endpoints. GET list endpoints are public (admin web route protects UI),
// but mutation endpoints require session-based auth. Use both `web` and `auth:sanctum`
// middleware so Sanctum cookie-based authentication works correctly.
Route::get('/admin/users', [\App\Http\Controllers\Api\AdminUserController::class, 'index']);
Route::post('/admin/users/{id}/toggle', [\App\Http\Controllers\Api\AdminUserController::class, 'toggle'])->middleware(['web','auth:sanctum']);
Route::delete('/admin/users/{id}', [\App\Http\Controllers\Api\AdminUserController::class, 'destroy'])->middleware(['web','auth:sanctum']);

Route::get('/admin/memory-results', [\App\Http\Controllers\Api\AdminMemoryController::class, 'index']);
Route::delete('/admin/memory-results/{id}', [\App\Http\Controllers\Api\AdminMemoryController::class, 'destroy'])->middleware(['web','auth:sanctum']);

// Combined admin results endpoint (supports ?game=memory|typespeed|aimtrainer)
// GET is intentionally public so the admin UI can fetch results. DELETE requires
// session auth (web + auth:sanctum).
Route::get('/admin/results', [\App\Http\Controllers\Api\AdminResultsController::class, 'index']);
Route::delete('/admin/results/{game}/{id}', [\App\Http\Controllers\Api\AdminResultsController::class, 'destroy'])->middleware(['web','auth:sanctum']);

Route::middleware(['auth:sanctum', 'web'])->group(function () {
    Route::post('/aimtrainer/result', [AimTrainerController::class, 'store']);
    Route::post('/typespeed/result', [TypeSpeedResultController::class, 'store']);
    Route::post('/memory/result', [\App\Http\Controllers\Api\MemoryResultController::class, 'store']);
    Route::get('/memory/personal', [\App\Http\Controllers\Api\MemoryResultController::class, 'personal']);
});
