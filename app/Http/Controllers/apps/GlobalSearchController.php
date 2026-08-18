<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    public function search(Request $request)
    {
        $q = trim($request->query('query'));
        if (!$q || strlen($q) < 2) {
            return response()->json(['results' => []]);
        }

        $results = [];

        // Search Products
        $products = Product::where('name', 'LIKE', "%{$q}%")
            ->orWhere('sku', 'LIKE', "%{$q}%")
            ->orWhere('barcode', 'LIKE', "%{$q}%")
            ->take(5)->get();
        foreach ($products as $prod) {
            $results[] = [
                'type' => 'Product',
                'title' => $prod->name,
                'subtitle' => 'SKU: ' . ($prod->sku ?: 'N/A') . ' | $' . number_format($prod->price, 2),
                'url' => route('app-ecommerce-product-list'),
                'icon' => 'bx bx-package'
            ];
        }

        // Search Orders
        $orders = Order::where('order_number', 'LIKE', "%{$q}%")->take(5)->get();
        foreach ($orders as $ord) {
            $results[] = [
                'type' => 'Order',
                'title' => 'Order #' . $ord->order_number,
                'subtitle' => '$' . number_format($ord->total_amount, 2) . ' • ' . ucfirst($ord->order_status),
                'url' => route('app-ecommerce-order-list'),
                'icon' => 'bx bx-cart'
            ];
        }

        // Search Suppliers
        $suppliers = Supplier::where('name', 'LIKE', "%{$q}%")
            ->orWhere('company_name', 'LIKE', "%{$q}%")
            ->take(5)->get();
        foreach ($suppliers as $sup) {
            $results[] = [
                'type' => 'Supplier',
                'title' => $sup->name,
                'subtitle' => $sup->company_name ?: 'Supplier Account',
                'url' => route('app-suppliers'),
                'icon' => 'bx bx-truck'
            ];
        }

        // Search Customers
        $customers = User::where('name', 'LIKE', "%{$q}%")
            ->orWhere('email', 'LIKE', "%{$q}%")
            ->take(5)->get();
        foreach ($customers as $cust) {
            $results[] = [
                'type' => 'Customer / User',
                'title' => $cust->name,
                'subtitle' => $cust->email,
                'url' => route('app-ecommerce-customer-all'),
                'icon' => 'bx bx-user'
            ];
        }

        return response()->json(['results' => $results]);
    }
}
