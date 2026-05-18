<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Models\User;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AchievementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $type = trim($request->string('type')->toString());
        $sort = in_array($request->string('sort')->toString(), ['date', 'xp_awarded', 'title'], true)
            ? $request->string('sort')->toString()
            : 'date';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';

        $achievements = Achievement::query()
            ->where('faculty_id', (string) auth()->id())
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('title', 'like', '%'.$search.'%')
                        ->orWhere('journal_or_body', 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($type, ['publication', 'patent', 'award', 'certification'], true), function ($query) use ($type): void {
                $query->where('type', $type);
            })
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('faculty.achievements.index', compact('achievements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('faculty.achievements.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, GamificationService $gamificationService): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:publication,patent,award,certification'],
            'title' => ['required', 'string', 'max:255'],
            'journal_or_body' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'proof_url' => ['nullable', 'url'],
        ]);

        $xpByType = [
            'publication' => 100,
            'patent' => 150,
            'award' => 80,
            'certification' => 60,
        ];

        Achievement::query()->create([
            ...$data,
            'faculty_id' => (string) auth()->id(),
            'xp_awarded' => $xpByType[$data['type']],
            'verified' => false,
        ]);

        $user = User::query()->find(auth()->id());
        if ($user) {
            $gamificationService->awardXP($user, $xpByType[$data['type']], 'Achievement added');
        }

        return redirect()->route('faculty.achievements.index')->with('status', 'Achievement added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('faculty.achievements.index', ['highlight' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): RedirectResponse
    {
        return redirect()->route('faculty.achievements.index', ['edit' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $achievement = Achievement::query()
            ->where('faculty_id', (string) auth()->id())
            ->findOrFail($id);

        $data = $request->validate([
            'type' => ['required', 'in:publication,patent,award,certification'],
            'title' => ['required', 'string', 'max:255'],
            'journal_or_body' => ['required', 'string', 'max:255'],
            'date' => ['required', 'date'],
            'proof_url' => ['nullable', 'url'],
        ]);

        $achievement->update($data);

        return redirect()->route('faculty.achievements.index')->with('status', 'Achievement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        Achievement::query()
            ->where('faculty_id', (string) auth()->id())
            ->findOrFail($id)
            ->delete();

        return redirect()->route('faculty.achievements.index')->with('status', 'Achievement deleted successfully.');
    }
}
