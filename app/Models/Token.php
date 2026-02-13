<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Token extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'tokens';

   
    protected $fillable = [
        'userId',
        'token',
        'created_at'
    ];

    const UPDATED_AT = null;

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
