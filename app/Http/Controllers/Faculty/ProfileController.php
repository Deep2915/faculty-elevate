<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\FacultyProfile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View
    {
        $userId  = (string) auth()->id();
        $profile = FacultyProfile::firstOrCreate(
            ['user_id' => $userId],
            [
                'bio'               => '',
                'department'        => '',
                'designation'       => '',
                'skills'            => [],
                'research_score'    => 0.0,
                'teaching_score'    => 0.0,
                'innovation_score'  => 0.0,
                'performance_index' => 0.0,
                'xp'                => 0,
                'level'             => 1,
            ]
        );

        return view('faculty.profile', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bio'         => ['nullable', 'string', 'max:1000'],
            'department'  => ['nullable', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'joining_date'=> ['nullable', 'date'],
            'skills'      => ['nullable', 'string'],
        ]);

        $userId  = (string) auth()->id();
        $profile = FacultyProfile::firstOrCreate(['user_id' => $userId]);

        $profile->bio         = $data['bio']         ?? '';
        $profile->department  = $data['department']  ?? '';
        $profile->designation = $data['designation'] ?? '';
        if (!empty($data['joining_date'])) {
            $profile->joining_date = $data['joining_date'];
        }
        // Convert comma-separated skills string to array
        if (isset($data['skills'])) {
            $profile->skills = array_values(array_filter(array_map('trim', explode(',', $data['skills']))));
        }
        $profile->save();

        return back()->with('status', 'Profile updated successfully.');
    }
}
