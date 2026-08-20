<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\CmsBanner;
use App\Models\StockMovement;
use App\Models\LoyaltyTransaction;
use App\Models\StoreCredit;
use App\Models\Coupon;
use App\Models\Review;
use App\Models\StockNotification;
use App\Models\ProductQuestion;
use App\Models\OrderReturn;
use App\Models\DeliverySlot;
use App\Models\PriceAlert;
use App\Models\User;
use App\Models\StoreSetting;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StorefrontController extends Controller
{
    /**
     * Storefront Homepage with Dynamic CMS Sliders & Merchandising Blocks
     */
    public function index(Request $request)
    {
        if ($ref = $request->query('ref')) {
            session()->put('referred_by_code', strtoupper(trim($ref)));
        }

        $heroSliders = CmsBanner::where('is_active', true)
            ->where('position', 'home_hero')
            ->orderBy('sort_order')
            ->get();

        $featuredCategories = Category::where('is_active', true)
            ->withCount('products')
            ->orderBy('sort_order')
            ->take(8)
            ->get();

        $featuredProducts = Product::where('is_active', true)
            ->where('is_featured', true)
            ->take(8)
            ->get();

        // If no featured flag set, fallback to latest products
        if ($featuredProducts->isEmpty()) {
            $featuredProducts = Product::where('is_active', true)->latest()->take(8)->get();
        }

        $trendingProducts = Product::where('is_active', true)
            ->where(fn($q) => $q->where('is_trending', true)->orWhere('qty', '>', 10))
            ->take(4)
            ->get();

        $bestSellers = Product::where('is_active', true)
            ->where(fn($q) => $q->where('is_best_seller', true)->orWhere('qty', '>', 5))
            ->orderByDesc('price')
            ->take(4)
            ->get();

        $dealsOfTheDay = Product::where('is_active', true)
            ->where(fn($q) => $q->where('deal_of_the_day', true)->orWhereNotNull('compare_at_price'))
            ->take(4)
            ->get();

        $recentIds = session()->get('recently_viewed', []);
        $recentlyViewed = Product::whereIn('id', array_slice($recentIds, 0, 6))
            ->where('is_active', true)
            ->get();

        return view('storefront.home', compact(
            'heroSliders',
            'featuredCategories',
            'featuredProducts',
            'trendingProducts',
            'bestSellers',
            'dealsOfTheDay',
            'recentlyViewed'
        ));
    }

    /**
     * Live Search Autocomplete Suggestions AJAX
     */
    public function searchSuggestions(Request $request)
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $products = Product::where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'LIKE', "%{$q}%")
                      ->orWhere('sku', 'LIKE', "%{$q}%")
                      ->orWhere('barcode', 'LIKE', "%{$q}%");
            })
            ->take(6)
            ->get(['id', 'name', 'price', 'image', 'qty', 'sku']);

        return response()->json(['suggestions' => $products]);
    }

    /**
     * Shop / Product Catalog with Faceted Filtering
     */
    public function shop(Request $request)
    {
        $filterConfig = json_decode(StoreSetting::get('storefront_filter_config', '{}'), true) ?: [
            'show_search'      => true,
            'show_category'    => true,
            'show_brand'       => true,
            'show_price'       => true,
            'show_rating'      => true,
            'show_stock'       => true,
            'show_dietary'     => true,
            'show_deals'       => true,
            'price_min_limit'  => 0,
            'price_max_limit'  => 100,
            'brand_display'    => 'scroll_list',
            'dietary_tags'     => 'Organic, Gluten-Free, Vegan, Dairy-Free, Sugar-Free, Non-GMO, Halal',
            'quick_filter_bar' => true,
            'grid_list_toggle' => true,
        ];

        $query = Product::where('is_active', true)->with(['category', 'variants']);

        // Search Keyword
        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('sku', 'LIKE', "%{$search}%")
                  ->orWhere('barcode', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Category Filter
        if ($categoryId = $request->input('category')) {
            $query->where('category_id', $categoryId);
        }

        // Merchandising Collections
        if ($collection = $request->input('collection')) {
            match ($collection) {
                'featured'   => $query->where('is_featured', true),
                'trending'   => $query->where('is_trending', true),
                'bestseller' => $query->where('is_best_seller', true),
                'deals'      => $query->where('deal_of_the_day', true),
                default      => null,
            };
        }

        // Brand Filter (supports array or single string)
        if ($brands = $request->input('brands')) {
            $brandList = is_array($brands) ? $brands : explode(',', $brands);
            $query->whereIn('brand', $brandList);
        } elseif ($brand = $request->input('brand')) {
            $query->where('brand', $brand);
        }

        // Rating Filter (e.g. 4 => 4 stars and above)
        if ($minRating = $request->input('min_rating')) {
            $query->where('rating_cache', '>=', (float)$minRating);
        }

        // Dietary / Lifestyle Tag Filter
        if ($dietary = $request->input('dietary')) {
            $query->where(function ($q) use ($dietary) {
                $q->where('name', 'LIKE', "%{$dietary}%")
                  ->orWhere('description', 'LIKE', "%{$dietary}%")
                  ->orWhere('attributes', 'LIKE', "%{$dietary}%");
            });
        }

        // Deals / Flash Sale Filter
        if ($request->boolean('deals_only') || $request->input('deals') === '1') {
            $query->where(function ($q) {
                $q->where('deal_of_the_day', true)
                  ->orWhereNotNull('compare_at_price');
            });
        }

        // Price Range
        if ($minPrice = $request->input('min_price')) {
            $query->where('price', '>=', (float)$minPrice);
        }
        if ($maxPrice = $request->input('max_price')) {
            $query->where('price', '<=', (float)$maxPrice);
        }

        // In Stock Only
        if ($request->boolean('in_stock')) {
            $query->where('qty', '>', 0);
        }

        // Sorting
        $sort = $request->input('sort', 'latest');
        match ($sort) {
            'price_low'   => $query->orderBy('price', 'asc'),
            'price_high'  => $query->orderBy('price', 'desc'),
            'rating_high' => $query->orderByDesc('rating_cache'),
            'name_asc'    => $query->orderBy('name', 'asc'),
            'popular'     => $query->orderByDesc('qty'),
            default       => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->withCount('products')->get();
        $availableBrands = Product::whereNotNull('brand')->where('brand', '!=', '')->distinct()->orderBy('brand')->pluck('brand');
        $brandCounts = Product::whereNotNull('brand')->where('brand', '!=', '')->selectRaw('brand, count(*) as count')->groupBy('brand')->pluck('count', 'brand');

        $inStockCount = Product::where('is_active', true)->where('qty', '>', 0)->count();
        $dealsCount = Product::where('is_active', true)->where(function ($q) {
            $q->where('deal_of_the_day', true)->orWhereNotNull('compare_at_price');
        })->count();

        return view('storefront.shop', compact('products', 'categories', 'availableBrands', 'brandCounts', 'filterConfig', 'inStockCount', 'dealsCount'));
    }

    /**
     * Product Detail Page
     */
    public function product($id)
    {
        $product = Product::with([
            'category',
            'variants',
            'reviews.user',
            'attributeValues.attribute',
            'attributeValues.value',
            'relatedProducts',
            'suggestedProducts',
            'crossSells'
        ])
        ->where('is_active', true)
        ->findOrFail($id);

        // Explicit admin-linked related products or fallback to same category
        $relatedProducts = $product->relatedProducts->isNotEmpty()
            ? $product->relatedProducts
            : Product::where('category_id', $product->category_id)
                ->where('id', '!=', $product->id)
                ->where('is_active', true)
                ->take(4)
                ->get();

        // Frequently Bought Together / Suggested items configured by Admin
        $frequentlyBought = $product->suggestedProducts->isNotEmpty()
            ? $product->suggestedProducts
            : Product::where('id', '!=', $product->id)
                ->where('is_active', true)
                ->take(2)
                ->get();

        $availableStock = app(InventoryService::class)->getAvailableStock($product->id);

        // 5-Star Rating Breakdown Histogram
        $reviews = $product->reviews;
        $totalReviews = $reviews->count();
        $ratingBreakdown = [
            5 => $totalReviews ? round(($reviews->where('rating', 5)->count() / $totalReviews) * 100) : 0,
            4 => $totalReviews ? round(($reviews->where('rating', 4)->count() / $totalReviews) * 100) : 0,
            3 => $totalReviews ? round(($reviews->where('rating', 3)->count() / $totalReviews) * 100) : 0,
            2 => $totalReviews ? round(($reviews->where('rating', 2)->count() / $totalReviews) * 100) : 0,
            1 => $totalReviews ? round(($reviews->where('rating', 1)->count() / $totalReviews) * 100) : 0,
        ];
        $averageRating = $totalReviews ? round($reviews->avg('rating'), 1) : 5.0;

        // Customer Questions & Answers
        $questions = ProductQuestion::where('product_id', $product->id)
            ->where('is_published', true)
            ->with('user', 'answeredBy')
            ->latest()
            ->get();

        // Recently Viewed Tracking
        $recentIds = session()->get('recently_viewed', []);
        $filteredRecentIds = array_values(array_diff($recentIds, [$product->id]));
        $recentlyViewed = Product::whereIn('id', array_slice($filteredRecentIds, 0, 6))
            ->where('is_active', true)
            ->get();

        // Push current product to front of session list
        array_unshift($recentIds, $product->id);
        session()->put('recently_viewed', array_values(array_unique(array_slice($recentIds, 0, 10))));

        return view('storefront.product-detail', compact(
            'product',
            'relatedProducts',
            'frequentlyBought',
            'availableStock',
            'ratingBreakdown',
            'averageRating',
            'totalReviews',
            'questions',
            'recentlyViewed'
        ));
    }


    /**
     * Cart Page
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $savedForLater = session()->get('saved_for_later', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        // Coupon Handling
        $coupon = session()->get('coupon');
        $couponDiscount = 0;
        if ($coupon) {
            if ($coupon['type'] === 'percentage') {
                $couponDiscount = round(($subtotal * $coupon['value']) / 100, 2);
            } else {
                $couponDiscount = min($subtotal, (float)$coupon['value']);
            }
        }
        $finalTotal = max(0, $subtotal - $couponDiscount);

        return view('storefront.cart', compact('cart', 'savedForLater', 'subtotal', 'coupon', 'couponDiscount', 'finalTotal'));
    }

    /**
     * Save Item for Later (Move from Cart to Saved)
     */
    public function saveForLater(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session()->get('cart', []);
        $saved = session()->get('saved_for_later', []);

        if (isset($cart[$productId])) {
            $saved[$productId] = $cart[$productId];
            unset($cart[$productId]);
            session()->put('cart', $cart);
            session()->put('saved_for_later', $saved);
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Item moved to Save for Later!',
            'cartCount'  => count($cart),
            'savedCount' => count($saved),
        ]);
    }

    /**
     * Move Item from Saved list back to Cart
     */
    public function moveToCartFromSaved(Request $request)
    {
        $productId = $request->input('product_id');
        $cart = session()->get('cart', []);
        $saved = session()->get('saved_for_later', []);

        if (isset($saved[$productId])) {
            $cart[$productId] = $saved[$productId];
            unset($saved[$productId]);
            session()->put('cart', $cart);
            session()->put('saved_for_later', $saved);
        }

        return response()->json([
            'success'    => true,
            'message'    => 'Item moved back to your cart!',
            'cartCount'  => count($cart),
            'savedCount' => count($saved),
        ]);
    }

    /**
     * Remove Item from Saved list
     */
    public function removeSaved(Request $request)
    {
        $productId = $request->input('product_id');
        $saved = session()->get('saved_for_later', []);
        unset($saved[$productId]);
        session()->put('saved_for_later', $saved);

        return response()->json([
            'success'    => true,
            'message'    => 'Item removed from saved list.',
            'savedCount' => count($saved),
        ]);
    }

    /**
     * Apply Coupon Code AJAX
     */
    public function applyCoupon(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));
        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Please enter a coupon code.'], 422);
        }

        $coupon = Coupon::where('code', $code)->where('is_active', true)->first();
        if (!$coupon) {
            return response()->json(['success' => false, 'message' => 'Invalid or inactive coupon code.'], 404);
        }

        if ($coupon->start_date && now()->lt($coupon->start_date)) {
            return response()->json(['success' => false, 'message' => 'Coupon campaign has not started yet.'], 422);
        }

        if ($coupon->end_date && now()->gt($coupon->end_date)) {
            return response()->json(['success' => false, 'message' => 'Coupon has expired.'], 422);
        }

        if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
            return response()->json(['success' => false, 'message' => 'Coupon usage limit reached.'], 422);
        }

        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        if ($subtotal <= 0) {
            return response()->json(['success' => false, 'message' => 'Your cart is empty.'], 422);
        }

        if ($coupon->min_spend && $subtotal < $coupon->min_spend) {
            return response()->json(['success' => false, 'message' => "Minimum order amount of \${$coupon->min_spend} required for coupon {$coupon->code}."], 422);
        }

        $discount = 0;
        if ($coupon->type === 'percentage') {
            $discount = round(($subtotal * $coupon->value) / 100, 2);
        } else {
            $discount = min($subtotal, (float)$coupon->value);
        }

        session()->put('coupon', [
            'code'     => $coupon->code,
            'type'     => $coupon->type,
            'value'    => (float)$coupon->value,
            'discount' => $discount,
        ]);

        $newTotal = max(0, $subtotal - $discount);

        return response()->json([
            'success'        => true,
            'message'        => "Coupon {$coupon->code} applied successfully! You saved $" . number_format($discount, 2),
            'discount'       => number_format($discount, 2),
            'discount_raw'   => $discount,
            'subtotal'       => number_format($subtotal, 2),
            'final_total'    => number_format($newTotal, 2),
            'code'           => $coupon->code,
        ]);
    }

    /**
     * Remove Applied Coupon
     */
    public function removeCoupon()
    {
        session()->forget('coupon');
        return redirect()->back()->with('success', 'Coupon removed successfully.');
    }

    /**
     * Add to Cart AJAX
     */
    public function addToCart(Request $request)
    {
        $productId = $request->input('product_id');
        $qty = max(1, (int)$request->input('qty', 1));
        $product = Product::findOrFail($productId);

        $cart = session()->get('cart', []);
        if (isset($cart[$productId])) {
            $cart[$productId]['qty'] += $qty;
        } else {
            $cart[$productId] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'price'    => (float)$product->price,
                'image'    => $product->image,
                'qty'      => $qty,
                'sku'      => $product->sku,
            ];
        }

        session()->put('cart', $cart);

        $totalItems = array_sum(array_column($cart, 'qty'));
        return response()->json([
            'success'    => true,
            'message'    => "{$product->name} added to cart!",
            'totalItems' => $totalItems,
        ]);
    }

    /**
     * Update Cart Quantity AJAX
     */
    public function updateCart(Request $request)
    {
        $productId = $request->input('product_id');
        $qty = (int)$request->input('qty');

        $cart = session()->get('cart', []);
        if ($qty > 0 && isset($cart[$productId])) {
            $cart[$productId]['qty'] = $qty;
        } elseif ($qty <= 0) {
            unset($cart[$productId]);
        }

        session()->put('cart', $cart);

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        // Recalculate coupon discount if present
        $coupon = session()->get('coupon');
        $couponDiscount = 0;
        if ($coupon) {
            if ($coupon['type'] === 'percentage') {
                $couponDiscount = round(($subtotal * $coupon['value']) / 100, 2);
            } else {
                $couponDiscount = min($subtotal, (float)$coupon['value']);
            }
            session()->put('coupon.discount', $couponDiscount);
        }
        $finalTotal = max(0, $subtotal - $couponDiscount);

        return response()->json([
            'success'        => true,
            'subtotal'       => number_format($subtotal, 2),
            'couponDiscount' => number_format($couponDiscount, 2),
            'finalTotal'     => number_format($finalTotal, 2),
            'cart'           => $cart,
        ]);
    }

    /**
     * Checkout View
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('storefront.shop')->with('error', 'Your shopping cart is empty.');
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $user = Auth::user();
        $walletBalance = $user ? (StoreCredit::where('user_id', $user->id)->value('balance') ?: 0) : 0;
        $loyaltyPoints = $user ? LoyaltyTransaction::getCustomerBalance($user->id) : 0;

        // Coupon Handling
        $coupon = session()->get('coupon');
        $couponDiscount = 0;
        if ($coupon) {
            if ($coupon['type'] === 'percentage') {
                $couponDiscount = round(($subtotal * $coupon['value']) / 100, 2);
            } else {
                $couponDiscount = min($subtotal, (float)$coupon['value']);
            }
        }
        $finalTotal = max(0, $subtotal - $couponDiscount);
        $deliverySlots = DeliverySlot::where('is_active', true)->get();

        return view('storefront.checkout', compact(
            'cart',
            'subtotal',
            'walletBalance',
            'loyaltyPoints',
            'coupon',
            'couponDiscount',
            'finalTotal',
            'deliverySlots'
        ));
    }

    /**
     * Process Checkout & Order Creation
     */
    public function processCheckout(Request $request)
    {
        $request->validate([
            'customer_name'    => 'required|string|max:255',
            'customer_email'   => 'required|email|max:255',
            'customer_phone'   => 'required|string|max:20',
            'shipping_address' => 'required|string|max:500',
            'payment_method'   => 'required|string',
            'delivery_slot_id' => 'nullable|exists:delivery_slots,id',
            'use_store_credit' => 'nullable',
        ]);

        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('storefront.shop')->with('error', 'Your cart is empty.');
        }

        return DB::transaction(function () use ($request, $cart) {
            $subtotal = 0;
            foreach ($cart as $item) {
                $subtotal += $item['price'] * $item['qty'];
            }

            // Apply Coupon if valid
            $coupon = session()->get('coupon');
            $couponDiscount = 0;
            if ($coupon) {
                if ($coupon['type'] === 'percentage') {
                    $couponDiscount = round(($subtotal * $coupon['value']) / 100, 2);
                } else {
                    $couponDiscount = min($subtotal, (float)$coupon['value']);
                }

                // Increment coupon usage
                $couponModel = Coupon::where('code', $coupon['code'])->first();
                if ($couponModel) {
                    $couponModel->increment('usage_count');
                }
            }

            $orderTotal = max(0, $subtotal - $couponDiscount);
            $orderNumber = 'ORD-' . strtoupper(Str::random(10));
            $user = Auth::user();

            // Store Credit Deduction
            $storeCreditUsed = 0;
            if ($request->boolean('use_store_credit') && $user) {
                $storeCredit = StoreCredit::where('user_id', $user->id)->first();
                if ($storeCredit && $storeCredit->balance > 0) {
                    $storeCreditUsed = min($storeCredit->balance, $orderTotal);
                    $storeCredit->debit(
                        $storeCreditUsed,
                        'order',
                        null,
                        "Applied Store Credit toward Order #{$orderNumber}"
                    );
                    $orderTotal = max(0, $orderTotal - $storeCreditUsed);
                }
            }

            $order = Order::create([
                'order_number'        => $orderNumber,
                'user_id'             => $user?->id,
                'total_amount'        => $orderTotal,
                'store_credit_amount' => $storeCreditUsed,
                'delivery_slot_id'    => $request->delivery_slot_id,
                'payment_method'      => $orderTotal == 0 ? 'store_credit' : $request->payment_method,
                'payment_status'      => ($orderTotal == 0 || $request->payment_method !== 'cod') ? 'paid' : 'pending',
                'order_status'        => 'pending',
                'shipping_address'    => $request->shipping_address,
                'billing_address'     => $request->shipping_address,
                'branch_id'           => session('branch_id', 1),
            ]);

            foreach ($cart as $item) {
                $product = Product::lockForUpdate()->find($item['id']);
                if ($product) {
                    OrderItem::create([
                        'order_id'     => $order->id,
                        'product_id'   => $product->id,
                        'product_name' => $product->name,
                        'qty'          => $item['qty'],
                        'price'        => $item['price'],
                        'unit_price'   => $item['price'],
                        'total'        => $item['price'] * $item['qty'],
                        'total_price'  => $item['price'] * $item['qty'],
                    ]);

                    // Deduct stock and record movement
                    StockMovement::record(
                        $product->id,
                        -$item['qty'],
                        'sale',
                        "Online Storefront Order #{$orderNumber}",
                        null,
                        $order->branch_id,
                        Order::class,
                        $order->id,
                        $user?->id
                    );
                }
            }

            // Award Loyalty Points if logged in (1 point per 10 spent)
            if ($user) {
                $points = (int) floor($subtotal / 10);
                if ($points > 0) {
                    LoyaltyTransaction::recordPoints(
                        $user->id,
                        $points,
                        'earned',
                        $order->id,
                        "Earned from Order #{$orderNumber}",
                        $order->branch_id
                    );
                }
            }

            // Award Referrer Bonus ($10 Store Credit) if referred by friend
            if ($refCode = session()->get('referred_by_code') ?: $request->input('referred_by_code')) {
                $referrer = User::where('referral_code', $refCode)->first();
                if ($referrer && $referrer->id !== $user?->id) {
                    $sc = StoreCredit::firstOrCreate(['user_id' => $referrer->id], ['balance' => 0]);
                    $sc->credit(10.00, 'referral', $order->id, "Referral reward from order #{$orderNumber}");
                    if ($user && !$user->referred_by_id) {
                        $user->referred_by_id = $referrer->id;
                        $user->save();
                    }
                    session()->forget('referred_by_code');
                }
            }

            // Automated Outbound Notifications (Email & WhatsApp Cloud API)
            try {
                $commService = app(\App\Services\CommunicationService::class);
                $trackingUrl = route('storefront.track', ['order_number' => $orderNumber]);
                $commVars = [
                    'customer_name'   => $request->customer_name,
                    'order_number'    => $orderNumber,
                    'order_total'     => number_format($orderTotal, 2),
                    'tracking_url'    => $trackingUrl,
                    'delivery_method' => $order->is_pickup ? 'Store Pickup' : 'Home Delivery',
                    'store_name'      => config('app.name', 'AK-Mart'),
                ];

                if (!empty($request->customer_email)) {
                    $commService->send('email', $request->customer_email, 'order_confirmation', $commVars);
                }

                if (!empty($request->customer_phone)) {
                    $commService->send('whatsapp', $request->customer_phone, 'order_confirmation', $commVars);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::warning("Outbound order communication error: " . $e->getMessage());
            }

            // Clear Cart
            session()->forget('cart');

            return redirect()->route('storefront.order.confirmation', ['orderNumber' => $orderNumber]);
        });
    }

    /**
     * Order Confirmation Page
     */
    public function orderConfirmation($orderNumber)
    {
        $order = Order::with('items.product')->where('order_number', $orderNumber)->firstOrFail();
        return view('storefront.order-confirmation', compact('order'));
    }

    /**
     * Public Order Tracking
     */
    public function trackOrder(Request $request)
    {
        $order = null;
        if ($orderNumber = $request->input('order_number')) {
            $order = Order::with('items.product')->where('order_number', trim($orderNumber))->first();
        }

        return view('storefront.order-tracking', compact('order'));
    }

    /**
     * Submit Product Review AJAX
     */
    public function submitReview(Request $request, $id)
    {
        $request->validate([
            'rating'  => 'required|integer|min:1|max:5',
            'title'   => 'nullable|string|max:150',
            'comment' => 'required|string|max:1000',
        ]);

        $product = Product::findOrFail($id);
        $user = Auth::user();

        $review = \App\Models\Review::create([
            'product_id'           => $product->id,
            'user_id'              => $user?->id ?? 1,
            'rating'               => (int)$request->rating,
            'title'                => $request->title ?: 'Customer Review',
            'comment'              => $request->comment,
            'status'               => 'approved',
            'is_verified_purchase' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you! Your verified review has been published.',
            'review'  => $review,
        ]);
    }

    /**
     * Toggle Wishlist Item AJAX
     */
    public function toggleWishlist(Request $request)
    {
        $productId = (int)$request->input('product_id');
        $wishlist = session()->get('wishlist', []);

        if (in_array($productId, $wishlist)) {
            $wishlist = array_values(array_diff($wishlist, [$productId]));
            $added = false;
        } else {
            $wishlist[] = $productId;
            $added = true;
        }

        session()->put('wishlist', $wishlist);

        return response()->json([
            'success'   => true,
            'added'     => $added,
            'count'     => count($wishlist),
            'message'   => $added ? 'Added to wishlist!' : 'Removed from wishlist.',
        ]);
    }

    /**
     * Storefront Wishlist Page
     */
    public function wishlist()
    {
        $wishlistIds = session()->get('wishlist', []);
        $products = Product::whereIn('id', $wishlistIds)->with('category')->get();
        return view('storefront.wishlist', compact('products'));
    }

    /**
     * Buy Again - 1-Click Grocery Reorder Hub
     */
    public function buyAgain(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            $orderIds = Order::where('user_id', $user->id)->pluck('id');
            $itemStats = OrderItem::whereIn('order_id', $orderIds)
                ->select('product_id', DB::raw('SUM(qty) as total_qty'), DB::raw('MAX(created_at) as last_ordered'))
                ->groupBy('product_id')
                ->get()
                ->keyBy('product_id');

            $products = Product::whereIn('id', $itemStats->keys())
                ->where('is_active', true)
                ->get()
                ->map(function ($p) use ($itemStats) {
                    $p->purchase_qty = $itemStats[$p->id]->total_qty ?? 1;
                    $p->last_ordered = $itemStats[$p->id]->last_ordered ?? null;
                    return $p;
                });

            // Supplement with top recurring essentials if few past orders
            if ($products->count() < 6) {
                $fallback = Product::where('is_active', true)
                    ->whereNotIn('id', $products->pluck('id'))
                    ->where(function ($q) {
                        $q->where('is_best_seller', true)->orWhere('is_trending', true);
                    })
                    ->take(8 - $products->count())
                    ->get();
                $products = $products->merge($fallback);
            }
            $isGuest = false;
        } else {
            $products = Product::where('is_active', true)
                ->where(function ($q) {
                    $q->where('is_best_seller', true)->orWhere('is_trending', true)->orWhere('deal_of_the_day', true);
                })
                ->take(12)
                ->get();
            $isGuest = true;
        }

        return view('storefront.buy-again', compact('products', 'isGuest'));
    }

    /**
     * Back in Stock Alert Subscription
     */
    public function subscribeStockNotification(Request $request, $productId)
    {
        $request->validate([
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        if (!$request->email && !$request->phone) {
            return response()->json(['success' => false, 'message' => 'Please provide an email address or mobile number.'], 422);
        }

        StockNotification::create([
            'product_id' => $productId,
            'user_id'    => Auth::id(),
            'email'      => $request->email,
            'phone'      => $request->phone,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'You are on the priority alert list! We will notify you immediately once this item is back in stock.',
        ]);
    }

    /**
     * Toggle Product in Compare List AJAX (Max 4 items)
     */
    public function toggleCompare(Request $request)
    {
        $productId = (int)$request->input('product_id');
        $compareList = session()->get('compare_list', []);

        if (in_array($productId, $compareList)) {
            $compareList = array_values(array_diff($compareList, [$productId]));
            $added = false;
        } else {
            if (count($compareList) >= 4) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can compare up to 4 items at a time.',
                ], 422);
            }
            $compareList[] = $productId;
            $added = true;
        }

        session()->put('compare_list', $compareList);

        return response()->json([
            'success' => true,
            'added'   => $added,
            'count'   => count($compareList),
            'message' => $added ? 'Added to comparison matrix!' : 'Removed from comparison.',
        ]);
    }

    /**
     * Clear Comparison List
     */
    public function clearCompare()
    {
        session()->forget('compare_list');
        return redirect()->route('storefront.compare')->with('success', 'Comparison list cleared.');
    }

    /**
     * Side-by-Side Product Comparison View
     */
    public function compare()
    {
        $compareIds = session()->get('compare_list', []);
        $products = Product::whereIn('id', $compareIds)
            ->where('is_active', true)
            ->with(['category', 'attributeValues.attribute', 'attributeValues.value'])
            ->get();

        return view('storefront.compare', compact('products'));
    }

    /**
     * Ask a Question AJAX
     */
    public function askQuestion(Request $request, $productId)
    {
        $request->validate([
            'question' => 'required|string|min:5|max:1000',
        ]);

        $product = Product::findOrFail($productId);
        $user = Auth::user();

        $question = ProductQuestion::create([
            'product_id'   => $product->id,
            'user_id'      => $user?->id,
            'question'     => $request->question,
            'is_published' => true,
        ]);

        return response()->json([
            'success'  => true,
            'message'  => 'Your question has been posted! Our staff and community will answer shortly.',
            'question' => $question,
        ]);
    }

    /**
     * Customer Self-Service Return Portal View
     */
    public function returns()
    {
        $user = Auth::user();
        $orders = $user
            ? Order::where('user_id', $user->id)->with('items.product')->latest()->take(10)->get()
            : collect();

        $myReturns = $user
            ? OrderReturn::where('user_id', $user->id)->with(['order', 'product'])->latest()->get()
            : collect();

        return view('storefront.returns', compact('orders', 'myReturns'));
    }

    /**
     * Submit Return / Exchange Request
     */
    public function submitReturn(Request $request)
    {
        $request->validate([
            'order_number' => 'required|string|max:50',
            'product_id'   => 'nullable|exists:products,id',
            'reason'       => 'required|string|max:255',
            'comments'     => 'nullable|string|max:1000',
            'photo'        => 'nullable|image|max:2048',
        ]);

        $order = Order::where('order_number', $request->order_number)->first();
        if (!$order) {
            return redirect()->back()->with('error', 'Invalid Order Number. Please check and try again.');
        }

        $imagePath = null;
        if ($request->hasFile('photo')) {
            $imagePath = $request->file('photo')->store('returns', 'public');
        }

        $returnNumber = 'RET-' . strtoupper(Str::random(8));

        OrderReturn::create([
            'return_number' => $returnNumber,
            'order_id'      => $order->id,
            'user_id'       => Auth::id() ?? $order->user_id,
            'product_id'    => $request->product_id,
            'reason'        => $request->reason,
            'comments'      => $request->comments,
            'image_path'    => $imagePath,
            'status'        => 'pending',
            'refund_amount' => $order->total_amount,
        ]);

        return redirect()->route('storefront.returns')->with('success', "Return Request #{$returnNumber} has been logged. Our dispatch team will inspect and process your refund/replacement within 24 hours.");
    }

    /**
     * Set Price Drop Alert AJAX
     */
    public function setPriceAlert(Request $request, $productId)
    {
        $request->validate([
            'email'        => 'required|email|max:255',
            'target_price' => 'required|numeric|min:0.01',
        ]);

        $product = Product::findOrFail($productId);

        PriceAlert::create([
            'product_id'   => $product->id,
            'user_id'      => Auth::id(),
            'email'        => $request->email,
            'target_price' => $request->target_price,
            'is_triggered' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Price drop alert activated! We will email you at {$request->email} the instant {$product->name} drops to \${$request->target_price} or lower.",
        ]);
    }

    /**
     * Customer Referral & Viral Growth Hub
     */
    public function referralProgram()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('info', 'Please sign in or create an account to get your personal referral link.');
        }

        if (!$user->referral_code) {
            $user->referral_code = 'AK-' . strtoupper(Str::random(6));
            $user->save();
        }

        $referralLink = url('/store?ref=' . $user->referral_code);
        $referredUsers = User::where('referred_by_id', $user->id)->latest()->get();
        $earnedCredits = StoreCredit::where('user_id', $user->id)->first()?->transactions()
            ->where('reference_type', 'referral')
            ->sum('amount') ?: 0.00;

        return view('storefront.referral', compact('user', 'referralLink', 'referredUsers', 'earnedCredits'));
    }
}


