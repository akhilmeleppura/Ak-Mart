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

          // Advanced Order Filters
          if ($request->filled('search_term')) {
              $term = trim($request->search_term);
              $query->where(function($q) use ($term) {
                  $q->where('order_number', 'LIKE', "%{$term}%")
                    ->orWhereHas('customer', function($cq) use ($term) {
                        $cq->where('name', 'LIKE', "%{$term}%")
                           ->orWhere('email', 'LIKE', "%{$term}%")
                           ->orWhere('phone', 'LIKE', "%{$term}%");
                    });
              });
          }

          if ($request->filled('branch_id')) {
              $query->where('branch_id', $request->branch_id);
          }

          if ($request->filled('payment_status')) {
              $query->where('payment_status', strtolower($request->payment_status));
          }

          if ($request->filled('order_status')) {
              $query->where('order_status', strtolower($request->order_status));
          }

          // Date Range Filter
          if ($request->filled('start_date') && $request->filled('end_date')) {
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
                  'completed'       => 2,
                  'confirmed'       => 1,
                  'processing'      => 1,
                  'pending'         => 4,
                  'cancelled'       => 3,
                  'dispatched'      => 1,
                  'delivered'       => 2,
                  'out_for_delivery'=> 3,
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
      $refunded       = Order::where('order_status', 'returned')->count();
      $failed         = Order::where('order_status', 'cancelled')->count();

      return view('content.apps.app-ecommerce-order-list', compact('pendingPayment', 'completed', 'refunded', 'failed'));
  }

  public function destroy($id)
  {
      Order::findOrFail($id)->delete();
      return response()->json(['success' => true, 'message' => 'Order deleted.']);
  }

  public function updateStatus(Request $request, $id, \App\Services\OrderManagementService $orderService)
  {
      $request->validate(['status' => 'required|string']);
      $order = Order::findOrFail($id);
      $orderService->transitionStatus($order, $request->status, auth()->id(), $request->input('reason', 'Status updated via order dashboard'));
      return response()->json(['success' => true, 'message' => 'Order status updated successfully.']);
  }

  public function cancel(Request $request, $id, \App\Services\OrderManagementService $orderService)
  {
      $request->validate([
          'reason_code' => 'required|string',
          'notes'       => 'nullable|string'
      ]);

      $order = Order::findOrFail($id);
      $orderService->cancelOrder($order, $request->reason_code, $request->notes, auth()->id());
      return response()->json(['success' => true, 'message' => 'Order cancelled and stock restored successfully.']);
  }

  public function addNote(Request $request, $id, \App\Services\OrderManagementService $orderService)
  {
      $request->validate([
          'note'        => 'required|string',
          'is_internal' => 'boolean'
      ]);

      $order = Order::findOrFail($id);
      $orderService->addNote($order, $request->note, $request->boolean('is_internal', true));
      return response()->json(['success' => true, 'message' => 'Note added to order.']);
  }

  public function reschedule(Request $request, $id, \App\Services\OrderManagementService $orderService)
  {
      $request->validate([
          'rescheduled_at' => 'required|date',
          'reason'         => 'nullable|string'
      ]);

      $order = Order::findOrFail($id);
      $orderService->rescheduleDelivery($order, $request->rescheduled_at, $request->reason, auth()->id());
      return response()->json(['success' => true, 'message' => 'Order delivery rescheduled successfully.']);
  }
}
