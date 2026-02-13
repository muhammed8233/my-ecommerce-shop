<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Support\Collection;

class Order extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'orders';

    protected $fillable = [
        'userId',
        'orderedStatus',
        'totalAmount',
        'orderedItems'
    ];

    protected $casts = [
        'totalAmount' => 'decimal:2',
        'orderedStatus' => 'string', 
        'orderedItems' => 'array',  
    ];

    public function addOrderItem(array $orderItem): void
    {
        $items = collect($this->orderedItems ?? []);
        $items->push($orderItem);
        
        $this->orderedItems = $items->all();
    }
}
