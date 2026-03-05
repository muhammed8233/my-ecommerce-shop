<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Payment extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'payments';

    protected $fillable = [
        'orderId',
        'reference',
        'amount',
        'status', // PENDING, SUCCESSFUL, FAILED
        'currency',
        'channel',
        'paid_at'
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'amount' => 'float'
    ];
}
