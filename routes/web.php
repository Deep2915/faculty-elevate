<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Faculty;
use App\Http\Controllers\HOD;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

require __DIR__.'/auth.php';

Route::middleware('auth')->get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'hod' => redirect()->route('hod.dashboard'),
        default => redirect()->route('faculty.dashboard'),
    };
})->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', Admin\UserManagementController::class);
    Route::get('/weights', [Admin\WeightConfigController::class, 'edit'])->name('weights.edit');
    Route::put('/weights', [Admin\WeightConfigController::class, 'update'])->name('weights.update');
    Route::resource('workshops', Admin\WorkshopController::class);
    Route::resource('badges', Admin\BadgeController::class);
});

Route::middleware(['auth', 'role:hod'])->prefix('hod')->name('hod.')->group(function (): void {
    Route::get('/dashboard', [HOD\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('evaluations', HOD\EvaluationController::class);
    Route::get('/leaderboard', [HOD\LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/faculty/{user}/report', [HOD\ReportController::class, 'download'])->name('report.download');
});

Route::middleware(['auth', 'role:faculty'])->prefix('faculty')->name('faculty.')->group(function (): void {
    Route::get('/dashboard', [Faculty\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/roadmap', [Faculty\GoalController::class, 'roadmap'])->name('roadmap');
    Route::resource('goals', Faculty\GoalController::class);
    Route::resource('achievements', Faculty\AchievementController::class);
    Route::get('/workshops', [Faculty\WorkshopController::class, 'index'])->name('workshops.index');
    Route::post('/workshops/{workshop}/register', [Faculty\WorkshopController::class, 'register'])->name('workshops.register');
    Route::get('/leaderboard', [Faculty\LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/wellbeing', [Faculty\WellbeingController::class, 'index'])->name('wellbeing');
    Route::post('/wellbeing', [Faculty\WellbeingController::class, 'store'])->name('wellbeing.store');
    Route::get('/profile', [Faculty\ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [Faculty\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/report/download', [Faculty\ReportController::class, 'download'])->name('report.download');
});
