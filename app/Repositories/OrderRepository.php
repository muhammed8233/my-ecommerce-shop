<?php

namespace App\Repositories;

use App\Models\Order;
use Illuminate\Support\Collection;

class OrderRepository
{
   
    public function findByUserId(string $userId): Collection
    {
        return Order::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->get();
    }

    
    public function findByStatus(string $status): Collection
    {
        return Order::where('status', $status)->get();
    }

    
    public function findByIdAndUserId(string $id, string $userId): ?Order
    {
        return Order::where('_id', $id)
                    ->where('user_id', $userId)
                    ->first();
    }

    
    public function save(array $data): Order
    {
        return Order::create($data);
    }
}

