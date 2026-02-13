<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class Product extends Model
{

    protected $connection = 'mongodb';
    
    protected $collection = 'products'; 

    protected $fillable = [
        'productName',
        'description',
        'sku',
        'price',
        'stockQuantity',
        'category',
        'version'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stockQuantity' => 'integer',
        'version' => 'integer',
    ];

    public $timestamps = true;
}