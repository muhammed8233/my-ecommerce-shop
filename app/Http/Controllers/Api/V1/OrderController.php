<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\OrderService;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Response;

class OrderController extends Controller
{
    // Constructor Injection (Equivalent to @Autowired / @RequiredArgsConstructor)
    public function __construct(
        protected OrderService $orderService,
        protected PaymentGatewayService $paymentGatewayService
    ) {}

    /**
     * POST /api/v1/orders/place
     * Equivalent to placeOrder()
     */
    public function placeOrder(Request $request): JsonResponse
    {
        // Validation logic usually goes here or in a FormRequest
        $orderResponse = $this->orderService->placeOrder($request->all());

        return response()->json($orderResponse, 201);
    }

    /**
     * POST /api/v1/orders/{orderId}/initiate-payment
     * Equivalent to initiatePayment()
     */
    public function initiatePayment(string $orderId): JsonResponse
    {
        $paymentUrl = $this->orderService->initiatePayment($orderId);
        
        return response()->json($paymentUrl);
    }

    /**
     * GET /api/v1/orders
     * Equivalent to getOrders() with Pagination
     */
    public function getOrders(Request $request): JsonResponse
    {
        $search = $request->query('search', '');
        $perPage = $request->query('size', 10);
        
        // Laravel's lengthAwarePaginator handles the "Pageable" logic
        $orders = $this->orderService->getOrders($search, $perPage);

        return response()->json($orders);
    }

    /**
     * GET /api/v1/orders/{id}
     * Equivalent to getOrderById()
     */
    public function getOrderById(string $id, Request $request): JsonResponse
    {
        $reference = $request->query('reference');
        $order = $this->orderService->findById($id);
        
        return response()->json($this->orderService->mapToOrderResponse($order));
    }
}
