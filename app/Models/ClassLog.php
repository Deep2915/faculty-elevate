<?php

namespace App\Models;

class ClassLog extends BaseMongoModel
{
    protected $collection = 'class_logs';

    protected $fillable = [
        'timetable_entry_id',
        'faculty_id',
        'date',
        'status',   // conducted | cancelled | substituted
        'remarks',
        'logged_by',  // faculty or hod user id
        'overridden_by', // hod user id if overridden
    ];

    protected $casts = [
        'date' => 'datetime',
    ];

    const STATUS_CONDUCTED    = 'conducted';
    const STATUS_CANCELLED    = 'cancelled';
    const STATUS_SUBSTITUTED  = 'substituted';

    /**
     * Compute attendance score for a faculty_id.
     * Score = conducted / (conducted + cancelled + substituted) capped at 1.
     *
     * @param  string      $facultyId
     * @param  string|null $from  date string (nullable = all time)
     * @param  string|null $to    date string (nullable = all time)
     * @return array{score: float, conducted: int, total: int}
     */
    public static function computeScore(string $facultyId, ?string $from = null, ?string $to = null): array
    {
        $query = self::where('faculty_id', $facultyId);

        if ($from) {
            $query->where('date', '>=', \Carbon\Carbon::parse($from)->startOfDay());
        }
        if ($to) {
            $query->where('date', '<=', \Carbon\Carbon::parse($to)->endOfDay());
        }

        $logs = $query->get();

        $conducted = $logs->where('status', self::STATUS_CONDUCTED)->count();
        $total     = $logs->count();

        return [
            'score'     => $total > 0 ? round($conducted / $total, 4) : 0.0,
            'conducted' => $conducted,
            'total'     => $total,
        ];
    }
}
