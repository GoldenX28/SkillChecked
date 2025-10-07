<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AimTrainerController;
use App\Http\Controllers\Api\TypeSpeedResultController;

Route::post('/aimtrainer/result', [AimTrainerController::class, 'store']);
Route::get('/aimtrainer/leaderboard', [AimTrainerController::class, 'leaderboard']);

Route::middleware(['auth:sanctum', 'web'])->group(function () {
    Route::post('/typespeed/result', [TypeSpeedResultController::class, 'store']);
    Route::get('/typespeed/leaderboard', [TypeSpeedResultController::class, 'leaderboard']);
});
