<?php

namespace App\Repositories;

use App\Models\InventoryMovement;
use App\Enums\Reason;
use Illuminate\Support\Collection;

class InventoryMovementRepository
{
    /**
     * Equivalent to repository.findAll()
     */
    public function all(): Collection
    {
        return InventoryMovement::orderBy('created_at', 'desc')->get();
    }

    /**
     * Find movements for a specific product (Stock History)
     * Equivalent to List<InventoryMovement> findByProductId(String productId)
     */
    public function findByProductId(string $productId): Collection
    {
        return InventoryMovement::where('product_id', $productId)
                                ->orderBy('created_at', 'desc')
                                ->get();
    }

    /**
     * Filter movements by Reason (e.g., show all 'RETURNS' or 'RESTOCKS')
     * Equivalent to List<InventoryMovement> findByReason(Reason reason)
     */
    public function findByReason(Reason|string $reason): Collection
    {
        // Get the raw string value if an Enum instance is passed
        $value = $reason instanceof Reason ? $reason->value : $reason;

        return InventoryMovement::where('reason', $value)
                                ->orderBy('created_at', 'desc')
                                ->get();
    }

    /**
     * Equivalent to repository.save(movement)
     */
    public function save(array $data): InventoryMovement
    {
        return InventoryMovement::create($data);
    }

    /**
     * Find movements by a specific user (Admin/Staff tracking)
     */
    public function findByUserId(string $userId): Collection
    {
        return InventoryMovement::where('user_id', $userId)
                                ->orderBy('created_at', 'desc')
                                ->get();
    }
}
