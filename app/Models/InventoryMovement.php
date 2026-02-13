<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    protected $connection = 'mongodb';
    protected $collection = 'inventory_movements';

   
    protected $fillable = [
        'product_id',     
        'quantityChange',
        'reason'
    ];

    protected $casts = [
        'quantityChange' => 'integer',
        'reason' => 'string', 
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
 