<?php

namespace App\Models;

class Workshop extends BaseMongoModel
{
    protected $collection = 'workshops';

    protected $fillable = [
        'title',
        'description',
        'facilitator',
        'category',
        'schedule_date',
        'duration_hours',
        'capacity',
        'registered_faculty_ids',
        'xp_reward',
        'status',
    ];

    protected $casts = [
        'schedule_date' => 'datetime',
        'duration_hours' => 'float',
        'registered_faculty_ids' => 'array',
        'xp_reward' => 'integer',
    ];
}
