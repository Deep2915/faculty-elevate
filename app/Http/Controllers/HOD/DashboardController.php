<?php

namespace App\Http\Controllers\HOD;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\FacultyProfile;
use App\Models\User;
use App\Models\WellbeingSurvey;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalFaculty  = User::where('role', 'faculty')->count();
        $pendingEvals  = Evaluation::where('status', 'draft')->count();
        $publishedEvals= Evaluation::where('status', 'published')->count();

        // Faculty with average PI
        $avgPI = FacultyProfile::avg('performance_index') ?? 0;

        // Top 5 performers for the mini-leaderboard
        $topFaculty = FacultyProfile::orderByDesc('performance_index')
            ->limit(5)->get()
            ->map(function ($p) {
                $p->user = User::find($p->user_id);
                return $p;
            })->filter(fn($p) => $p->user !== null);

        // Burnout alerts: faculty with burnout_index < 40 in last survey
        $burnoutAlerts = WellbeingSurvey::where('burnout_index', '<', 40)
            ->orderByDesc('surveyed_at')
            ->limit(10)
            ->get()
            ->map(function ($s) {
                $s->faculty = User::find($s->faculty_id);
                return $s;
            })->filter(fn($s) => $s->faculty !== null);

        // PI chart data per faculty (last 8)
        $piChartData = FacultyProfile::orderByDesc('performance_index')
            ->limit(8)->get()
            ->map(function ($p) {
                $p->user = User::find($p->user_id);
                return $p;
            })->filter(fn($p) => $p->user !== null);

        return view('hod.dashboard', compact(
            'totalFaculty', 'pendingEvals', 'publishedEvals',
            'avgPI', 'topFaculty', 'burnoutAlerts', 'piChartData'
        ));
    }
}
