<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\PaymentGatewayService;
use App\Services\PaymentReconciliationService;
use App\Services\OrderManagementService;
use App\Services\CommissionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming payment webhooks with cryptographic HMAC signature verification
     */
    public function handle(
        Request $request,
        PaymentGatewayService $gatewayService,
        PaymentReconciliationService $reconciliationService,
        OrderManagementService $oms,
        CommissionService $commissionService
    ) {
        $rawPayload = $request->getContent();
        $payload = $request->all();
        $gateway = strtolower($request->header('X-Payment-Gateway', $payload['gateway'] ?? 'stripe'));

        Log::info("Incoming {$gateway} payment webhook received", ['ip' => $request->ip()]);

        // 1. Cryptographic HMAC Signature Verification
        $isVerified = false;

        if ($gateway === 'stripe') {
            $sigHeader = $request->header('Stripe-Signature');
            $secret = config('services.stripe.webhook_secret') ?: env('STRIPE_WEBHOOK_SECRET', 'whsec_test_secret_123');
            $isVerified = $gatewayService->verifyStripeSignature($rawPayload, $sigHeader, $secret);
        } elseif ($gateway === 'razorpay') {
            $sigHeader = $request->header('X-Razorpay-Signature');
            $secret = config('services.razorpay.webhook_secret') ?: env('RAZORPAY_WEBHOOK_SECRET', 'rzp_sec_test_secret_123');
            $isVerified = $gatewayService->verifyRazorpaySignature($rawPayload, $sigHeader, $secret);
        } elseif ($gateway === 'sandbox_upi') {
            // Sandbox UPI token verification
            $upiSecret = config('services.upi.secret', 'sandbox_upi_secret_999');
            $authHeader = $request->header('X-UPI-Auth-Token');
            $isVerified = hash_equals($upiSecret, (string)$authHeader);
        }

        if (!$isVerified) {
            Log::warning("Rejected unverified {$gateway} webhook signature from {$request->ip()}");
            return response()->json([
                'error'   => 'Invalid webhook cryptographic signature.',
                'gateway' => $gateway,
            ], 401);
        }

        // 2. Extract Transaction & Order Metadata
        $orderNumber = null;
        $transactionId = null;
        $amount = 0.0;
        $fee = 0.0;

        if ($gateway === 'stripe') {
            $eventObject = $payload['data']['object'] ?? [];
            $orderNumber = $eventObject['client_reference_id'] ?? $eventObject['metadata']['order_number'] ?? ($payload['order_number'] ?? null);
            $transactionId = $eventObject['payment_intent'] ?? $eventObject['id'] ?? ('tx_stripe_' . uniqid());
            $amount = isset($eventObject['amount_total']) ? ($eventObject['amount_total'] / 100) : (float)($payload['amount'] ?? 0);
            $fee = isset($eventObject['fee']) ? ($eventObject['fee'] / 100) : round($amount * 0.029, 2);
        } elseif ($gateway === 'razorpay') {
            $paymentEntity = $payload['payload']['payment']['entity'] ?? $payload;
            $orderNumber = $paymentEntity['notes']['order_number'] ?? ($payload['order_number'] ?? null);
            $transactionId = $paymentEntity['id'] ?? ('tx_rzp_' . uniqid());
            $amount = isset($paymentEntity['amount']) ? ($paymentEntity['amount'] / 100) : (float)($payload['amount'] ?? 0);
            $fee = isset($paymentEntity['fee']) ? ($paymentEntity['fee'] / 100) : round($amount * 0.02, 2);
        } elseif ($gateway === 'sandbox_upi') {
            $orderNumber = $payload['order_number'] ?? null;
            $transactionId = $payload['transaction_ref'] ?? ('upi_' . uniqid());
            $amount = (float)($payload['amount'] ?? 0);
            $fee = 0.0; // UPI zero-fee
        }

        if (!$orderNumber) {
            return response()->json(['message' => 'Verified webhook received without order reference.'], 200);
        }

        $order = Order::where('order_number', $orderNumber)->first();
        if (!$order) {
            return response()->json(['error' => "Order {$orderNumber} not found."], 404);
        }

        // 3. Idempotent Payment Reconciliation
        $idempotencyKey = "WEBHOOK-{$gateway}-{$transactionId}";
        $reconciliation = $reconciliationService->recordPayment(
            $gateway,
            $transactionId,
            $amount ?: (float)$order->total_amount,
            $order,
            $fee,
            'captured',
            true,
            $idempotencyKey,
            $payload
        );

        // 4. Update Order Status and State Machine
        $order->update([
            'payment_status' => 'paid',
        ]);

        if ($order->order_status === 'pending') {
            $oms->transitionStatus($order, 'processing', null, "Automated transition via verified {$gateway} webhook");
        }

        // 5. Vendor Commission Ledger Split
        try {
            $commissionService->processOrder($order);
        } catch (\Throwable $e) {
            Log::warning("Commission calculation notice for Order #{$order->order_number}: " . $e->getMessage());
        }

        return response()->json([
            'success'            => true,
            'message'            => "Order {$orderNumber} verified and reconciled.",
            'reconciliation_id'  => $reconciliation->id,
            'order_status'       => $order->fresh()->order_status,
            'payment_status'     => $order->fresh()->payment_status,
        ], 200);
    }
}
