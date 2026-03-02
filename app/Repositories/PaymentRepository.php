<?php

namespace App\Repositories;

use App\Models\Payment;
use App\Enums\PaymentStatus;
use Illuminate\Support\Collection;

class PaymentRepository
{
    /**
     * Equivalent to Optional<Payment> findByReference(String reference);
     */
    public function findByReference(string $reference): ?Payment
    {
        return Payment::where('reference', $reference)->first();
    }

    /**
     * Equivalent to List<Payment> findByPaymentStatus(PaymentStatus paymentStatus);
     */
    public function findByPaymentStatus(PaymentStatus|string $status): Collection
    {
        // Supports passing the Enum object or a raw string
        $value = $status instanceof PaymentStatus ? $status->value : $status;
        
        return Payment::where('payment_status', $value)->get();
    }
}
