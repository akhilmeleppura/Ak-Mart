<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AbandonedCart;

class AbandonedCartController extends Controller
{
    public function index()
    {
        $carts = AbandonedCart::with('user')->latest()->paginate(20);
        $totalAbandoned = AbandonedCart::whereNull('recovered_at')->count();
        $totalRecovered = AbandonedCart::whereNotNull('recovered_at')->count();
        $potentialRevenue = AbandonedCart::whereNull('recovered_at')->sum('total_amount');

        return view('content.apps.marketing.abandoned-carts', compact(
            'carts',
            'totalAbandoned',
            'totalRecovered',
            'potentialRevenue'
        ));
    }

    public function sendRecovery(Request $request, AbandonedCart $cart)
    {
        $cart->increment('recovery_emails_sent');
        return back()->with('success', "Recovery notification dispatched to {$cart->email}!");
    }
}
