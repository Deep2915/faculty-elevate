<?php

namespace App\Models;

class Achievement extends BaseMongoModel
{
    protected $collection = 'achievements';

    protected $fillable = [
        'faculty_id',
        'type',
        'title',
        'journal_or_body',
        'date',
        'xp_awarded',
        'verified',
        'proof_url',
    ];

    protected $casts = [
        'date' => 'datetime',
        'xp_awarded' => 'integer',
        'verified' => 'boolean',
    ];
}
