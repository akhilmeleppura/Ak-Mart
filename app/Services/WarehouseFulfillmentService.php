<?php

namespace App\Services;

use App\Models\FulfillmentOrder;
use App\Models\FulfillmentOrderItem;
use App\Models\FulfillmentPackage;
use App\Models\Order;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class WarehouseFulfillmentService
{
    /**
     * Start picking for a fulfillment order
     */
    public function startPicking(FulfillmentOrder $fulfillment, int $pickerId): FulfillmentOrder
    {
        $fulfillment->update([
            'status'             => 'picking',
            'picker_id'          => $pickerId,
            'picking_started_at' => now(),
        ]);

        if ($fulfillment->order) {
            $fulfillment->order->update(['order_status' => 'picking']);
        }

        return $fulfillment->fresh();
    }

    /**
     * Verify an item picked via barcode scan
     */
    public function verifyPickItem(FulfillmentOrderItem $item, string $scannedBarcode, float $qtyPicked): FulfillmentOrderItem
    {
        $orderItem = $item->orderItem;
        $product = $orderItem?->product;

        if ($product && $product->barcode && trim($product->barcode) !== trim($scannedBarcode)) {
            throw ValidationException::withMessages([
                'barcode' => "Barcode mismatch. Expected {$product->barcode}, scanned {$scannedBarcode}."
            ]);
        }

        $item->update([
            'qty' => $qtyPicked,
        ]);

        return $item->fresh();
    }

    /**
     * Complete picking and advance to packed/packing
     */
    public function completePicking(FulfillmentOrder $fulfillment): FulfillmentOrder
    {
        $fulfillment->update([
            'status'               => 'picked',
            'picking_completed_at' => now(),
        ]);

        if ($fulfillment->order) {
            $fulfillment->order->update(['order_status' => 'picked']);
        }

        return $fulfillment->fresh();
    }

    /**
     * Pack items into a sealed package with barcode and verified weight
     */
    public function createPackage(FulfillmentOrder $fulfillment, float $weightKg, string $packageType = 'carton', ?int $packerId = null): FulfillmentPackage
    {
        return DB::transaction(function () use ($fulfillment, $weightKg, $packageType, $packerId) {
            $barcode = 'PKG-' . strtoupper(date('ymd') . '-' . Str::random(6));

            $package = FulfillmentPackage::create([
                'fulfillment_order_id' => $fulfillment->id,
                'package_barcode'      => $barcode,
                'weight_kg'            => $weightKg,
                'package_type'         => $packageType,
                'sealed_by_user_id'    => $packerId,
                'verification_status'  => 'verified',
            ]);

            $fulfillment->update([
                'status'               => 'packed',
                'packer_id'            => $packerId,
                'packing_completed_at' => now(),
            ]);

            if ($fulfillment->order) {
                $fulfillment->order->update(['order_status' => 'packed']);
            }

            return $package;
        });
    }

    /**
     * Mark ready for dispatch and dispatch
     */
    public function dispatchFulfillment(FulfillmentOrder $fulfillment, ?string $carrier = null, ?string $trackingNumber = null): FulfillmentOrder
    {
        $fulfillment->update([
            'status'           => 'shipped',
            'shipping_carrier' => $carrier,
            'tracking_number'  => $trackingNumber,
            'shipped_at'       => now(),
        ]);

        if ($fulfillment->order) {
            $fulfillment->order->update(['order_status' => 'dispatched']);
        }

        return $fulfillment->fresh();
    }
}
