<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Auth;

class DriverOrderController extends Controller
{
    /**
     * Assign the authenticated driver to an order.
     */
    public function assign($orderId)
    {
        $driverId = Auth::id();
        $order = Order::findOrFail($orderId);

        // Allow assignment if unassigned or pending/processing
        if ($order->driver_id && $order->driver_id !== $driverId) {
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Order is already assigned to another driver.'], 400);
            }
            return back()->with('error', 'Order is already assigned to another driver.');
        }

        $order->driver_id = $driverId;
        $order->order_status = 'assigned';
        $order->save();

        event(new OrderStatusUpdated($order));

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order #' . $order->order_number . ' claimed successfully!',
                'order'   => $order
            ]);
        }

        return back()->with('success', 'Order #' . $order->order_number . ' claimed successfully!');
    }

    /**
     * Update the status of an order by the driver.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'order_id' => 'required',
            'status'   => 'required|in:assigned,picked_up,in_transit,delivered,failed,Delivered',
        ]);

        $orderId = $request->input('order_id');
        $newStatus = strtolower($request->input('status'));
        $driverId = Auth::id();

        $order = Order::where('id', $orderId)
            ->where('driver_id', $driverId)
            ->firstOrFail();

        $order->order_status = $newStatus;

        // If delivered and payment was COD, mark as paid
        if (in_array($newStatus, ['delivered', 'completed']) && $order->payment_method === 'cod') {
            $order->payment_status = 'paid';
        }

        $order->save();

        event(new OrderStatusUpdated($order));

        // Automated Omnichannel Notifications for Order Status Progression
        try {
            $customer = $order->customer;
            $recipientEmail = $customer?->email;
            $recipientPhone = $customer?->phone;
            $customerName = $customer?->name ?? 'Valued Customer';
            $trackingUrl = route('storefront.track', ['order_number' => $order->order_number]);
            $commService = app(\App\Services\CommunicationService::class);

            $vars = [
                'customer_name'   => $customerName,
                'order_number'    => $order->order_number,
                'order_total'     => number_format($order->total_amount, 2),
                'carrier'         => 'AK-Mart Express Courier (' . (Auth::user()?->name ?? 'Driver') . ')',
                'tracking_number' => $order->order_number,
                'tracking_url'    => $trackingUrl,
                'status'          => ucfirst(str_replace('_', ' ', $newStatus)),
                'store_name'      => config('app.name', 'AK-Mart'),
            ];

            if ($newStatus === 'in_transit' || $newStatus === 'picked_up') {
                if ($recipientEmail) $commService->send('email', $recipientEmail, 'order_shipped', $vars);
                if ($recipientPhone) $commService->send('whatsapp', $recipientPhone, 'order_shipped', $vars);
            } elseif (in_array($newStatus, ['delivered', 'completed'])) {
                if ($recipientEmail) $commService->send('email', $recipientEmail, 'order_confirmation', $vars);
                if ($recipientPhone) $commService->send('whatsapp', $recipientPhone, 'order_confirmation', $vars);
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning("Driver status communication failure: " . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Order #' . $order->order_number . ' status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)),
                'status'  => $newStatus
            ]);
        }

        return back()->with('success', 'Order #' . $order->order_number . ' status updated to ' . ucfirst(str_replace('_', ' ', $newStatus)));
    }
}
