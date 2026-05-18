<?php

namespace App\Models;

class Setting extends BaseMongoModel
{
    protected $collection = 'settings';

    protected $fillable = [
        'key',
        'value',
    ];

    protected $casts = [
        'value' => 'array',
    ];
}
