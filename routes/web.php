<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Faculty;
use App\Http\Controllers\HOD;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Public\StudentFeedbackController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));

require __DIR__.'/auth.php';

// ── Public Student Feedback (no auth required) ──────────────────────────
Route::get('/feedback/{token}', [StudentFeedbackController::class, 'show'])->name('feedback.show');
Route::post('/feedback/{token}', [StudentFeedbackController::class, 'store'])->name('feedback.store');
Route::get('/feedback/{token}/thanks', [StudentFeedbackController::class, 'thanks'])->name('feedback.thanks');

// ── Authenticated redirect ───────────────────────────────────────────────
Route::middleware('auth')->get('/dashboard', function () {
    /** @var \App\Models\User $user */
    $user = auth()->user();

    return match ($user->role) {
        'admin' => redirect()->route('admin.dashboard'),
        'hod'   => redirect()->route('hod.dashboard'),
        default => redirect()->route('faculty.dashboard'),
    };
})->name('dashboard');

Route::middleware('auth')->group(function (): void {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Admin ────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', Admin\UserManagementController::class);
    Route::get('/weights', [Admin\WeightConfigController::class, 'edit'])->name('weights.edit');
    Route::put('/weights', [Admin\WeightConfigController::class, 'update'])->name('weights.update');
    Route::resource('workshops', Admin\WorkshopController::class);
    Route::resource('badges', Admin\BadgeController::class);
});

// ── HOD ─────────────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:hod'])->prefix('hod')->name('hod.')->group(function (): void {
    Route::get('/dashboard', [HOD\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('evaluations', HOD\EvaluationController::class);
    Route::get('/leaderboard', [HOD\LeaderboardController::class, 'index'])->name('leaderboard');
    Route::get('/faculty/{user}/report', [HOD\ReportController::class, 'download'])->name('report.download');

    // Attendance (timetable-based)
    Route::get('/attendance', [HOD\AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/log', [HOD\AttendanceController::class, 'addLog'])->name('attendance.add-log');
    Route::put('/attendance/log/{id}/override', [HOD\AttendanceController::class, 'overrideLog'])->name('attendance.override');
    Route::delete('/attendance/log/{id}', [HOD\AttendanceController::class, 'destroyLog'])->name('attendance.destroy');

    // Student Feedback
    Route::get('/feedback', [HOD\FeedbackController::class, 'index'])->name('feedback.index');
});

// ── Faculty ──────────────────────────────────────────────────────────────
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

    // Attendance (redirect to timetable)
    Route::get('/attendance', [Faculty\AttendanceController::class, 'index'])->name('attendance');

    // Timetable & class marking
    Route::get('/timetable', [Faculty\TimetableController::class, 'index'])->name('timetable');
    Route::post('/timetable', [Faculty\TimetableController::class, 'storeTimetable'])->name('timetable.store');
    Route::delete('/timetable/{id}', [Faculty\TimetableController::class, 'destroyTimetable'])->name('timetable.destroy');
    Route::post('/timetable/mark', [Faculty\TimetableController::class, 'markClass'])->name('timetable.mark');

    // Student Feedback (view own)
    Route::get('/feedback', [Faculty\FeedbackController::class, 'index'])->name('feedback');
});
