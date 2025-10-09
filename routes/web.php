<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\TypeSpeedResultController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
})->name('welcome');

Route::get('/games/aimtraingame', function () {
    return Inertia::render('Games/AimTrainGame');
});

Route::get('/games/typespeed', function () {
    return Inertia::render('Games/TypeSpeedGame');
})->name('type-speed-game');

Route::get('/games/memorygame', function () {
    return Inertia::render('Games/MemoryGame');
})->name('memory-game');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/my-runs', function () {
        return Inertia::render('Profile/MyRuns');
    })->name('profile.my-runs');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
