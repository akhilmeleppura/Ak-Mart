<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PaymentReconciliation;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PaymentReconciliationService
{
    /**
     * Record and reconcile an incoming payment webhook idempotently
     */
    public function recordPayment(
        string $gateway,
        string $transactionId,
        float $amount,
        ?Order $order = null,
        float $gatewayFee = 0.0,
        string $status = 'captured',
        bool $signatureVerified = true,
        ?string $idempotencyKey = null,
        array $rawPayload = []
    ): PaymentReconciliation {
        return DB::transaction(function () use (
            $gateway,
            $transactionId,
            $amount,
            $order,
            $gatewayFee,
            $status,
            $signatureVerified,
            $idempotencyKey,
            $rawPayload
        ) {
            // Idempotency check: if key or transaction_id exists, return existing
            $existing = PaymentReconciliation::where('transaction_id', $transactionId)
                ->orWhere(function ($q) use ($idempotencyKey) {
                    if ($idempotencyKey) {
                        $q->where('idempotency_key', $idempotencyKey);
                    }
                })
                ->first();

            if ($existing) {
                return $existing;
            }

            $netSettlement = max(0, $amount - $gatewayFee);

            $reconciliation = PaymentReconciliation::create([
                'gateway'            => strtolower($gateway),
                'transaction_id'     => $transactionId,
                'order_id'           => $order?->id,
                'amount'             => $amount,
                'currency'           => $order?->currency ?? 'USD',
                'gateway_fee'        => $gatewayFee,
                'net_settlement'     => $netSettlement,
                'status'             => $status,
                'signature_verified' => $signatureVerified,
                'idempotency_key'    => $idempotencyKey,
                'raw_payload'        => $rawPayload,
            ]);

            // Update order payment status if captured
            if ($order && $status === 'captured') {
                $order->update([
                    'payment_status' => 'paid',
                ]);
            }

            return $reconciliation;
        });
    }

    /**
     * Verify Stripe webhook signature
     */
    public function verifyStripeSignature(string $payload, string $signatureHeader, string $secret): bool
    {
        if (empty($signatureHeader) || empty($secret)) {
            return false;
        }

        // Expected format: t=timestamp,v1=signature
        $parts = explode(',', $signatureHeader);
        $timestamp = null;
        $sig = null;

        foreach ($parts as $part) {
            $pair = explode('=', trim($part), 2);
            if ($pair[0] === 't') $timestamp = $pair[1];
            if ($pair[0] === 'v1') $sig = $pair[1];
        }

        if (!$timestamp || !$sig) {
            return false;
        }

        $signedPayload = "{$timestamp}.{$payload}";
        $expected = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expected, $sig);
    }
}
