<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\CreditNote;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderManagementService
{
    /**
     * Allowed status transitions
     */
    protected array $allowedTransitions = [
        'pending'          => ['processing', 'cancelled'],
        'processing'       => ['picking', 'packed', 'dispatched', 'cancelled'],
        'picking'          => ['picked', 'cancelled'],
        'picked'           => ['packing', 'packed', 'cancelled'],
        'packing'          => ['packed', 'cancelled'],
        'packed'           => ['ready_for_dispatch', 'dispatched', 'cancelled'],
        'ready_for_dispatch' => ['dispatched', 'cancelled'],
        'dispatched'       => ['in_transit', 'out_for_delivery', 'delivered', 'failed'],
        'in_transit'       => ['out_for_delivery', 'delivered', 'failed'],
        'out_for_delivery' => ['delivered', 'failed'],
        'delivered'        => ['completed', 'returned'],
        'completed'        => ['returned'],
        'failed'           => ['rescheduled', 'cancelled'],
        'rescheduled'      => ['out_for_delivery', 'dispatched', 'cancelled'],
        'cancelled'        => [], // Terminal
        'returned'         => [], // Terminal
    ];

    /**
     * Transition order to a new status with validation and timeline record
     */
    public function transitionStatus(Order $order, string $newStatus, ?int $userId = null, ?string $reason = null, array $metadata = []): Order
    {
        $currentStatus = strtolower($order->order_status ?? 'pending');
        $targetStatus = strtolower($newStatus);

        if ($currentStatus === $targetStatus) {
            return $order;
        }

        // Validate state machine rule
        if (isset($this->allowedTransitions[$currentStatus]) && !in_array($targetStatus, $this->allowedTransitions[$currentStatus])) {
            throw ValidationException::withMessages([
                'status' => "Invalid order transition from '{$currentStatus}' to '{$targetStatus}'."
            ]);
        }

        return DB::transaction(function () use ($order, $currentStatus, $targetStatus, $userId, $reason, $metadata) {
            $order->update(['order_status' => $targetStatus]);

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'from_status' => $currentStatus,
                'to_status'   => $targetStatus,
                'user_id'     => $userId,
                'actor_type'  => $userId ? 'staff' : 'system',
                'reason'      => $reason,
                'metadata'    => $metadata,
            ]);

            return $order->fresh();
        });
    }

    /**
     * Cancel entire order or partial items with reason code and automatic inventory restoration
     */
    public function cancelOrder(Order $order, string $reasonCode, ?string $notes = null, ?int $userId = null): Order
    {
        $currentStatus = strtolower($order->order_status);
        if (in_array($currentStatus, ['dispatched', 'in_transit', 'out_for_delivery', 'delivered', 'completed'])) {
            throw ValidationException::withMessages([
                'order' => 'Cannot cancel order that has already been dispatched or delivered.'
            ]);
        }

        return DB::transaction(function () use ($order, $reasonCode, $notes, $userId) {
            $order->update([
                'order_status'             => 'cancelled',
                'cancellation_reason_code' => $reasonCode,
                'cancellation_notes'       => $notes,
            ]);

            // Restore physical inventory for all uncancelled items
            foreach ($order->items as $item) {
                if ($item->item_status !== 'cancelled') {
                    $item->update([
                        'item_status'         => 'cancelled',
                        'cancelled_qty'       => $item->qty,
                        'cancellation_reason' => $reasonCode,
                    ]);

                    $product = Product::lockForUpdate()->find($item->product_id);
                    if ($product) {
                        StockMovement::record(
                            $product->id,
                            (int)$item->qty,
                            'adjustment',
                            "Restock from Order #{$order->order_number} cancellation",
                            null,
                            $order->branch_id,
                            'Order',
                            $order->id,
                            $userId
                        );
                    }
                }
            }

            OrderStatusHistory::create([
                'order_id'    => $order->id,
                'from_status' => $order->getOriginal('order_status'),
                'to_status'   => 'cancelled',
                'user_id'     => $userId,
                'actor_type'  => $userId ? 'staff' : 'customer',
                'reason'      => "Reason: {$reasonCode} - {$notes}",
            ]);

            return $order->fresh();
        });
    }

    /**
     * Partial cancellation of a specific order item line
     */
    public function cancelOrderItem(OrderItem $item, float $cancelQty, string $reason, ?int $userId = null): OrderItem
    {
        return DB::transaction(function () use ($item, $cancelQty, $reason, $userId) {
            $remainingQty = max(0, (float)$item->qty - (float)$item->cancelled_qty);
            if ($cancelQty > $remainingQty) {
                throw ValidationException::withMessages([
                    'qty' => 'Cannot cancel more quantity than remaining.'
                ]);
            }

            $newCancelledQty = (float)$item->cancelled_qty + $cancelQty;
            $itemStatus = ($newCancelledQty >= (float)$item->qty) ? 'cancelled' : 'partially_cancelled';

            $item->update([
                'cancelled_qty'       => $newCancelledQty,
                'item_status'         => $itemStatus,
                'cancellation_reason' => $reason,
            ]);

            // Restock the cancelled portion
            $product = Product::lockForUpdate()->find($item->product_id);
            if ($product) {
                StockMovement::record(
                    $product->id,
                    (int)$cancelQty,
                    'adjustment',
                    "Partial cancellation for Order item #{$item->id}",
                    null,
                    $item->order->branch_id ?? 1,
                    'OrderItem',
                    $item->id,
                    $userId
                );
            }

            return $item->fresh();
        });
    }

    /**
     * Add internal or customer-visible notes
     */
    public function addNote(Order $order, string $note, bool $isInternal = true): Order
    {
        $field = $isInternal ? 'internal_notes' : 'customer_notes';
        $existing = $order->{$field} ? $order->{$field} . "\n---\n" : '';
        $timestamp = now()->toDateTimeString();
        $updated = "{$existing}[{$timestamp}] {$note}";

        $order->update([$field => $updated]);
        return $order->fresh();
    }

    /**
     * Reschedule delivery time
     */
    public function rescheduleDelivery(Order $order, string $rescheduledAt, ?string $reason = null, ?int $userId = null): Order
    {
        $order->update([
            'rescheduled_delivery_at' => $rescheduledAt,
            'order_status'            => 'rescheduled',
        ]);

        OrderStatusHistory::create([
            'order_id'    => $order->id,
            'from_status' => $order->getOriginal('order_status'),
            'to_status'   => 'rescheduled',
            'user_id'     => $userId,
            'reason'      => "Rescheduled to {$rescheduledAt}. " . ($reason ?? ''),
        ]);

        return $order->fresh();
    }
}
