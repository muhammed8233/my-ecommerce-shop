<?php

namespace App\Contracts;

use App\Models\Order;

interface InventoryMovementService
{
    /**
     * @param string $productId
     * @param int $quantity
     * @return void
     */
    public function restockProduct(string $productId, int $quantity): void;

    /**
     * @param Order $order
     * @return bool
     */
    public function reReserveStock(Order $order): bool;
}
