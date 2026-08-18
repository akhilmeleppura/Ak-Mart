<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\SavedCart;
use App\Models\Order;
use App\Models\StoreCredit;
use App\Models\ReturnRequest;

class CustomerPortalController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $orders = Order::where('user_id', $user->id)->with('items')->latest()->take(10)->get();
        $wishlist = Wishlist::where('user_id', $user->id)->with('product')->get();
        $savedCarts = SavedCart::where('user_id', $user->id)->latest()->get();
        $storeCredit = StoreCredit::firstOrCreate(['user_id' => $user->id], ['balance' => 0]);
        $returnRequests = ReturnRequest::whereHas('order', fn($q) => $q->where('user_id', $user->id))->latest()->get();

        return view('content.apps.customer.portal', compact(
            'user',
            'orders',
            'wishlist',
            'savedCarts',
            'storeCredit',
            'returnRequests'
        ));
    }

    public function toggleWishlist(Request $request)
    {
        $request->validate(['product_id' => 'required|exists:products,id']);
        $user = auth()->user();

        $existing = Wishlist::where('user_id', $user->id)->where('product_id', $request->product_id)->first();
        if ($existing) {
            $existing->delete();
            return response()->json(['success' => true, 'action' => 'removed']);
        }

        Wishlist::create(['user_id' => $user->id, 'product_id' => $request->product_id]);
        return response()->json(['success' => true, 'action' => 'added']);
    }

    public function saveCart(Request $request)
    {
        $request->validate([
            'name'      => 'nullable|string|max:100',
            'cart_data' => 'required|array',
        ]);

        $cart = SavedCart::create([
            'user_id'   => auth()->id(),
            'name'      => $request->name ?: ('Saved Cart ' . now()->format('M d')),
            'cart_data' => $request->cart_data,
        ]);

        return response()->json(['success' => true, 'id' => $cart->id]);
    }
}
