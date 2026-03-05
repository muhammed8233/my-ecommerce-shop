<?php

namespace App\Services;

use App\Contracts\OrderService;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class OrderServiceImpl implements OrderService
{
    public function __construct(
        protected \App\Contracts\ProductService $productService,
        protected \App\Contracts\InventoryMovementService $inventoryService,
        protected \App\Services\PaymentGatewayService $paymentGateway
    ) {}

    public function initiatePayment(string $orderId): string
    {
        // Equivalent to SecurityContextHolder.getContext().getAuthentication()
        $user = Auth::user(); 
        
        $order = $this->findById($orderId);

        return $this->paymentGateway->initiatePayment(
            $order->total_amount, "USD", $order->id
        );
    }

    public function placeOrder(array $request): array
    {
        $user = Auth::user();

        // 1. Validate Stock (Logic below)
        $this->validateStockAvailability($request['item_list']);

        // 2. Calculate Total
        $totalAmount = $this->calculateTotal($request['item_list']);

        // 3. Save Order (Atomic Transaction)
        return DB::connection('mongodb')->transaction(function () use ($request, $user, $totalAmount) {
            $order = Order::create([
                'userId' => $user->id,
                'orderedStatus' => 'PENDING',
                'totalAmount' => $totalAmount,
                'orderedItems' => []
            ]);

            $orderItems = [];
            foreach ($request['item_list'] as $itemDto) {
                $product = $this->productService->findById($itemDto['product_id']);

                // Deduct Stock
                $this->productService->deductStock($product->id, $itemDto['quantity']);

                $orderItems[] = [
                    'productId' => $product->id,
                    'name' => $product->productName,
                    'quantity' => $itemDto['quantity'],
                    'unitPrice' => $product->price,
                ];
            }

            $order->orderedItems = $orderItems;
            $order->save();

            return $this->mapToOrderResponse($order);
        });
    }

    public function markAsPaid(string $orderId): void
    {
        $order = $this->findById($orderId);

        if ($order->orderedStatus === 'CANCELLED') {
            Log::info("Attempting to re-reserve stock for revived Order: $orderId");

            if (!$this->inventoryService->reReserveStock($order)) {
                Log::error("Revival Failed: Items out of stock for Order: $orderId");
                return;
            }
        }

        $order->orderedStatus = 'PAID';
        $order->save();
        Log::info("Order $orderId marked as PAID");
    }

    private function validateStockAvailability(array $items): void
    {
        foreach ($items as $item) {
            $product = $this->productService->findById($item['product_id']);
            
            if (!$product) throw new Exception("Product not found: " . $item['product_id']);

            if ($product->stock_quantity < $item['quantity']) {
                throw new Exception("Insufficient stock for " . $product->productName);
            }
        }
    }

    private function calculateTotal(array $items): float
    {
        return collect($items)->reduce(function ($carry, $item) {
            $product = $this->productService->findById($item['product_id']);
            return $carry + ($product->price * $item['quantity']);
        }, 0.00);
    }

    public function getOrders(?string $search, int $perPage = 10)
    {
        $query = Order::query();

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('_id', 'like', "%$search%")
                  ->orWhere('userId', 'like', "%$search%");
            });
        }

        // Equivalent to PageableExecutionUtils.getPage
        return $query->paginate($perPage); 
    }

    public function findById(string $orderId): Order
    {
        return Order::findOrFail($orderId);
    }

    public function mapToOrderResponse(Order $order): array
    {
        return [
            'orderId' => $order->id,
            'totalAmount' => $order->totalAmount,
            'status' => $order->orderedStatus,
            'items' => collect($order->orderedItems)->map(fn($item) => [
                'productName' => $item['name'],
                'quantity' => $item['quantity'],
                'unitPrice' => $item['unitPrice']
            ])->toArray()
        ];
    }
}
