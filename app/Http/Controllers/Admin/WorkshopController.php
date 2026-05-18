<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $sort = in_array($request->string('sort')->toString(), ['schedule_date', 'title', 'category', 'status'], true)
            ? $request->string('sort')->toString()
            : 'schedule_date';
        $direction = $request->string('direction')->toString() === 'desc' ? 'desc' : 'asc';
        $search = trim($request->string('q')->toString());
        $status = trim($request->string('status')->toString());

        $workshops = Workshop::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($nested) use ($search): void {
                    $nested->where('title', 'like', '%'.$search.'%')
                        ->orWhere('facilitator', 'like', '%'.$search.'%')
                        ->orWhere('category', 'like', '%'.$search.'%');
                });
            })
            ->when(in_array($status, ['upcoming', 'ongoing', 'completed'], true), function ($query) use ($status): void {
                $query->where('status', $status);
            })
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('admin.workshops.index', compact('workshops'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('admin.workshops.index');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreWorkshopRequest $request): RedirectResponse
    {
        Workshop::query()->create([
            ...$request->validated(),
            'registered_faculty_ids' => [],
        ]);

        return redirect()->route('admin.workshops.index')->with('status', 'Workshop created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): RedirectResponse
    {
        return redirect()->route('admin.workshops.index', ['highlight' => $id]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id): RedirectResponse
    {
        return redirect()->route('admin.workshops.index', ['edit' => $id]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreWorkshopRequest $request, string $id): RedirectResponse
    {
        $workshop = Workshop::query()->findOrFail($id);
        $workshop->update($request->validated());

        return redirect()->route('admin.workshops.index')->with('status', 'Workshop updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        Workshop::query()->findOrFail($id)->delete();

        return redirect()->route('admin.workshops.index')->with('status', 'Workshop deleted successfully.');
    }
}
