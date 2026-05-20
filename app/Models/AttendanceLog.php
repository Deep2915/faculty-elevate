<?php

namespace App\Models;

class AttendanceLog extends BaseMongoModel
{
    protected $collection = 'attendance_logs';

    protected $fillable = [
        'faculty_id',
        'logged_by',
        'date',
        'scheduled_hours',
        'actual_hours',
        'note',
    ];

    protected $casts = [
        'date'            => 'datetime',
        'scheduled_hours' => 'float',
        'actual_hours'    => 'float',
    ];

    /**
     * Compute attendance score (0-1) for a given faculty.
     * Score = sum(actual) / sum(scheduled), capped at 1.
     */
    public static function computeScore(string $facultyId): float
    {
        $logs = self::where('faculty_id', $facultyId)->get();
        $scheduled = $logs->sum('scheduled_hours');
        $actual    = $logs->sum('actual_hours');

        if ($scheduled <= 0) {
            return 0.0;
        }

        return (float) min(1.0, round($actual / $scheduled, 4));
    }
}
