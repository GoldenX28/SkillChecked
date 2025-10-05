<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AimTrainerController;

Route::post('/aimtrainer/result', [AimTrainerController::class, 'store']);
Route::get('/aimtrainer/leaderboard', [AimTrainerController::class, 'leaderboard']);