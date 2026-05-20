<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\FacultyProfile;
use App\Models\StudentFeedback;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(): View
    {
        $userId  = (string) auth()->id();
        $profile = FacultyProfile::firstWhere('user_id', $userId);
        $scores  = StudentFeedback::computeAverageScores($userId);

        // Recent comments (anonymised)
        $recentFeedbacks = StudentFeedback::where('faculty_id', $userId)
            ->whereNotNull('comment')
            ->where('comment', '!=', '')
            ->orderByDesc('submitted_at')
            ->limit(10)
            ->get();

        $feedbackLink = $profile?->feedback_token
            ? route('feedback.show', $profile->feedback_token)
            : null;

        return view('faculty.feedback', compact('scores', 'recentFeedbacks', 'feedbackLink', 'profile'));
    }
}
