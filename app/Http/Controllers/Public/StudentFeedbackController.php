<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\FacultyProfile;
use App\Models\StudentFeedback;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentFeedbackController extends Controller
{
    public function show(string $token): View
    {
        $profile = FacultyProfile::where('feedback_token', $token)->firstOrFail();
        $faculty = User::findOrFail($profile->user_id);

        return view('public.student_feedback', compact('faculty', 'profile', 'token'));
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $profile = FacultyProfile::where('feedback_token', $token)->firstOrFail();

        $data = $request->validate([
            'scores.clarity'       => ['required', 'numeric', 'min:0', 'max:1'],
            'scores.communication' => ['required', 'numeric', 'min:0', 'max:1'],
            'scores.punctuality'   => ['required', 'numeric', 'min:0', 'max:1'],
            'scores.engagement'    => ['required', 'numeric', 'min:0', 'max:1'],
            'comment'              => ['nullable', 'string', 'max:1000'],
        ]);

        StudentFeedback::create([
            'faculty_id'     => $profile->user_id,
            'feedback_token' => $token,
            'scores'         => [
                'clarity'       => (float) $data['scores']['clarity'],
                'communication' => (float) $data['scores']['communication'],
                'punctuality'   => (float) $data['scores']['punctuality'],
                'engagement'    => (float) $data['scores']['engagement'],
            ],
            'comment'      => $data['comment'] ?? null,
            'submitted_at' => Carbon::now(),
        ]);

        return redirect()->route('feedback.thanks', $token);
    }

    public function thanks(string $token): View
    {
        $profile = FacultyProfile::where('feedback_token', $token)->firstOrFail();
        $faculty = User::findOrFail($profile->user_id);
        return view('public.feedback_thanks', compact('faculty'));
    }
}
