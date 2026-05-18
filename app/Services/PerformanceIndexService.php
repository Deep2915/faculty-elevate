<?php

namespace App\Services;

use App\Models\FacultyProfile;
use App\Models\Setting;

class PerformanceIndexService
{
    public function calculate(FacultyProfile $profile): float
    {
        $weights = Setting::firstWhere('key', 'performance_weights')?->value ?? [
            'research' => 0.4,
            'teaching' => 0.4,
            'innovation' => 0.2,
        ];

        $performanceIndex =
            ((float) $profile->research_score * (float) ($weights['research'] ?? 0.4)) +
            ((float) $profile->teaching_score * (float) ($weights['teaching'] ?? 0.4)) +
            ((float) $profile->innovation_score * (float) ($weights['innovation'] ?? 0.2));

        $profile->performance_index = $performanceIndex;
        $profile->save();

        return (float) $performanceIndex;
    }
}
