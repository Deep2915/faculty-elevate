<?php

namespace App\Http\Controllers\HOD;

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

        return view('hod.leaderboard', compact('profiles'));
    }
}
