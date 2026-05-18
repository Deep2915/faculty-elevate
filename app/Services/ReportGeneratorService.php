<?php

namespace App\Services;

use App\Models\Achievement;
use App\Models\Badge;
use App\Models\Evaluation;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportGeneratorService
{
    public function generateAnnualReport(User $faculty): string
    {
        $profile     = $faculty->profile ?? null;
        $evaluations = Evaluation::where('faculty_id', (string) $faculty->getKey())
            ->where('status', 'published')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        $achievements = Achievement::where('faculty_id', (string) $faculty->getKey())
            ->orderByDesc('date')
            ->get();

        // Earned badges (based on XP threshold)
        $badges = $profile
            ? Badge::where('xp_threshold', '<=', (int) ($profile->xp ?? 0))->orderBy('xp_threshold')->get()
            : collect();

        $pdf = Pdf::loadView('reports.annual_growth_report', [
            'faculty'      => $faculty,
            'profile'      => $profile,
            'evaluations'  => $evaluations,
            'achievements' => $achievements,
            'badges'       => $badges,
            'generatedAt'  => now(),
        ])->setPaper('a4', 'portrait');

        $fileName = 'reports/annual_report_' . $faculty->getKey() . '.pdf';
        $path     = storage_path('app/public/' . $fileName);

        if (! is_dir(dirname($path))) {
            mkdir(dirname($path), 0755, true);
        }

        file_put_contents($path, $pdf->output());

        return $path;
    }
}
