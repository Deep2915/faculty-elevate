<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\Evaluation;
use App\Models\FacultyProfile;
use App\Models\Goal;
use App\Models\WellbeingSurvey;
use App\Services\RecommendationEngineService;
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
        $goals        = Goal::where('faculty_id', $userId)->get();
        $goalsTotal   = $goals->count();
        $goalsCompleted = $goals->where('status', 'completed')->count();

        // Recent achievements (timeline)
        $achievements = Achievement::where('faculty_id', $userId)
            ->orderByDesc('date')
            ->limit(5)
            ->get();

        // Wellbeing trend (last 6 surveys)
        $wellbeingData = WellbeingSurvey::where('faculty_id', $userId)
            ->orderBy('surveyed_at')
            ->limit(6)
            ->get();

        return view('faculty.dashboard', compact(
            'profile', 'recommendations', 'evaluations',
            'goalsTotal', 'goalsCompleted', 'achievements', 'wellbeingData'
        ));
    }
}
