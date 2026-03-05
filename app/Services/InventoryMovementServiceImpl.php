<?php

namespace App\Services;

use App\Contracts\InventoryMovementService;
use App\Models\InventoryMovement;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryMovementServiceImpl implements InventoryMovementService
{
    /**
     * @param string $productId
     * @param int $quantity
     */
    public function restockProduct(string $productId, int $quantity): void
    {
        // 1. Find product (Like findById)
        $product = Product::findOrFail($productId);

        // 2. Update stock (Atomic $inc in MongoDB)
        $product->increment('stock_quantity', $quantity);

        // 3. Save Movement (Like builder() + save())
        InventoryMovement::create([
            'product_id' => $product->id,
            'quantity_change' => $quantity,
            'reason' => 'RESTOCK' // Enum equivalent
        ]);
    }

    /**
     * @Transactional equivalent
     * @param Order $order
     * @return bool
     */
    public function reReserveStock(Order $order): bool
    {
        return DB::connection('mongodb')->transaction(function () use ($order) {
            $items = $order->ordered_items; // Equivalent to getOrderedItems()

            // Phase 1: Check availability (Validation)
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product || $product->stock_quantity < $item['quantity']) {
                    return false; 
                }
            }

            // Phase 2: Perform decrements
            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                $product->decrement('stock_quantity', $item['quantity']);
                
                // Optional: Log movement for audit trail
                InventoryMovement::create([
                    'product_id' => $product->id,
                    'quantity_change' => -$item['quantity'],
                    'reason' => 'RESERVATION'
                ]);
            }

            return true;
        });
    }
}
