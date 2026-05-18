<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Evaluation;
use App\Models\FacultyProfile;
use App\Models\User;
use App\Models\Workshop;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalFaculty   = User::where('role', 'faculty')->count();
        $totalHOD       = User::where('role', 'hod')->count();
        $totalWorkshops = Workshop::count();
        $totalEvals     = Evaluation::count();

        // Top 5 performers by PI
        $topPerformers = FacultyProfile::orderByDesc('performance_index')
            ->limit(5)
            ->get()
            ->map(function ($profile) {
                $profile->user = User::find($profile->user_id);
                return $profile;
            })
            ->filter(fn($p) => $p->user !== null);

        // PI distribution for chart (buckets 0-20, 20-40, 40-60, 60-80, 80-100)
        $piData = [0, 0, 0, 0, 0];
        FacultyProfile::all()->each(function ($p) use (&$piData) {
            $idx = min(4, (int) floor(((float) $p->performance_index) / 20));
            $piData[$idx]++;
        });

        // Recent users
        $recentUsers = User::orderByDesc('created_at')->limit(6)->get();

        // Workshop status counts
        $wsUpcoming  = Workshop::where('status', 'upcoming')->count();
        $wsOngoing   = Workshop::where('status', 'ongoing')->count();
        $wsCompleted = Workshop::where('status', 'completed')->count();

        return view('admin.dashboard', compact(
            'totalFaculty', 'totalHOD', 'totalWorkshops', 'totalEvals',
            'topPerformers', 'piData', 'recentUsers',
            'wsUpcoming', 'wsOngoing', 'wsCompleted'
        ));
    }
}
