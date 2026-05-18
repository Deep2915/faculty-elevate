<?php

namespace App\Models;

class WellbeingSurvey extends BaseMongoModel
{
    protected $collection = 'wellbeing_surveys';

    protected $fillable = [
        'faculty_id',
        'responses',
        'burnout_index',
        'notes',
        'surveyed_at',
    ];

    protected $casts = [
        'responses' => 'array',
        'burnout_index' => 'float',
        'surveyed_at' => 'datetime',
    ];
}
