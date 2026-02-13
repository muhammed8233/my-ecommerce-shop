<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // 1. Add item to nested cart array
    public function addItem(Request $request, $userId)
    {
        $user = User::findOrFail($userId);
        $product = Product::findOrFail($request->product_id);

        // MongoDB 'push' adds the object to the 'cart' array
        $user->push('cart', [
            'product_id' => $product->id,
            'name' => $product->productName,
            'price' => (float) $product->price,
            'quantity' => (int) ($request->quantity ?? 1),
            'added_at' => now()
        ]);

        return response()->json(['message' => 'Product added to cart', 'cart' => $user->cart]);
    }

    // 2. View current user's cart
    public function show($userId)
    {
        $user = User::findOrFail($userId);
        return response()->json($user->cart ?? []);
    }

    // 3. Remove item using MongoDB 'pull'
    public function removeItem(Request $request, $userId)
    {
        $user = User::findOrFail($userId);

        // 'pull' removes items from the array that match the criteria
        $user->pull('cart', ['product_id' => $request->product_id]);

        return response()->json(['message' => 'Item removed', 'cart' => $user->cart]);
    }
}
