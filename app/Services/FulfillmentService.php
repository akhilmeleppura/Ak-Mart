<?php

namespace App\Services;

use App\Models\Order;
use App\Models\FulfillmentOrder;
use App\Models\FulfillmentOrderItem;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class FulfillmentService
{
    /**
     * Create split or full fulfillment order
     */
    public function createFulfillmentOrder(int $orderId, array $orderItemIdsWithQty, ?int $warehouseId = null, ?int $branchId = null): FulfillmentOrder
    {
        return DB::transaction(function () use ($orderId, $orderItemIdsWithQty, $warehouseId, $branchId) {
            $fulfillment = FulfillmentOrder::create([
                'fulfillment_number' => 'FUL-' . strtoupper(Str::random(8)),
                'order_id'           => $orderId,
                'warehouse_id'       => $warehouseId,
                'branch_id'          => $branchId,
                'status'             => 'unfulfilled',
            ]);

            foreach ($orderItemIdsWithQty as $item) {
                FulfillmentOrderItem::create([
                    'fulfillment_order_id' => $fulfillment->id,
                    'order_item_id'        => $item['order_item_id'],
                    'qty'                  => $item['qty'],
                ]);
            }

            return $fulfillment;
        });
    }

    /**
     * Update shipment status and tracking details
     */
    public function updateShipmentStatus(int $fulfillmentOrderId, string $status, ?string $trackingNumber = null, ?string $carrier = null): FulfillmentOrder
    {
        $fulfillment = FulfillmentOrder::findOrFail($fulfillmentOrderId);
        $updateData = ['status' => $status];

        if ($trackingNumber) {
            $updateData['tracking_number'] = $trackingNumber;
        }
        if ($carrier) {
            $updateData['shipping_carrier'] = $carrier;
        }
        if ($status === 'shipped') {
            $updateData['shipped_at'] = now();
        }
        if ($status === 'delivered') {
            $updateData['delivered_at'] = now();
        }

        $fulfillment->update($updateData);

        // Check if all fulfillment orders for the parent order are delivered
        $order = $fulfillment->order;
        if ($order) {
            $allDelivered = $order->fulfillments()->where('status', '!=', 'delivered')->count() === 0;
            if ($allDelivered) {
                $order->update(['order_status' => 'completed']);
            } elseif ($status === 'shipped') {
                $order->update(['order_status' => 'shipped']);
            }
        }

        return $fulfillment;
    }
}
