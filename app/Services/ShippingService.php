<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Shipment;
use App\Models\ShippingMethod;
use Illuminate\Support\Facades\Log;

class ShippingService
{
    /**
     * Create a shipment for an order using a specific method.
     */
    public function createShipment(Order $order, ShippingMethod $method)
    {
        $carrier = $method->carrier_code;

        try {
            switch ($carrier) {
                case 'shiprocket':
                    return $this->processShiprocket($order, $method);
                case 'delhivery':
                    return $this->processDelhivery($order, $method);
                case 'bluedart':
                    return $this->processBlueDart($order, $method);
                case 'fedex':
                    return $this->processFedEx($order, $method);
                default:
                    return $this->processLocal($order, $method);
            }
        } catch (\Exception $e) {
            Log::error("Shipping Error for Order #{$order->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a destination pincode is serviceable by carrier
     */
    public function checkServiceability(string $pincode, ?string $carrier = 'delhivery'): array
    {
        $cleanPincode = preg_replace('/[^0-9]/', '', $pincode);
        if (strlen($cleanPincode) !== 6) {
            return ['serviceable' => false, 'message' => 'Invalid Indian 6-digit pincode format.'];
        }

        return [
            'serviceable'  => true,
            'pincode'      => $cleanPincode,
            'carrier'      => $carrier ?: 'Delhivery Express',
            'cod_available'=> true,
            'estimated_days'=> rand(2, 4),
        ];
    }

    protected function processShiprocket(Order $order, ShippingMethod $method)
    {
        $trackingId = 'SR-' . strtoupper(uniqid());
        
        return Shipment::create([
            'order_id'           => $order->id,
            'shipping_method_id' => $method->id,
            'tracking_id'        => $trackingId,
            'status'             => 'pending',
            'carrier_response'   => ['mock_shiprocket_id' => rand(10000, 99999)]
        ]);
    }

    protected function processDelhivery(Order $order, ShippingMethod $method)
    {
        $trackingId = 'DLV-' . strtoupper(uniqid());
        
        return Shipment::create([
            'order_id'           => $order->id,
            'shipping_method_id' => $method->id,
            'tracking_id'        => $trackingId,
            'status'             => 'pending',
            'label_url'          => 'https://example.com/delhivery-waybill-mock.pdf',
            'carrier_response'   => ['waybill' => $trackingId, 'pickup_token' => rand(100000, 999999)]
        ]);
    }

    protected function processBlueDart(Order $order, ShippingMethod $method)
    {
        $trackingId = 'BD-' . strtoupper(uniqid());
        
        return Shipment::create([
            'order_id'           => $order->id,
            'shipping_method_id' => $method->id,
            'tracking_id'        => $trackingId,
            'status'             => 'pending',
            'carrier_response'   => ['awb_number' => $trackingId]
        ]);
    }

    protected function processFedEx(Order $order, ShippingMethod $method)
    {
        $trackingId = 'FX-' . strtoupper(uniqid());
        
        return Shipment::create([
            'order_id'           => $order->id,
            'shipping_method_id' => $method->id,
            'tracking_id'        => $trackingId,
            'status'             => 'pending',
            'label_url'          => 'https://example.com/fedex-label-mock.pdf',
            'carrier_response'   => ['fedex_tracking_number' => $trackingId]
        ]);
    }

    protected function processLocal(Order $order, ShippingMethod $method)
    {
        return Shipment::create([
            'order_id'           => $order->id,
            'shipping_method_id' => $method->id,
            'status'             => 'pending'
        ]);
    }
}
