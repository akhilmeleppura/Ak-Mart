<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentWebhookController extends Controller
{
    /**
     * Handle incoming payment webhooks (Stripe/PayPal style)
     */
    public function handle(Request $request)
    {
        $payload = $request->all();
        
        Log::info('Payment Webhook Received', $payload);

        // Example: Stripe style 'checkout.session.completed'
        $orderNumber = $payload['data']['object']['client_reference_id'] ?? $payload['order_id'] ?? null;

        if ($orderNumber) {
            $order = Order::where('order_number', $orderNumber)->first();

            if ($order) {
                // Update order status based on payment success
                $order->update([
                    'order_status' => 'Confirmed', // Move from Pending to Confirmed
                ]);

                // Process the multi-vendor commission split
                $commissionService = new \App\Services\CommissionService();
                $commissionService->processOrder($order);

                return response()->json(['message' => 'Order ' . $orderNumber . ' updated to Confirmed. Commission processed.']);
            }
        }

        return response()->json(['message' => 'Webhook received but no order matched.'], 200);
    }
}
