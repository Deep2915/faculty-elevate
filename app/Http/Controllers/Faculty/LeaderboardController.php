<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\FacultyProfile;
use App\Models\User;
use Illuminate\View\View;

class LeaderboardController extends Controller
{
    public function index(): View
    {
        $profiles = FacultyProfile::orderByDesc('performance_index')
            ->get()
            ->map(function ($p) {
                $p->user = User::find($p->user_id);
                return $p;
            })
            ->filter(fn($p) => $p->user !== null)
            ->values();

        $myProfile = FacultyProfile::firstWhere('user_id', (string) auth()->id());
        $myRank    = $profiles->search(fn($p) => (string)$p->user_id === (string)auth()->id());
        $myRank    = $myRank !== false ? $myRank + 1 : null;

        return view('faculty.leaderboard', compact('profiles', 'myProfile', 'myRank'));
    }
}
