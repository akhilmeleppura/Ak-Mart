<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Coupon;
use App\Models\StockMovement;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StorefrontController extends Controller
{
    /**
     * List all catalog products with search, filtering, and pagination.
     */
    public function products(Request $request)
    {
        $query = Product::with(['category', 'variants'])->where('is_active', true);

        // Search
        if ($request->has('q') && $request->q) {
            $q = $request->q;
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'LIKE', "%{$q}%")
                    ->orWhere('sku', 'LIKE', "%{$q}%")
                    ->orWhere('brand', 'LIKE', "%{$q}%")
                    ->orWhere('barcode', $q);
            });
        }

        // Category filter
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Brand filter
        if ($request->has('brand') && $request->brand) {
            $query->where('brand', $request->brand);
        }

        // Price range
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('price', '>=', $request->min_price);
        }
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        if ($sort === 'price_asc') $query->orderBy('price', 'asc');
        elseif ($sort === 'price_desc') $query->orderBy('price', 'desc');
        elseif ($sort === 'popular') $query->orderBy('qty', 'asc');
        else $query->latest();

        $products = $query->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => $products->items(),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'total'        => $products->total(),
                'per_page'     => $products->perPage(),
            ]
        ]);
    }

    /**
     * Get single product details with variants and inventory status.
     */
    public function productDetails($id)
    {
        $product = Product::with(['category', 'variants', 'reviews'])->find($id);

        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Product not found.'], 404);
        }

        return response()->json([
            'success' => true,
            'product' => [
                'id'               => $product->id,
                'name'             => $product->name,
                'slug'             => $product->slug,
                'brand'            => $product->brand,
                'sku'              => $product->sku,
                'barcode'          => $product->barcode,
                'price'            => (float) $product->price,
                'compare_at_price' => (float) $product->compare_at_price,
                'qty'              => (int) $product->qty,
                'is_in_stock'      => $product->qty > 0,
                'description'      => $product->description,
                'image'            => $product->image,
                'category'         => $product->category ? [
                    'id'   => $product->category->id,
                    'name' => $product->category->name,
                    'slug' => $product->category->slug,
                ] : null,
                'variants'         => $product->variants->map(function ($v) {
                    return [
                        'id'              => $v->id,
                        'attribute_name'  => $v->attribute_name,
                        'attribute_value' => $v->attribute_value,
                        'price'           => (float) $v->price,
                        'sale_price'      => (float) $v->sale_price,
                        'qty'             => (int) $v->qty,
                        'sku'             => $v->sku,
                    ];
                }),
                'attributes'       => $product->attributes,
                'meta_title'       => $product->meta_title,
                'meta_description' => $product->meta_description,
            ]
        ]);
    }

    /**
     * List all categories.
     */
    public function categories()
    {
        $categories = Category::where('is_active', true)->withCount('products')->get();

        return response()->json([
            'success'    => true,
            'categories' => $categories
        ]);
    }

    /**
     * Validate a coupon code against minimum spend.
     */
    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code'     => 'required|string',
            'amount'   => 'nullable|numeric|min:0',
            'subtotal' => 'nullable|numeric|min:0',
        ]);

        $amount = (float) ($request->amount ?? $request->subtotal ?? 0);
        $coupon = Coupon::where('code', strtoupper($request->code))->first();

        if (!$coupon) {
            return response()->json(['valid' => false, 'success' => false, 'message' => 'Invalid coupon code.'], 404);
        }

        if (!$coupon->is_active) {
            return response()->json(['valid' => false, 'success' => false, 'message' => 'This coupon is inactive.'], 422);
        }

        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            return response()->json(['valid' => false, 'success' => false, 'message' => 'Coupon is not yet active.'], 422);
        }

        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            return response()->json(['valid' => false, 'success' => false, 'message' => 'Coupon has expired.'], 422);
        }

        if ($coupon->min_spend && $amount < $coupon->min_spend) {
            return response()->json([
                'valid'   => false,
                'success' => false,
                'message' => "Minimum spend of $" . number_format($coupon->min_spend, 2) . " is required for this coupon."
            ], 422);
        }

        $discount = $coupon->type === 'percentage'
            ? ($amount * ($coupon->value / 100))
            : min($coupon->value, $amount);

        if ($coupon->max_spend && $discount > $coupon->max_spend) {
            $discount = $coupon->max_spend;
        }

        $finalAmount = max(0, $amount - $discount);

        return response()->json([
            'valid'           => true,
            'success'         => true,
            'coupon_id'       => $coupon->id,
            'code'            => $coupon->code,
            'discount_type'   => $coupon->type,
            'discount_value'  => (float) $coupon->value,
            'discount_amount' => round($discount, 2),
            'new_total'       => round($finalAmount, 2),
            'final_amount'    => round($finalAmount, 2),
        ]);
    }

    /**
     * Create / Place a new Storefront order.
     */
    public function placeOrder(Request $request)
    {
        $request->validate([
            'items'            => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'      => 'required|integer|min:1',
            'total_amount'     => 'required|numeric|min:0',
            'payment_method'   => 'required|string',
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email',
            'shipping_address' => 'required|string',
        ]);

        return DB::transaction(function () use ($request) {
            // Find or create customer
            $customer = User::firstOrCreate(
                ['email' => $request->customer_email],
                [
                    'name'      => $request->customer_name,
                    'password'  => Hash::make(Str::random(16)),
                    'phone'     => $request->customer_phone,
                    'user_type' => 'customer',
                ]
            );

            $orderNumber = 'ORD-' . strtoupper(Str::random(8));

            $order = Order::create([
                'order_number'    => $orderNumber,
                'user_id'         => $customer->id,
                'total_amount'    => $request->total_amount,
                'payment_status'  => $request->payment_method === 'cod' ? 'pending' : 'paid',
                'order_status'    => 'processing',
                'payment_method'  => $request->payment_method,
                'shipping_address'=> $request->shipping_address,
                'billing_address' => $request->billing_address ?? $request->shipping_address,
                'branch_id'       => session('branch_id'),
            ]);

            foreach ($request->items as $item) {
                $product = Product::lockForUpdate()->find($item['product_id']);
                if ($product) {
                    $qty = (int) $item['qty'];
                    $price = (float) $product->price;

                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'qty'          => $qty,
                        'price'        => $price,
                        'unit_price'   => $price,
                        'total'        => $price * $qty,
                        'total_price'  => $price * $qty,
                    ]);

                    // Deduct stock & record movement
                    StockMovement::record(
                        $product->id,
                        -$qty,
                        'sale',
                        "Online Store Order #{$orderNumber}",
                        null,
                        $order->branch_id,
                        'Order',
                        $order->id,
                        $customer->id
                    );
                }
            }

            // Loyalty points: 1 pt per $10
            $pointsEarned = (int) floor($order->total_amount / 10);
            if ($pointsEarned > 0) {
                LoyaltyTransaction::recordPoints(
                    $customer->id,
                    $pointsEarned,
                    'earned',
                    $order->id,
                    "Storefront Order #{$orderNumber}",
                    $order->branch_id
                );
            }

            return response()->json([
                'success'      => true,
                'message'      => 'Order placed successfully!',
                'order_number' => $order->order_number,
                'order_id'     => $order->id,
                'total_amount' => number_format($order->total_amount, 2),
                'status'       => $order->order_status,
            ], 201);
        });
    }

    /**
     * Get order details by order number.
     */
    public function getOrder($orderNumber)
    {
        $order = Order::with(['items.product', 'customer'])
            ->where('order_number', $orderNumber)
            ->orWhere('id', $orderNumber)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order not found.'], 404);
        }

        // IDOR Protection
        if (auth()->check() && !auth()->user()->is_supreme_admin && !auth()->user()->is_super_admin) {
            if ($order->user_id && $order->user_id !== auth()->id()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized access to this order.'], 403);
            }
        }

        return response()->json([
            'success' => true,
            'order'   => $order
        ]);
    }
}
