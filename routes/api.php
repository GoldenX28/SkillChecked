<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AimTrainerController;
use App\Http\Controllers\Api\TypeSpeedResultController;

Route::post('/aimtrainer/result', [AimTrainerController::class, 'store']);
Route::get('/aimtrainer/leaderboard', [AimTrainerController::class, 'leaderboard']);

// Public memory leaderboard (per-difficulty)
Route::get('/memory/leaderboard', [\App\Http\Controllers\Api\MemoryResultController::class, 'leaderboard']);

Route::middleware(['auth:sanctum', 'web'])->group(function () {
    Route::post('/typespeed/result', [TypeSpeedResultController::class, 'store']);
    Route::get('/typespeed/leaderboard', [TypeSpeedResultController::class, 'leaderboard']);
    Route::post('/memory/result', [\App\Http\Controllers\Api\MemoryResultController::class, 'store']);
    Route::get('/memory/personal', [\App\Http\Controllers\Api\MemoryResultController::class, 'personal']);
});
