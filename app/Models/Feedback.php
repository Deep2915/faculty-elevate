<?php

namespace App\Models;

class Feedback extends BaseMongoModel
{
    protected $collection = 'feedbacks';

    protected $fillable = [
        'faculty_id',
        'reviewer_id',
        'type',
        'scores',
        'comment',
        'is_anonymous',
    ];

    protected $casts = [
        'scores' => 'array',
        'is_anonymous' => 'boolean',
    ];
}
