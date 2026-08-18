<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GiftCard;
use Illuminate\Support\Str;

class GiftCardController extends Controller
{
    public function index()
    {
        $giftCards = GiftCard::latest()->paginate(20);
        $totalIssuedValue = GiftCard::sum('initial_balance');
        $activeBalance = GiftCard::where('is_active', true)->sum('current_balance');

        return view('content.apps.customer.gift-cards', compact('giftCards', 'totalIssuedValue', 'activeBalance'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount'          => 'required|numeric|min:1',
            'recipient_email' => 'nullable|email',
            'expiry_days'     => 'nullable|integer|min:1',
        ]);

        $code = 'GC-' . strtoupper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4));
        $expiry = $request->expiry_days ? now()->addDays($request->expiry_days) : now()->addYear();

        $gc = GiftCard::create([
            'code'            => $code,
            'initial_balance' => $request->amount,
            'current_balance' => $request->amount,
            'recipient_email' => $request->recipient_email,
            'pin'             => rand(1000, 9999),
            'expiry_date'     => $expiry,
            'is_active'       => true,
            'created_by'      => auth()->id(),
        ]);

        return redirect()->route('app-gift-cards')->with('success', "Gift Card {$gc->code} created with balance \${$gc->initial_balance}!");
    }

    public function lookup(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        $gc = GiftCard::where('code', trim($request->code))->first();

        if (!$gc || !$gc->isValid()) {
            return response()->json(['valid' => false, 'message' => 'Invalid or expired gift card.']);
        }

        return response()->json([
            'valid'   => true,
            'code'    => $gc->code,
            'balance' => $gc->current_balance,
        ]);
    }
}
