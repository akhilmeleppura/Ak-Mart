<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\StoreCredit;
use App\Models\LoyaltyTransaction;
use App\Models\ReturnRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class CustomerAccountController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $totalOrders = Order::where('user_id', $user->id)->count();
        $totalSpent = Order::where('user_id', $user->id)->where('payment_status', 'paid')->sum('total_amount');
        $walletBalance = StoreCredit::where('user_id', $user->id)->value('balance') ?: 0;
        $loyaltyPoints = LoyaltyTransaction::getCustomerBalance($user->id);
        $recentOrders = Order::where('user_id', $user->id)->latest()->take(5)->get();

        return view('customer.dashboard', compact(
            'user',
            'totalOrders',
            'totalSpent',
            'walletBalance',
            'loyaltyPoints',
            'recentOrders'
        ));
    }

    public function orders()
    {
        $orders = Order::where('user_id', Auth::id())->latest()->paginate(10);
        return view('customer.orders', compact('orders'));
    }

    public function orderDetails($orderNumber)
    {
        $order = Order::with(['items.product', 'transactions'])
            ->where('user_id', Auth::id())
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('customer.order-details', compact('order'));
    }

    public function profile()
    {
        $user = Auth::user();
        return view('customer.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $user->update(['password' => Hash::make($request->password)]);
        }

        return back()->with('success', 'Profile updated successfully!');
    }

    public function wishlist()
    {
        $wishlistItems = Wishlist::with('product')->where('user_id', Auth::id())->latest()->get();
        return view('customer.wishlist', compact('wishlistItems'));
    }

    public function wallet()
    {
        $user = Auth::user();
        $credit = StoreCredit::with('transactions')->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0, 'currency' => 'USD']
        );

        return view('customer.wallet', compact('credit'));
    }

    public function loyalty()
    {
        $user = Auth::user();
        $transactions = LoyaltyTransaction::where('customer_id', $user->id)->latest()->paginate(15);
        $totalPoints = LoyaltyTransaction::getCustomerBalance($user->id);

        return view('customer.loyalty', compact('transactions', 'totalPoints'));
    }
}
