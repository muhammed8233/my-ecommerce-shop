<?php

namespace App\Contracts;

use App\Models\Payment;
use Illuminate\Support\Collection;

interface PaymentGatewayService
{
    /**
     * @param float $totalAmount
     * @param string $currency
     * @param string $orderId
     * @return string (The Authorization URL)
     */
    public function initiatePayment(float $totalAmount, string $currency, string $orderId): string;

    public function findByReference(string $reference): ?Payment;

    public function processPaymentStatus(string $reference): void;

    public function deleteAll(): void;

    public function findAll(): Collection;

    public function savePayment(Payment $payment): void;

    /**
     * @param string $payload
     * @param string|null $headerSignature
     * @return void
     */
    public function handlePaystackWebhook(string $payload, ?string $headerSignature): void;

    /**
     * @param string $paymentStatus (Enum equivalent)
     * @return Collection
     */
    public function findByPaymentStatus(string $paymentStatus): Collection;
}
