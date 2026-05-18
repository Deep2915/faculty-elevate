<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Badge;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BadgeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $sort = in_array($request->string('sort')->toString(), ['xp_threshold', 'name', 'category'], true)
            ? $request->string('sort')->toString()
            : 'xp_threshold';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        $search = trim($request->string('q')->toString());
        $category = trim($request->string('category')->toString());

        $badges = Badge::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('name', 'like', '%'.$search.'%')
                        ->orWhere('description', 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($category, ['research', 'teaching', 'innovation', 'attendance'], true), function ($query) use ($category): void {
                $query->where('category', $category);
            })
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('admin.badges.index', compact('badges'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.badges.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'icon_svg' => ['nullable', 'string'],
            'xp_threshold' => ['required', 'integer', 'min:0'],
            'category' => ['required', 'in:research,teaching,innovation,attendance'],
        ]);

        Badge::query()->create([
            ...$data,
            'slug' => Str::slug($data['name']),
            'criteria' => ['xp_threshold' => (int) $data['xp_threshold']],
            'icon_svg' => $data['icon_svg'] ?? '<svg></svg>',
        ]);

        return redirect()->route('admin.badges.index')->with('status', 'Badge created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.badges.index', ['highlight' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): RedirectResponse
    {
        return redirect()->route('admin.badges.index', ['edit' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): RedirectResponse
    {
        $badge = Badge::query()->findOrFail($id);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:1000'],
            'icon_svg' => ['nullable', 'string'],
            'xp_threshold' => ['required', 'integer', 'min:0'],
            'category' => ['required', 'in:research,teaching,innovation,attendance'],
        ]);

        $badge->update([
            ...$data,
            'slug' => Str::slug($data['name']),
            'criteria' => ['xp_threshold' => (int) $data['xp_threshold']],
            'icon_svg' => $data['icon_svg'] ?? '<svg></svg>',
        ]);

        return redirect()->route('admin.badges.index')->with('status', 'Badge updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        Badge::query()->findOrFail($id)->delete();

        return redirect()->route('admin.badges.index')->with('status', 'Badge deleted successfully.');
    }
}
