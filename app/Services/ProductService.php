<?php

namespace App\Contracts;

use\App\Models\Product;
use\Illuminate\https\Resources\JsonResponse;
use\Illuminate\Pagination\LengthAwarePaginator

interface ProductService 
{
    public function createProduct(array $request): array;
    
}