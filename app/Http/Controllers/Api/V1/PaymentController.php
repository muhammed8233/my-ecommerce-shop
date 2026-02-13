<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\PaymentGatewayService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    // Constructor Injection (Equivalent to @RequiredArgsConstructor)
    public function __construct(
        protected PaymentGatewayService $paymentGatewayService
    ) {}

    /**
     * POST /api/v1/payments/webhook
     * Equivalent to handlePaystackWebhook()
     */
    public function handlePaystackWebhook(Request $request): Response
    {
        $payload = $request->getContent(); // Raw Request Body
        $signature = $request->header('x-paystack-signature');

        // Log::info('Paystack Webhook Received', ['payload' => $payload]); // Equivalent to @Slf4j

        try {
            $this->paymentGatewayService->handlePaystackWebhook($payload, $signature);
            
            // Return 200 OK with no body
            return response()->noContent(); 
        } catch (\Exception $e) {
            Log::error('Webhook processing failed: ' . $e->getMessage());
            // Paystack expects a 200 even on some failures to stop retries, 
            // but usually 400 signals a signature mismatch.
            return response()->noContent(400);
        }
    }
}
