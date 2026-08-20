<?php

namespace App\Http\Controllers\Driver;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;

class DriverDashboardController extends Controller
{
    /**
     * Show the driver dashboard.
     */
    public function index(Request $request)
    {
        $driver = Auth::user();
        $driverId = $driver->id;

        // Active assignments: assigned, picked_up, in_transit
        $activeOrders = Order::with(['customer', 'items.product', 'deliverySlot'])
            ->where('driver_id', $driverId)
            ->whereIn('order_status', ['assigned', 'Assigned', 'picked_up', 'in_transit', 'Processing'])
            ->orderBy('id', 'desc')
            ->get();

        // Completed deliveries
        $deliveredOrders = Order::with(['customer', 'items.product'])
            ->where('driver_id', $driverId)
            ->whereIn('order_status', ['delivered', 'Delivered', 'completed', 'Completed'])
            ->orderBy('id', 'desc')
            ->take(20)
            ->get();

        // Unassigned pending orders available for pickup
        $availableOrders = Order::with(['customer', 'items.product', 'deliverySlot'])
            ->whereNull('driver_id')
            ->whereIn('order_status', ['pending', 'Pending', 'Processing', 'processing'])
            ->orderBy('id', 'desc')
            ->take(15)
            ->get();

        // Key stats
        $stats = [
            'active_count'    => $activeOrders->count(),
            'delivered_today' => Order::where('driver_id', $driverId)
                ->whereIn('order_status', ['delivered', 'Delivered', 'completed', 'Completed'])
                ->whereDate('updated_at', today())
                ->count(),
            'total_delivered' => $deliveredOrders->count(),
            'cod_to_collect'  => $activeOrders->where('payment_method', 'cod')->where('payment_status', '!=', 'paid')->sum('total_amount'),
        ];

        return view('driver.dashboard', compact('driver', 'activeOrders', 'deliveredOrders', 'availableOrders', 'stats'));
    }
}
