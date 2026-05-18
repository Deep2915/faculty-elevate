<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

abstract class BaseMongoModel extends Model
{
    protected $connection = 'mongodb';
}
