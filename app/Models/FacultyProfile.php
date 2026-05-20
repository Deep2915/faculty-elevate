<?php

namespace App\Models;

use Carbon\Carbon;

class FacultyProfile extends BaseMongoModel
{
    protected $collection = 'faculty_profiles';

    protected $fillable = [
        'user_id',
        'bio',
        'department',
        'designation',
        'joining_date',
        'skills',
        'research_score',
        'teaching_score',
        'innovation_score',
        'performance_index',
        'xp',
        'level',
        'rank',
        'feedback_token',
    ];

    protected $casts = [
        'joining_date' => 'datetime',
        'skills' => 'array',
        'research_score' => 'float',
        'teaching_score' => 'float',
        'innovation_score' => 'float',
        'performance_index' => 'float',
        'xp' => 'integer',
        'level' => 'integer',
        'rank' => 'integer',
    ];

    public function calculatePerformanceScore(): float
    {
        $weights = Setting::firstWhere('key', 'performance_weights')?->value ?? [
            'research' => 0.4,
            'teaching' => 0.4,
            'innovation' => 0.2,
        ];

        $this->performance_index =
            ((float) $this->research_score * (float) ($weights['research'] ?? 0.4)) +
            ((float) $this->teaching_score * (float) ($weights['teaching'] ?? 0.4)) +
            ((float) $this->innovation_score * (float) ($weights['innovation'] ?? 0.2));

        $this->updated_at = Carbon::now();
        $this->save();

        return (float) $this->performance_index;
    }
}
