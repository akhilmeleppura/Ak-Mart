<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class InvoicePrint extends Controller
{
  public function index($id = null)
  {
    $order = $id ? \App\Models\Order::with(['items.product', 'customer'])->find($id) : \App\Models\Order::with(['items.product', 'customer'])->latest()->first();

    if (!$order) {
        return redirect()->route('app-ecommerce-order-list');
    }

    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.apps.app-invoice-print', ['pageConfigs' => $pageConfigs, 'order' => $order]);
  }
}
