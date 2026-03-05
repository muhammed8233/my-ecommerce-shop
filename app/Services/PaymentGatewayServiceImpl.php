<?php

namespace App\Services;

use App\Contracts\PaymentGatewayService;
use App\Contracts\OrderService;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentGatewayServiceImpl implements PaymentGatewayService
{
    protected string $baseUrl;
    protected string $secretKey;

    public function __construct(protected OrderService $orderService)
    {
        $this->baseUrl = config('services.paystack.base_url');
        $this->secretKey = config('services.paystack.secret_key');
    }

    public function initiatePayment(float $totalAmount, string $currency, string $orderId): string
    {
        $user = Auth::user();
        if (!$user || !str_contains($user->email, '@')) {
            throw new Exception("A valid user email is required for payment");
        }

        try {
            // Paystack expects amount in Kobo/Cents (Amount * 100)
            $payload = [
                'email' => $user->email,
                'amount' => $totalAmount * 100,
                'callback_url' => url("/api/v1/orders/{$orderId}"),
                'metadata' => ['order_id' => $orderId]
            ];

            // Equivalent to RestTemplate.postForObject
            $response = Http::withToken($this->secretKey)
                ->post("{$this->baseUrl}/transaction/initialize", $payload);

            if ($response->failed() || !$response->json('status')) {
                throw new Exception("Paystack Initialization Failed: " . $response->json('message'));
            }

            $data = $response->json('data');

            // Save Payment Model
            Payment::create([
                'reference' => $data['reference'],
                'orderId' => $orderId,
                'amount' => $totalAmount,
                'status' => 'PENDING'
            ]);

            return $data['authorization_url'];

        } catch (Exception $e) {
            throw new Exception("Could not initiate payment: " . $e->getMessage());
        }
    }

    public function processPaymentStatus(string $reference): void
    {
        $payment = $this->findByReference($reference);

        if ($payment->status === 'SUCCESS') {
            Log::info("Payment $reference already marked as SUCCESS. Skipping.");
            return;
        }

        try {
            $response = Http::withToken($this->secretKey)
                ->get("{$this->baseUrl}/transaction/verify/{$reference}");

            if ($response->successful()) {
                $data = $response->json('data');
                $status = $data['status'];

                if ($status === 'success') {
                    $payment->update(['status' => 'SUCCESS']);
                    
                    // Logic to update Order status
                    $this->orderService->markAsPaid($payment->orderId);
                    Log::info("Order {$payment->orderId} updated to PAID via verification.");

                } elseif ($status === 'abandoned' && $payment->created_at->lt(now()->subMinutes(30))) {
                    $payment->update(['status' => 'FAILED']);
                    $this->orderService->updateStatus($payment->orderId, 'CANCELLED');
                    Log::warn("Payment $reference timed out after 30 mins.");

                } elseif ($status === 'failed') {
                    $payment->update(['status' => 'FAILED']);
                    $this->orderService->updateStatus($payment->orderId, 'CANCELLED');
                }
            }
        } catch (Exception $e) {
            Log::error("Error verifying payment $reference: " . $e->getMessage());
        }
    }

    public function handlePaystackWebhook(string $payload, ?string $headerSignature): void
    {
        // Signature validation: hash_hmac('sha512', $payload, $secret)
        if ($headerSignature !== hash_hmac('sha512', $payload, $this->secretKey)) {
            Log::error("Invalid Paystack signature! Request rejected.");
            return;
        }

        $data = json_decode($payload, true);
        $event = $data['event'] ?? '';
        $reference = $data['data']['reference'] ?? '';

        if ($event === 'charge.success' && !empty($reference)) {
            $this->processPaymentStatus($reference);
        }
    }

    public function findByReference(string $reference): Payment
    {
        return Payment::where('reference', $reference)->firstOrFail();
    }

    public function findAll(): \Illuminate\Support\Collection { return Payment::all(); }
    public function deleteAll(): void { Payment::truncate(); }
    public function savePayment(Payment $payment): void { $payment->save(); }
    public function findByPaymentStatus(string $status): \Illuminate\Support\Collection { 
        return Payment::where('status', $status)->get(); 
    }
}
