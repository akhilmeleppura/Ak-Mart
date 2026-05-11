<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EcommerceCustomerDetailsOverview extends Controller
{
  public function index(Request $request, $id = null)
  {
      $customer = $id ? \App\Models\User::find($id) : \App\Models\User::first();
      
      if (!$customer) {
          return redirect()->route('app-ecommerce-customer-all');
      }

      if ($request->ajax()) {
          $orders = \App\Models\Order::where('user_id', $customer->id)->get();
          $statusMap = [
              'pending' => 1,
              'processing' => 2,
              'completed' => 3,
              'cancelled' => 4,
          ];

          $formatted = $orders->map(function($order) use ($statusMap) {
              return [
                  'id' => $order->id,
                  'order' => ltrim($order->order_number, 'ORD-'),
                  'date' => $order->created_at->format('M d, Y'),
                  'status' => $statusMap[$order->order_status] ?? 1,
                  'spent' => '$' . number_format($order->total_amount, 2)
              ];
          });
          return response()->json(['data' => $formatted]);
      }

      $ordersCount = \App\Models\Order::where('user_id', $customer->id)->count();
      $totalSpent = \App\Models\Order::where('user_id', $customer->id)->sum('total_amount');

      return view('content.apps.app-ecommerce-customer-details-overview', compact('customer', 'ordersCount', 'totalSpent'));
  }
}
