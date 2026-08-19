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
    public function index()
    {
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

        return view('storefront.home', compact(
            'heroSliders',
            'featuredCategories',
            'featuredProducts',
            'trendingProducts',
            'bestSellers',
            'dealsOfTheDay'
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
            'price_low'  => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'popular'    => $query->orderByDesc('qty'),
            default      => $query->latest(),
        };

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::where('is_active', true)->withCount('products')->get();

        return view('storefront.shop', compact('products', 'categories'));
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

        return view('storefront.product-detail', compact('product', 'relatedProducts', 'frequentlyBought', 'availableStock'));
    }


    /**
     * Cart Page
     */
    public function cart()
    {
        $cart = session()->get('cart', []);
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        return view('storefront.cart', compact('cart', 'subtotal'));
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

        return response()->json([
            'success'  => true,
            'subtotal' => number_format($subtotal, 2),
            'cart'     => $cart,
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

        return view('storefront.checkout', compact('cart', 'subtotal', 'walletBalance', 'loyaltyPoints'));
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

            $orderNumber = 'ORD-' . strtoupper(Str::random(10));
            $user = Auth::user();

            $order = Order::create([
                'order_number'     => $orderNumber,
                'user_id'          => $user?->id,
                'total_amount'     => $subtotal,
                'payment_method'   => $request->payment_method,
                'payment_status'   => $request->payment_method === 'cod' ? 'pending' : 'paid',
                'order_status'     => 'pending',
                'shipping_address' => $request->shipping_address,
                'billing_address'  => $request->shipping_address,
                'branch_id'        => session('branch_id', 1),
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
}


