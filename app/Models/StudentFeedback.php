<?php

namespace App\Models;

class StudentFeedback extends BaseMongoModel
{
    protected $collection = 'student_feedbacks';

    protected $fillable = [
        'faculty_id',
        'feedback_token',
        'scores',
        'comment',
        'submitted_at',
    ];

    protected $casts = [
        'scores'       => 'array',
        'submitted_at' => 'datetime',
    ];

    /**
     * Compute average clarity score (0-1) for a faculty.
     */
    public static function computeClarityScore(string $facultyId): float
    {
        $feedbacks = self::where('faculty_id', $facultyId)->get();
        if ($feedbacks->isEmpty()) {
            return 0.0;
        }
        $total = $feedbacks->sum(fn ($f) => (float) data_get($f->scores, 'clarity', 0));
        return (float) round($total / $feedbacks->count(), 4);
    }

    /**
     * Compute average of all dimension scores for a faculty.
     */
    public static function computeAverageScores(string $facultyId): array
    {
        $feedbacks = self::where('faculty_id', $facultyId)->get();
        if ($feedbacks->isEmpty()) {
            return ['clarity' => 0, 'communication' => 0, 'punctuality' => 0, 'engagement' => 0, 'count' => 0];
        }
        $dims = ['clarity', 'communication', 'punctuality', 'engagement'];
        $result = [];
        foreach ($dims as $dim) {
            $result[$dim] = round($feedbacks->avg(fn ($f) => (float) data_get($f->scores, $dim, 0)), 2);
        }
        $result['count'] = $feedbacks->count();
        return $result;
    }
}
