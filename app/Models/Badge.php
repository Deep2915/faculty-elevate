<?php

namespace App\Models;

class Badge extends BaseMongoModel
{
    protected $collection = 'badges';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon_svg',
        'criteria',
        'xp_threshold',
        'category',
    ];

    protected $casts = [
        'criteria' => 'array',
        'xp_threshold' => 'integer',
    ];
}
