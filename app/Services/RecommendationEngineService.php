<?php

namespace App\Services;

use App\Models\FacultyProfile;
use App\Models\Workshop;

class RecommendationEngineService
{
    public function getRecommendations(FacultyProfile $profile): array
    {
        $recommendedCategories = [];
        $certifications = [];
        $skillGaps = [];

        if (((float) data_get($profile, 'student_clarity', 1)) < 0.70) {
            $recommendedCategories[] = 'pedagogy';
        }

        if ((float) $profile->innovation_score < 0.60) {
            $recommendedCategories[] = 'design-thinking';
            $recommendedCategories[] = 'edtech';
        }

        if ((float) $profile->research_score < 0.50) {
            $certifications[] = 'Research Methodology';
        }

        $workshops = Workshop::query()
            ->whereIn('category', array_values(array_unique($recommendedCategories)))
            ->where('status', 'upcoming')
            ->limit(5)
            ->get();

        if (empty($profile->skills)) {
            $skillGaps[] = 'No skill profile found. Add baseline competencies.';
        }

        return [
            'workshops' => $workshops->all(),
            'certifications' => $certifications,
            'skill_gaps' => $skillGaps,
        ];
    }
}
