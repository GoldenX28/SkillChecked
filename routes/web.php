<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Api\TypeSpeedResultController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
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

Route::get('/leaderboard', function () {
    return Inertia::render('LeaderboardPage');
})->name('leaderboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile/my-runs', function () {
        return Inertia::render('Profile/MyRuns');
    })->name('profile.my-runs');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Admin area (basic auth protection). Also ensure only users with is_admin can access.
    Route::get('/admin', function () {
        if (!Auth::check() || !Auth::user()?->is_admin) {
            abort(403);
        }
        return Inertia::render('Admin/Dashboard');
    })->name('admin.dashboard');
    
    // Stub admin routes for the sidebar links (placeholders)
    Route::get('/admin/users', function () {
        if (!Auth::check() || !Auth::user()?->is_admin) {
            abort(403);
        }
        return Inertia::render('Admin/Users');
    })->name('admin.users');
    
    Route::get('/admin/results', function () {
        if (!Auth::check() || !Auth::user()?->is_admin) {
            abort(403);
        }
        return Inertia::render('Admin/Results');
    })->name('admin.results');
    
    Route::get('/admin/settings', function () {
        if (!Auth::check() || !Auth::user()?->is_admin) {
            abort(403);
        }
        return Inertia::render('Admin/Settings');
    })->name('admin.settings');
});

require __DIR__.'/auth.php';
