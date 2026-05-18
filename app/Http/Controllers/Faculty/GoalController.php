<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Goal;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GoalController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $goals = Goal::where('faculty_id', (string) auth()->id())->get();

        return view('faculty.goals.index', compact('goals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'target_date' => ['required', 'date'],
        ]);

        Goal::create([
            'faculty_id' => (string) auth()->id(),
            'title' => $data['title'],
            'description' => $data['description'],
            'target_date' => $data['target_date'],
            'milestones' => [],
            'completion_percentage' => 0.0,
            'status' => 'active',
        ]);

        return back()->with('status', 'Goal created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $goal = Goal::findOrFail($id);

        return view('faculty.goals.show', compact('goal'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $goal = Goal::findOrFail($id);
        $goal->update($request->only(['title', 'description', 'status', 'completion_percentage']));

        return back()->with('status', 'Goal updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Goal::findOrFail($id)->delete();

        return back()->with('status', 'Goal deleted.');
    }

    public function roadmap(): View
    {
        $goals = Goal::where('faculty_id', (string) auth()->id())->orderBy('target_date')->get();

        return view('faculty.roadmap', compact('goals'));
    }
}
