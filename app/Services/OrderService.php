<?php

namespace App\Contracts;

use App\Models\Order;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderService
{
    /**
     * @param string $orderId
     * @return string (Payment URL or Reference)
     */
    public function initiatePayment(string $orderId): string;

    /**
     * @param array $request (Equivalent to OrderRequestDto)
     * @return array (Equivalent to OrderResponseDto)
     */
    public function placeOrder(array $request): array;

    /**
     * @param string $orderId
     * @return void
     */
    public function markAsPaid(string $orderId): void;

    /**
     * @param string|null $search
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getOrders(?string $search, int $perPage): LengthAwarePaginator;

    /**
     * Equivalent to mapToOrderResponse
     * @param Order $order
     * @return array
     */
    public function mapToOrderResponse(Order $order): array;

    /**
     * @param string $orderId
     * @param string $newStatus (Enum equivalent)
     * @return void
     */
    public function updateStatus(string $orderId, string $newStatus): void;

    /**
     * @param string $orderId
     * @return Order
     */
    public function findById(string $orderId): Order;
}
