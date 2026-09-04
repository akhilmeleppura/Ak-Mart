<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LastMileDeliveryService
{
    /**
     * Assign a driver to the order for dispatch
     */
    public function assignDriver(Order $order, int $driverId, ?int $assignedBy = null): Order
    {
        $driver = User::find($driverId);
        if (!$driver) {
            throw ValidationException::withMessages(['driver' => 'Driver not found.']);
        }

        return DB::transaction(function () use ($order, $driver, $assignedBy) {
            $otp = (string) random_int(100000, 999999);

            $order->update([
                'driver_id'    => $driver->id,
                'delivery_otp' => $otp,
                'order_status' => 'out_for_delivery',
            ]);

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'from_status' => $order->getOriginal('order_status'),
                'to_status'   => 'out_for_delivery',
                'user_id'     => $assignedBy,
                'actor_type'  => $assignedBy ? 'staff' : 'system',
                'reason'      => "Assigned to driver {$driver->name} (#{$driver->id})",
            ]);

            return $order->fresh();
        });
    }

    /**
     * Generate or regenerate a 6-digit delivery OTP
     */
    public function generateDeliveryOtp(Order $order): string
    {
        $otp = (string) random_int(100000, 999999);
        $order->update(['delivery_otp' => $otp]);
        return $otp;
    }

    /**
     * Verify OTP and record Proof of Delivery (Photo & Signature)
     */
    public function verifyAndCompleteDelivery(
        Order $order,
        string $submittedOtp,
        ?string $signatureUrl = null,
        ?string $photoUrl = null,
        ?int $driverId = null
    ): Order {
        if (trim($order->delivery_otp) !== trim($submittedOtp)) {
            throw ValidationException::withMessages([
                'otp' => 'Invalid delivery OTP verification code.'
            ]);
        }

        return DB::transaction(function () use ($order, $signatureUrl, $photoUrl, $driverId) {
            $order->update([
                'order_status'              => 'delivered',
                'delivery_signature_url'    => $signatureUrl,
                'delivery_proof_photo_url'  => $photoUrl,
            ]);

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'from_status' => 'out_for_delivery',
                'to_status'   => 'delivered',
                'user_id'     => $driverId,
                'actor_type'  => 'driver',
                'reason'      => 'Delivery completed with verified OTP and proof',
                'metadata'    => [
                    'signature' => (bool)$signatureUrl,
                    'photo'     => (bool)$photoUrl,
                ]
            ]);

            AuditLogService::log(
                'delivery.completed',
                'Order',
                $order->id,
                ['order_status' => 'out_for_delivery'],
                ['order_status' => 'delivered', 'proof_photo' => (bool)$photoUrl, 'signature' => (bool)$signatureUrl],
                $driverId,
                'driver'
            );

            return $order->fresh();
        });
    }

    /**
     * Record a failed delivery attempt with reason code
     */
    public function recordDeliveryFailure(
        Order $order,
        string $failureReason,
        ?int $driverId = null,
        ?string $notes = null
    ): Order {
        return DB::transaction(function () use ($order, $failureReason, $driverId, $notes) {
            $attempts = (int)$order->delivery_attempts + 1;

            $order->update([
                'order_status'           => 'failed',
                'delivery_failed_reason' => $failureReason,
                'delivery_attempts'      => $attempts,
                'internal_notes'         => ($order->internal_notes ? $order->internal_notes . "\n" : '') .
                                            "[" . now()->toDateTimeString() . "] Delivery attempt #{$attempts} failed: {$failureReason}. {$notes}",
            ]);

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'from_status' => $order->getOriginal('order_status'),
                'to_status'   => 'failed',
                'user_id'     => $driverId,
                'actor_type'  => 'driver',
                'reason'      => "Delivery attempt #{$attempts} failed: {$failureReason}",
            ]);

            return $order->fresh();
        });
    }
}
