<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Support\Collection;

class ProductRepository
{

    public function delete(string $id): bool
    {
        $product = Product::find($id);
        return $product ? (bool)$product->delete() : false;
    }

    public function searchByName(string $name): Collection
    {
        return Product::where('name', 'like', "%$name%")->get();
    }
}
