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

    protected function processShiprocket(Order $order, ShippingMethod $method)
    {
        // Mocking Shiprocket API Call
        // In real implementation, you'd use Guzzle to hit Shiprocket endpoints
        $trackingId = 'SR-' . strtoupper(uniqid());
        
        return Shipment::create([
            'order_id' => $order->id,
            'shipping_method_id' => $method->id,
            'tracking_id' => $trackingId,
            'status' => 'pending',
            'carrier_response' => ['mock_shiprocket_id' => rand(10000, 99999)]
        ]);
    }

    protected function processFedEx(Order $order, ShippingMethod $method)
    {
        // Mocking FedEx API Call
        $trackingId = 'FX-' . strtoupper(uniqid());
        
        return Shipment::create([
            'order_id' => $order->id,
            'shipping_method_id' => $method->id,
            'tracking_id' => $trackingId,
            'status' => 'pending',
            'label_url' => 'https://example.com/fedex-label-mock.pdf',
            'carrier_response' => ['fedex_tracking_number' => $trackingId]
        ]);
    }

    protected function processLocal(Order $order, ShippingMethod $method)
    {
        return Shipment::create([
            'order_id' => $order->id,
            'shipping_method_id' => $method->id,
            'status' => 'pending'
        ]);
    }
}
