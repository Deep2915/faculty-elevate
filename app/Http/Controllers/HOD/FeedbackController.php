<?php

namespace App\Http\Controllers\HOD;

use App\Http\Controllers\Controller;
use App\Models\FacultyProfile;
use App\Models\StudentFeedback;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $faculties = User::where('role', 'faculty')->orderBy('name')->get();

        $feedbackStats = $faculties->map(function (User $faculty) {
            $scores = StudentFeedback::computeAverageScores((string) $faculty->id);
            $profile = FacultyProfile::firstWhere('user_id', (string) $faculty->id);
            return [
                'faculty'       => $faculty,
                'profile'       => $profile,
                'scores'        => $scores,
                'feedback_token'=> $profile?->feedback_token,
                'link'          => $profile?->feedback_token
                    ? route('feedback.show', $profile->feedback_token)
                    : null,
            ];
        });

        return view('hod.feedback.index', compact('feedbackStats'));
    }

    public function generateLink(Request $request, string $userId): RedirectResponse
    {
        $user = User::findOrFail($userId);
        $profile = FacultyProfile::firstOrCreate(
            ['user_id' => $userId],
            ['user_id' => $userId]
        );
        $profile->feedback_token = Str::uuid()->toString();
        $profile->save();

        return redirect()->route('hod.feedback.index')
            ->with('status', "Feedback link generated for {$user->name}.");
    }
}
