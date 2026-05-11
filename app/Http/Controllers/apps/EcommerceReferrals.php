<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class EcommerceReferrals extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Use customers as "referral" rows — realistic demonstration
            $users = User::withCount('orders')
                ->with('orders')
                ->get();

            $data = $users->map(function ($user, $index) {
                $totalSpent = $user->orders->sum('total_amount');
                $statuses   = ['Active', 'Inactive', 'Pending'];
                $status     = $statuses[$user->id % 3];

                return [
                    'id'          => $user->id,
                    'user'        => $user->name,
                    'email'       => $user->email,
                    'avatar'      => null,
                    'referred_id' => 'REF-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'status'      => $status,
                    'value'       => '$' . number_format($totalSpent, 2),
                    'earnings'    => '$' . number_format($totalSpent * 0.1, 2), // 10% commission
                ];
            });

            return response()->json(['data' => $data]);
        }

        // Summary cards
        $totalEarning  = Order::sum('total_amount') * 0.1;   // 10% referral commission
        $unpaidEarning = Order::whereMonth('created_at', now()->month)->sum('total_amount') * 0.1;
        $signups       = User::count();
        $totalOrders   = Order::count();
        $conversionRate = $signups > 0 ? round(($totalOrders / $signups) * 100, 1) : 0;

        return view('content.apps.app-ecommerce-referrals', compact(
            'totalEarning', 'unpaidEarning', 'signups', 'conversionRate'
        ));
    }
}
