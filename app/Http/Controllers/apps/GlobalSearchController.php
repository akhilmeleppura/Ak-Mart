<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\SubscriptionInvoice;
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

        // 1. Search Settings Hub Sections
        $settingsSections = [
            'store'           => ['title' => 'Store Details & Business Profile', 'icon' => 'bx bx-store', 'url' => url('settings/store')],
            'payments'        => ['title' => 'Payment Gateways & Processing', 'icon' => 'bx bx-credit-card', 'url' => url('settings/payments')],
            'shipping'        => ['title' => 'Shipping Rates & Zones', 'icon' => 'bx bx-package', 'url' => url('settings/shipping')],
            'email'           => ['title' => 'Email & SMTP Server Configuration', 'icon' => 'bx bx-envelope', 'url' => url('settings/email')],
            'whatsapp'        => ['title' => 'WhatsApp Cloud API Configuration', 'icon' => 'bx bxl-whatsapp', 'url' => url('settings/whatsapp')],
            'ai'              => ['title' => 'Google Gemini AI & Smart Copilot', 'icon' => 'bx bx-bot', 'url' => url('settings/ai')],
            'pricing'         => ['title' => 'Pricing, Currency & Tax (GST)', 'icon' => 'bx bx-dollar-circle', 'url' => url('settings/pricing')],
            'inventory'       => ['title' => 'Inventory Rules & Low Stock Thresholds', 'icon' => 'bx bx-cube', 'url' => url('settings/inventory')],
            'security'        => ['title' => 'Security Center & Password Policies', 'icon' => 'bx bx-shield-quarter', 'url' => url('settings/security')],
            'notifications'   => ['title' => 'Omnichannel Notification Matrix', 'icon' => 'bx bx-bell', 'url' => url('settings/notifications')],
            'automation'      => ['title' => 'Workflow Automation Rules', 'icon' => 'bx bx-bolt-circle', 'url' => url('settings/automation')],
            'api-webhooks'    => ['title' => 'API Keys & Outbound Webhooks', 'icon' => 'bx bx-code-block', 'url' => url('settings/api-webhooks')],
            'backup'          => ['title' => 'Database Backup & System Maintenance', 'icon' => 'bx bx-data', 'url' => url('settings/backup')],
        ];

        foreach ($settingsSections as $key => $meta) {
            if (stripos($meta['title'], $q) !== false || stripos($key, $q) !== false) {
                $results[] = [
                    'type'     => 'Settings Section',
                    'title'    => $meta['title'],
                    'subtitle' => 'Store Management Hub • /settings/' . $key,
                    'url'      => $meta['url'],
                    'icon'     => $meta['icon']
                ];
            }
        }

        // 2. Search Products
        $products = Product::where('name', 'LIKE', "%{$q}%")
            ->orWhere('sku', 'LIKE', "%{$q}%")
            ->orWhere('barcode', 'LIKE', "%{$q}%")
            ->take(5)->get();
        foreach ($products as $prod) {
            $results[] = [
                'type'     => 'Product',
                'title'    => $prod->name,
                'subtitle' => 'SKU: ' . ($prod->sku ?: 'N/A') . ' | $' . number_format($prod->price, 2),
                'url'      => route('app-ecommerce-product-list'),
                'icon'     => 'bx bx-package'
            ];
        }

        // 3. Search Orders
        $orders = Order::where('order_number', 'LIKE', "%{$q}%")->take(5)->get();
        foreach ($orders as $ord) {
            $results[] = [
                'type'     => 'Order',
                'title'    => 'Order #' . $ord->order_number,
                'subtitle' => '$' . number_format($ord->total_amount, 2) . ' • ' . ucfirst($ord->order_status),
                'url'      => route('app-ecommerce-order-list'),
                'icon'     => 'bx bx-cart'
            ];
        }

        // 4. Search Invoices
        $invoices = SubscriptionInvoice::where('invoice_number', 'LIKE', "%{$q}%")->take(4)->get();
        foreach ($invoices as $inv) {
            $results[] = [
                'type'     => 'Invoice',
                'title'    => 'Invoice ' . $inv->invoice_number,
                'subtitle' => '$' . number_format($inv->amount, 2) . ' • ' . ucfirst($inv->status),
                'url'      => route('saas.invoices.preview', $inv->id),
                'icon'     => 'bx bx-file'
            ];
        }

        // 5. Search Suppliers
        $suppliers = Supplier::where('name', 'LIKE', "%{$q}%")
            ->orWhere('company_name', 'LIKE', "%{$q}%")
            ->take(4)->get();
        foreach ($suppliers as $sup) {
            $results[] = [
                'type'     => 'Supplier',
                'title'    => $sup->name,
                'subtitle' => $sup->company_name ?: 'Supplier Account',
                'url'      => route('app-suppliers'),
                'icon'     => 'bx bx-truck'
            ];
        }

        // 6. Search Customers
        $customers = User::where('name', 'LIKE', "%{$q}%")
            ->orWhere('email', 'LIKE', "%{$q}%")
            ->take(4)->get();
        foreach ($customers as $cust) {
            $results[] = [
                'type'     => 'Customer / User',
                'title'    => $cust->name,
                'subtitle' => $cust->email,
                'url'      => route('app-ecommerce-customer-all'),
                'icon'     => 'bx bx-user'
            ];
        }

        return response()->json(['results' => $results]);
    }
}
