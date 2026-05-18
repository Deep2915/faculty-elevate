<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WeightConfigController extends Controller
{
    public function edit(): View
    {
        $raw = Setting::firstWhere('key', 'performance_weights')?->value ?? [
            'research'   => 0.4,
            'teaching'   => 0.4,
            'innovation' => 0.2,
        ];

        // Convert 0-1 decimals → 0-100 integers for the slider view
        $weights = [
            'research'   => (int) round(((float) ($raw['research']   ?? 0.4)) * 100),
            'teaching'   => (int) round(((float) ($raw['teaching']   ?? 0.4)) * 100),
            'innovation' => (int) round(((float) ($raw['innovation'] ?? 0.2)) * 100),
        ];

        return view('admin.weights.edit', compact('weights'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'weights.research'   => ['required', 'integer', 'between:0,100'],
            'weights.teaching'   => ['required', 'integer', 'between:0,100'],
            'weights.innovation' => ['required', 'integer', 'between:0,100'],
        ]);

        $w = $request->input('weights');
        $total = ($w['research'] ?? 0) + ($w['teaching'] ?? 0) + ($w['innovation'] ?? 0);

        if ($total !== 100) {
            return back()->withErrors(['weights' => 'Weights must total exactly 100%.']);
        }

        Setting::updateOrCreate(
            ['key' => 'performance_weights'],
            ['value' => [
                'research'   => (float) $w['research']   / 100,
                'teaching'   => (float) $w['teaching']   / 100,
                'innovation' => (float) $w['innovation'] / 100,
            ]]
        );

        return back()->with('status', 'Performance weights updated successfully.');
    }
}
