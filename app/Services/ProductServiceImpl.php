<?php

namespace App\Services;

use App\Contracts\ProductService;
use App\Models\Product;
use Exception;

class ProductServiceImpl implements ProductService
{
    public function findById(string $id): Product
    {
        return Product::findOrFail($id);
    }

    public function deductStock(string $productId, int $quantity): void
    {
        $product = $this->findById($productId);

        if ($product->stock_quantity < $quantity) {
            throw new Exception("Insufficient stock for {$product->productName}");
        }

        // Atomic decrement in MongoDB
        $product->decrement('stock_quantity', $quantity);
    }

    public function findAllById(array $ids)
    {
        return Product::whereIn('_id', $ids)->get();
    }
}
