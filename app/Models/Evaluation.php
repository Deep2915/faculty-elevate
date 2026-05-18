<?php

namespace App\Models;

class Evaluation extends BaseMongoModel
{
    protected $collection = 'evaluations';

    protected $fillable = [
        'faculty_id',
        'evaluator_id',
        'period',
        'scores',
        'weighted_score',
        'remarks',
        'status',
    ];

    protected $casts = [
        'scores' => 'array',
        'weighted_score' => 'float',
    ];
}
