<?php

namespace App\Http\Controllers\Faculty;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Workshop;
use App\Services\GamificationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(Request $request): View
    {
        $status   = $request->get('status', 'upcoming');
        $category = trim($request->get('category', ''));

        $workshops = Workshop::query()
            ->when(in_array($status, ['upcoming', 'ongoing', 'completed'], true), fn($q) => $q->where('status', $status))
            ->when($category !== '', fn($q) => $q->where('category', 'like', "%{$category}%"))
            ->orderBy('schedule_date')
            ->paginate(12)
            ->withQueryString();

        return view('faculty.workshops.index', compact('workshops', 'status'));
    }

    public function register(string $workshop, GamificationService $gamificationService): RedirectResponse
    {
        $ws = Workshop::findOrFail($workshop);

        // Award XP for registering / completing a workshop
        /** @var User $user */
        $user = auth()->user();
        $gamificationService->awardXP($user, (int)($ws->xp_reward ?? 50), "Registered for workshop: {$ws->title}");

        return back()->with('status', "Registered for \"{$ws->title}\" — {$ws->xp_reward} XP awarded! 🎉");
    }
}
