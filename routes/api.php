<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AimTrainerController;
use App\Http\Controllers\Api\TypeSpeedResultController;

Route::post('/aimtrainer/result', [AimTrainerController::class, 'store']);
Route::get('/aimtrainer/leaderboard', [AimTrainerController::class, 'leaderboard']);

Route::middleware("api")->get('/typespeed/leaderboard', [TypeSpeedResultController::class, 'leaderboard']);

Route::middleware('auth:sanctum')->post('typespeed/results', [TypeSpeedResultController::class, 'store']);
