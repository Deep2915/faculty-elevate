<?php

namespace App\Http\Controllers\HOD;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreEvaluationRequest;
use App\Models\Evaluation;
use App\Models\FacultyProfile;
use App\Models\User;
use App\Notifications\EvaluationPublishedNotification;
use App\Services\GamificationService;
use App\Services\PerformanceIndexService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EvaluationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $search = trim($request->string('q')->toString());
        $status = trim($request->string('status')->toString());
        $sort = in_array($request->string('sort')->toString(), ['created_at', 'weighted_score', 'period', 'status'], true)
            ? $request->string('sort')->toString()
            : 'created_at';
        $direction = $request->string('direction')->toString() === 'asc' ? 'asc' : 'desc';

        $evaluations = Evaluation::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('faculty_id', 'like', '%'.$search.'%')
                        ->orWhere('period', 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($status, ['draft', 'published'], true), function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        $faculties = User::query()->where('role', 'faculty')->orderBy('name')->get();

        return view('hod.evaluations.index', compact('evaluations', 'faculties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $faculties = User::query()->where('role', 'faculty')->orderBy('name')->get();
        $evaluations = Evaluation::query()->orderByDesc('created_at')->paginate(15);

        return view('hod.evaluations.index', compact('faculties', 'evaluations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreEvaluationRequest $request,
        PerformanceIndexService $performanceIndexService,
        GamificationService $gamificationService
    ): RedirectResponse
    {
        $validated = $request->validated();
        $weightedScore = (
            ((float) ($validated['scores']['research'] ?? 0)) +
            ((float) ($validated['scores']['teaching'] ?? 0)) +
            ((float) ($validated['scores']['innovation'] ?? 0))
        ) / 3;

        Evaluation::create([
            ...$validated,
            'evaluator_id' => (string) auth()->id(),
            'weighted_score' => (float) $weightedScore,
        ]);

        $profile = FacultyProfile::firstWhere('user_id', $validated['faculty_id']);
        if ($profile) {
            $profile->research_score = (float) ($validated['scores']['research'] ?? 0);
            $profile->teaching_score = (float) ($validated['scores']['teaching'] ?? 0);
            $profile->innovation_score = (float) ($validated['scores']['innovation'] ?? 0);
            $performanceIndexService->calculate($profile);

            if ((float) $weightedScore >= 0.8) {
                $faculty = User::find($validated['faculty_id']);
                if ($faculty) {
                    $gamificationService->awardXP($faculty, 50, 'Strong evaluation performance');
                }
            }
        }

        if (($validated['status'] ?? 'draft') === 'published') {
            // Notification disabled: Notifiable trait removed (no SQL connection)
            // $faculty = User::find($validated['faculty_id']);
            // if ($faculty) { $faculty->notify(new EvaluationPublishedNotification()); }
        }

        return redirect()->route('hod.evaluations.index')
            ->with('status', 'Evaluation saved.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('hod.evaluations.index', ['highlight' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): RedirectResponse
    {
        return redirect()->route('hod.evaluations.index', ['edit' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        StoreEvaluationRequest $request,
        PerformanceIndexService $performanceIndexService,
        GamificationService $gamificationService,
        string $id
    ): RedirectResponse
    {
        $evaluation = Evaluation::query()->findOrFail($id);
        $validated = $request->validated();

        $weightedScore = (
            ((float) ($validated['scores']['research'] ?? 0)) +
            ((float) ($validated['scores']['teaching'] ?? 0)) +
            ((float) ($validated['scores']['innovation'] ?? 0))
        ) / 3;

        $evaluation->update([
            ...$validated,
            'weighted_score' => (float) $weightedScore,
        ]);

        $profile = FacultyProfile::firstWhere('user_id', $validated['faculty_id']);
        if ($profile) {
            $profile->research_score = (float) ($validated['scores']['research'] ?? 0);
            $profile->teaching_score = (float) ($validated['scores']['teaching'] ?? 0);
            $profile->innovation_score = (float) ($validated['scores']['innovation'] ?? 0);
            $performanceIndexService->calculate($profile);

            if ((float) $weightedScore >= 0.8) {
                $faculty = User::find($validated['faculty_id']);
                if ($faculty) {
                    $gamificationService->awardXP($faculty, 50, 'Strong evaluation performance');
                }
            }
        }

        return redirect()->route('hod.evaluations.index')->with('status', 'Evaluation updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        Evaluation::query()->findOrFail($id)->delete();

        return redirect()->route('hod.evaluations.index')->with('status', 'Evaluation deleted.');
    }
}
