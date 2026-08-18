<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\StockMovement;
use App\Models\LoyaltyTransaction;
use App\Models\Customers\Customer;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PosController extends Controller
{
    /**
     * Show the POS terminal.
     */
    public function index(Request $request)
    {
        $branchId = session('branch_id') ?? auth()->user()?->branch_id;
        
        $query = Product::with(['category', 'variants'])->where('is_active', true);
        if ($branchId && !auth()->user()?->isSupremeAdmin()) {
            $query->where('branch_id', $branchId);
        }

        $products = $query->get();
        $categories = Category::where('is_active', true)->get();
        $customers = User::where('user_type', 'customer')->orWhereHas('roles', function($q) {
            $q->where('name', 'User');
        })->get();

        return view('content.apps.vendor.pos', compact('products', 'categories', 'customers'));
    }

    /**
     * Search product by barcode, SKU, or Name.
     */
    public function search(Request $request)
    {
        $query = trim($request->query('q'));
        if (!$query) {
            return response()->json(['success' => false, 'message' => 'Query string empty']);
        }

        $branchId = session('branch_id') ?? auth()->user()?->branch_id;

        $pQuery = Product::with(['category', 'variants'])->where('is_active', true);
        if ($branchId && !auth()->user()?->isSupremeAdmin()) {
            $pQuery->where('branch_id', $branchId);
        }

        $product = $pQuery->where(function($q) use ($query) {
            $q->where('barcode', $query)
              ->orWhere('sku', $query)
              ->orWhere('name', 'LIKE', "%{$query}%");
        })->first();

        if ($product) {
            return response()->json([
                'success' => true,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'sku' => $product->sku,
                    'barcode' => $product->barcode,
                    'price' => (float) $product->price,
                    'qty' => (int) $product->qty,
                    'image' => $product->image,
                    'category' => $product->category?->name ?? 'General',
                    'variants' => $product->variants->map(function($v) {
                        return [
                            'id' => $v->id,
                            'name' => $v->attribute_name . ': ' . $v->attribute_value,
                            'price' => (float) ($v->price ?: 0),
                            'qty' => (int) $v->qty,
                        ];
                    })
                ]
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Product not found']);
    }

    /**
     * Process POS sale with stock movement and loyalty integration.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'items'          => 'required|array|min:1',
            'items.*.id'     => 'required|exists:products,id',
            'items.*.qty'    => 'required|integer|min:1',
            'items.*.price'  => 'required|numeric|min:0',
            'total'          => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'customer_id'    => 'nullable|exists:users,id',
            'discount_amount'=> 'nullable|numeric|min:0',
            'tax_amount'     => 'nullable|numeric|min:0',
            'points_redeemed'=> 'nullable|integer|min:0',
        ]);

        return DB::transaction(function () use ($request) {
            $branchId = session('branch_id') ?? auth()->user()?->branch_id;
            $orderNumber = 'ORD-POS-' . date('Ymd') . '-' . rand(1000, 9999);

            $order = Order::create([
                'order_number'    => $orderNumber,
                'user_id'         => $request->customer_id ?? auth()->id(),
                'total_amount'    => $request->total,
                'payment_method'  => $request->payment_method,
                'payment_status'  => 'paid',
                'order_status'    => 'completed',
                'shipping_address'=> 'POS In-Store Checkout',
                'billing_address' => 'POS In-Store Checkout',
                'branch_id'       => $branchId,
            ]);

            $receiptItems = [];

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if ($product) {
                    $qty = (int) $item['qty'];
                    $price = (float) $item['price'];
                    $variantId = $item['variant_id'] ?? null;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name . ($item['variant_name'] ?? '' ? ' (' . $item['variant_name'] . ')' : ''),
                        'qty'          => $qty,
                        'price'        => $price,
                        'unit_price'   => $price,
                        'total'        => $price * $qty,
                        'total_price'  => $price * $qty,
                    ]);

                    // Deduct stock and log movement
                    StockMovement::record(
                        $product->id,
                        -$qty,
                        'sale',
                        "POS Sale Order #{$orderNumber}",
                        $variantId,
                        $branchId,
                        'Order',
                        $order->id,
                        auth()->id()
                    );

                    $receiptItems[] = [
                        'name' => $product->name,
                        'qty' => $qty,
                        'price' => number_format($price, 2),
                        'subtotal' => number_format($price * $qty, 2),
                    ];
                }
            }

            // Handle Loyalty Points: 1 point per 100 spent
            if ($request->customer_id) {
                // Deduct redeemed points
                if ($request->points_redeemed > 0) {
                    LoyaltyTransaction::recordPoints(
                        $request->customer_id,
                        -$request->points_redeemed,
                        'redeemed',
                        $order->id,
                        "Redeemed at checkout for Order #{$orderNumber}",
                        $branchId
                    );
                }

                // Earn new points (1 point per 10 currency spent)
                $earnedPoints = (int) floor($request->total / 10);
                if ($earnedPoints > 0) {
                    LoyaltyTransaction::recordPoints(
                        $request->customer_id,
                        $earnedPoints,
                        'earned',
                        $order->id,
                        "Earned from POS Sale Order #{$orderNumber}",
                        $branchId
                    );
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'POS sale completed successfully & stock updated!',
                'receipt' => [
                    'order_number'    => $order->order_number,
                    'date'            => $order->created_at->format('d M Y, h:i A'),
                    'cashier'         => auth()->user()?->name ?? 'Staff',
                    'customer'        => $request->customer_id ? User::find($request->customer_id)?->name : 'Walk-in Customer',
                    'items'           => $receiptItems,
                    'subtotal'        => number_format($request->subtotal ?? $request->total, 2),
                    'discount'        => number_format($request->discount_amount ?? 0, 2),
                    'tax'             => number_format($request->tax_amount ?? 0, 2),
                    'total'           => number_format($order->total_amount, 2),
                    'payment_method'  => ucfirst($order->payment_method),
                ]
            ]);
        });
    }
}
