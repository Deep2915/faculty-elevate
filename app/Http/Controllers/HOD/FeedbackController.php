<?php

namespace App\Http\Controllers\HOD;

use App\Http\Controllers\Controller;
use App\Models\StudentFeedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FeedbackController extends Controller
{
    public function index(Request $request): View
    {
        $faculties = User::where('role', 'faculty')->orderBy('name')->get();

        $feedbackStats = $faculties->map(function (User $faculty) {
            $scores = StudentFeedback::computeAverageScores((string) $faculty->id);
            return [
                'faculty' => $faculty,
                'scores'  => $scores,
            ];
        });

        return view('hod.feedback.index', compact('feedbackStats'));
    }
}

