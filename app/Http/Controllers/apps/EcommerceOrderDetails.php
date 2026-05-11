<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EcommerceOrderDetails extends Controller
{
  public function index(Request $request, $id = null)
  {
      $order = $id ? \App\Models\Order::with(['items.product.category', 'customer'])->find($id) : \App\Models\Order::with(['items.product.category', 'customer'])->latest()->first();

      if (!$order) {
          return redirect()->route('app-ecommerce-order-list');
      }

      // Explicit tenant boundary authorization
      $user = auth()->user();
      $currentBranch = session('branch_id') ?? ($user ? $user->branch_id : null);
      
      $isSuperAdmin = $user && $user->user_type === 'super_admin';
      
      if (!$isSuperAdmin && $currentBranch && $order->branch_id && $order->branch_id != $currentBranch) {
          abort(403, 'Unauthorized access to this order.');
      }

      if ($request->ajax()) {
          $formatted = $order->items->map(function($item) {
              return [
                  'id' => $item->id,
                  'product_name' => $item->product_name,
                  'product_info' => $item->product?->category?->name ?? 'Misc',
                  'price' => $item->price,
                  'qty' => $item->qty,
                  'image' => $item->product?->image ?? ''
              ];
          });
          return response()->json(['data' => $formatted]);
      }

      return view('content.apps.app-ecommerce-order-details', compact('order'));
  }
}
