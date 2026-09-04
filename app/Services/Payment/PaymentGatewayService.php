<?php

namespace App\Services\Payment;

use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PaymentGatewayService
{
    /**
     * Create a payment intent or checkout session for the given order and gateway
     */
    public function createPaymentSession(Order $order, string $gateway): array
    {
        return match (strtolower($gateway)) {
            'stripe'      => $this->createStripeSession($order),
            'razorpay'    => $this->createRazorpayOrder($order),
            'sandbox_upi' => $this->createSandboxUpiIntent($order),
            default       => throw ValidationException::withMessages([
                'payment_method' => "Unsupported payment gateway: {$gateway}"
            ]),
        };
    }

    /**
     * Generate a Stripe Checkout Session
     */
    protected function createStripeSession(Order $order): array
    {
        $secretKey = config('services.stripe.secret') ?? env('STRIPE_SECRET_KEY');
        $sessionReference = 'cs_test_' . bin2hex(random_bytes(16));
        $currency = strtolower(config('app.currency', 'inr'));
        $amountInCents = (int) round($order->total_amount * 100);

        Log::info("Initiated Stripe session for Order #{$order->order_number} (Amount: {$amountInCents} cents)");

        return [
            'gateway'           => 'stripe',
            'order_id'          => $order->id,
            'order_number'      => $order->order_number,
            'session_id'        => $sessionReference,
            'amount'            => $order->total_amount,
            'currency'          => $currency,
            'checkout_url'      => url("/checkout/stripe/pay?session_id={$sessionReference}&order={$order->order_number}"),
            'status'            => 'requires_payment',
        ];
    }

    /**
     * Generate a Razorpay Order
     */
    protected function createRazorpayOrder(Order $order): array
    {
        $keyId = config('services.razorpay.key') ?? env('RAZORPAY_KEY_ID', 'rzp_test_sample');
        $razorpayOrderId = 'order_' . bin2hex(random_bytes(10));
        $amountInPaise = (int) round($order->total_amount * 100);

        Log::info("Initiated Razorpay order for Order #{$order->order_number} (Amount: {$amountInPaise} paise)");

        return [
            'gateway'           => 'razorpay',
            'order_id'          => $order->id,
            'order_number'      => $order->order_number,
            'razorpay_order_id' => $razorpayOrderId,
            'amount'            => $order->total_amount,
            'amount_subunits'   => $amountInPaise,
            'currency'          => 'INR',
            'key_id'            => $keyId,
            'checkout_url'      => url("/checkout/razorpay/pay?order={$order->order_number}&razorpay_order_id={$razorpayOrderId}"),
            'status'            => 'created',
        ];
    }

    /**
     * Generate a Sandbox UPI QR & VPA intent
     */
    protected function createSandboxUpiIntent(Order $order): array
    {
        $txnRef = 'UPI-' . strtoupper(bin2hex(random_bytes(6)));
        $merchantVpa = config('services.upi.vpa', 'akmart@sandboxupi');
        $upiIntentUrl = "upi://pay?pa={$merchantVpa}&pn=AK-Mart&tr={$txnRef}&am={$order->total_amount}&cu=INR&tn=Order_{$order->order_number}";

        Log::info("Initiated Sandbox UPI for Order #{$order->order_number} (Ref: {$txnRef})");

        return [
            'gateway'        => 'sandbox_upi',
            'order_id'       => $order->id,
            'order_number'   => $order->order_number,
            'transaction_ref'=> $txnRef,
            'amount'         => $order->total_amount,
            'vpa'            => $merchantVpa,
            'upi_intent_url' => $upiIntentUrl,
            'checkout_url'   => url("/checkout/upi/pay?order={$order->order_number}&ref={$txnRef}"),
            'status'         => 'awaiting_upi_pin',
        ];
    }

    /**
     * Verify Stripe Webhook Signature (HMAC SHA256)
     */
    public function verifyStripeSignature(string $payload, ?string $sigHeader, ?string $secret = null): bool
    {
        $secret = $secret ?: (config('services.stripe.webhook_secret') ?: env('STRIPE_WEBHOOK_SECRET'));
        if (!$sigHeader || !$secret) {
            return false;
        }

        // Stripe signature header format: t=1492774577,v1=5257a869e7ecebeda32affa62cd4908f483a625b4cf6fbe4194d380860f33339
        $items = explode(',', $sigHeader);
        $timestamp = null;
        $signature = null;

        foreach ($items as $item) {
            $parts = explode('=', trim($item), 2);
            if (count($parts) === 2) {
                if ($parts[0] === 't') $timestamp = $parts[1];
                if ($parts[0] === 'v1') $signature = $parts[1];
            }
        }

        if (!$timestamp || !$signature) {
            return false;
        }

        $signedPayload = "{$timestamp}.{$payload}";
        $expectedSignature = hash_hmac('sha256', $signedPayload, $secret);

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Verify Razorpay Webhook Signature (HMAC SHA256)
     */
    public function verifyRazorpaySignature(string $payload, ?string $signature, ?string $secret = null): bool
    {
        $secret = $secret ?: (config('services.razorpay.webhook_secret') ?: env('RAZORPAY_WEBHOOK_SECRET'));
        if (!$signature || !$secret) {
            return false;
        }

        $expectedSignature = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expectedSignature, $signature);
    }
}
