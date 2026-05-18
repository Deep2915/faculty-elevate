<?php

namespace App\Models;

class Goal extends BaseMongoModel
{
    protected $collection = 'goals';

    protected $fillable = [
        'faculty_id',
        'title',
        'description',
        'target_date',
        'milestones',
        'completion_percentage',
        'status',
    ];

    protected $casts = [
        'target_date' => 'datetime',
        'milestones' => 'array',
        'completion_percentage' => 'float',
    ];
}
