<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class EcommerceOrderList extends Controller
{
  public function index(Request $request)
  {
      if ($request->ajax()) {
          $query = Order::with('customer');

          // Date Filter
          if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
              $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
          }

          $orders = $query->latest()->get();
          
          $formatted = $orders->map(function($order) {
              $paymentStatusMap = [
                  'paid'           => 1,
                  'unpaid'         => 2,
                  'partially_paid' => 2,
                  'failed'         => 3,
              ];
              $orderStatusMap = [
                  'completed'  => 2,
                  'confirmed'  => 1,
                  'processing' => 1,
                  'pending'    => 4,
                  'cancelled'  => 3,
                  'dispatched' => 1,
                  'delivered'  => 2,
                  'out_for_delivery' => 3,
                  'ready_to_pickup' => 4,
              ];
              $paymentMethodMap = [
                  'credit_card' => 'mastercard',
                  'paypal'      => 'paypal',
                  'cash'        => 'visa',
                  'wallet'      => 'visa',
              ];
              
              return [
                  'id'            => $order->id,
                  'order'         => ltrim($order->order_number, 'ORD-'),
                  'date'          => $order->created_at->format('Y-m-d'),
                  'time'          => $order->created_at->format('H:i:s'),
                  'customer'      => $order->customer ? $order->customer->name : 'Guest',
                  'email'         => $order->customer ? $order->customer->email : '',
                  'avatar'        => '',
                  'payment'       => $paymentStatusMap[strtolower($order->payment_status)] ?? 2,
                  'status'        => $orderStatusMap[strtolower($order->order_status)] ?? 4,
                  'method'        => $paymentMethodMap[strtolower($order->payment_method)] ?? 'paypal',
                  'method_number' => '****',
              ];
          });
          
          return response()->json(['data' => $formatted]);
      }
      
      $pendingPayment = Order::where('payment_status', 'unpaid')->count();
      $completed      = Order::where('order_status', 'completed')->count();
      $refunded       = 0;
      $failed         = Order::where('order_status', 'cancelled')->count();

      return view('content.apps.app-ecommerce-order-list', compact('pendingPayment', 'completed', 'refunded', 'failed'));
  }

  public function destroy($id)
  {
      Order::findOrFail($id)->delete();
      return response()->json(['success' => true, 'message' => 'Order deleted.']);
  }

  public function updateStatus(Request $request, $id)
  {
      $request->validate(['status' => 'required|in:pending,processing,completed,cancelled,dispatched,out_for_delivery,delivered']);
      $order = Order::findOrFail($id);
      $order->update(['order_status' => $request->status]);
      return response()->json(['success' => true, 'message' => 'Order status updated.']);
  }
}
