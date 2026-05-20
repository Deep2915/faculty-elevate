<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\WellbeingSurvey;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WellbeingController extends Controller
{
    public function index(): View
    {
        $surveys = WellbeingSurvey::query()
            ->where('faculty_id', (string) auth()->id())
            ->orderBy('surveyed_at')
            ->limit(10)
            ->get();

        return view('faculty.wellbeing', compact('surveys'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'responses.workload'   => ['required', 'integer', 'between:1,10'],
            'responses.stress'     => ['required', 'integer', 'between:1,10'],
            'responses.motivation' => ['required', 'integer', 'between:1,10'],
            'responses.support'    => ['required', 'integer', 'between:1,10'],
            'notes'                => ['nullable', 'string'],
        ]);

        $responses    = $data['responses'];
        $wellbeing    = ((11 - (int)$responses['workload']) + (11 - (int)$responses['stress'])
                         + (int)$responses['motivation'] + (int)$responses['support']) / 4 / 10 * 100;
        $burnoutIndex = round($wellbeing, 2);

        WellbeingSurvey::create([
            'faculty_id'   => (string) auth()->id(),
            'responses'    => $responses,
            'burnout_index'=> (float) $burnoutIndex,
            'notes'        => $data['notes'] ?? null,
            'surveyed_at'  => now(),
        ]);

        // Burnout alert notification disabled (Notifiable trait removed — no SQL connection)
        // HOD would be notified here if burnout_index < 40 for 2 consecutive surveys

        return back()->with('status', 'Wellbeing survey submitted. Thank you!');
    }
}
