<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EcommerceCustomerDetailsNotifications extends Controller
{
  public function index(Request $request, $id = null)
  {
      $customer = $id ? \App\Models\User::find($id) : \App\Models\User::first();
      
      if (!$customer) {
          return redirect()->route('app-ecommerce-customer-all');
      }

      $ordersCount = \App\Models\Order::where('user_id', $customer->id)->count();
      $totalSpent = \App\Models\Order::where('user_id', $customer->id)->sum('total_amount');

      return view('content.apps.app-ecommerce-customer-details-notifications', compact('customer', 'ordersCount', 'totalSpent'));
  }
}
