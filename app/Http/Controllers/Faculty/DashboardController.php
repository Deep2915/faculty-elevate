<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\ClassLog;
use App\Models\Evaluation;
use App\Models\FacultyProfile;
use App\Models\Goal;
use App\Models\TimetableEntry;
use App\Models\WellbeingSurvey;
use App\Models\Workshop;
use App\Services\RecommendationEngineService;
use Carbon\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(RecommendationEngineService $recommendationEngineService): View
    {
        $userId  = (string) auth()->id();
        $profile = FacultyProfile::firstWhere('user_id', $userId);

        $recommendations = $profile
            ? $recommendationEngineService->getRecommendations($profile)
            : ['workshops' => [], 'certifications' => [], 'skill_gaps' => []];

        // Recent evaluations
        $evaluations = Evaluation::where('faculty_id', $userId)
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        // Goals summary
        $goals          = Goal::where('faculty_id', $userId)->get();
        $goalsTotal     = $goals->count();
        $goalsCompleted = $goals->where('status', 'completed')->count();
        $goalsActive    = $goals->where('status', 'active')->count();

        // Recent achievements (timeline)
        $achievements = Achievement::where('faculty_id', $userId)
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        // Wellbeing trend (last 8 surveys)
        $wellbeingData = WellbeingSurvey::where('faculty_id', $userId)
            ->orderBy('surveyed_at')
            ->limit(8)
            ->get();

        // Today's timetable
        $todayName    = Carbon::now()->format('l');
        $todayClasses = TimetableEntry::where('faculty_id', $userId)
            ->where('is_active', true)
            ->where('day_of_week', $todayName)
            ->orderBy('time_slot')
            ->get();

        // Attendance score (last 120 days)
        $attendanceData = ClassLog::computeScore(
            $userId,
            Carbon::now()->subDays(120)->toDateString()
        );

        // Upcoming workshops (not yet registered, max 3)
        $upcomingWorkshops = Workshop::where('status', 'upcoming')
            ->orderBy('schedule_date')
            ->limit(3)
            ->get();

        return view('faculty.dashboard', compact(
            'profile', 'recommendations', 'evaluations',
            'goalsTotal', 'goalsCompleted', 'goalsActive',
            'achievements', 'wellbeingData',
            'todayClasses', 'todayName',
            'attendanceData', 'upcomingWorkshops'
        ));
    }
}
