<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\B2bQuote;
use App\Models\B2bQuoteItem;
use App\Models\B2bCompany;
use App\Models\Product;
use App\Services\B2bService;
use Illuminate\Support\Str;

class B2bQuoteController extends Controller
{
    public function index()
    {
        $quotes = B2bQuote::with(['company', 'user'])->withCount('items')->latest()->get();
        $companies = B2bCompany::where('status', 'active')->get();
        $products = Product::where('is_active', true)->get();

        return view('content.apps.b2b.quotes', compact('quotes', 'companies', 'products'));
    }

    public function store(Request $request, B2bService $b2bService)
    {
        $request->validate([
            'b2b_company_id' => 'required|exists:b2b_companies,id',
            'items'          => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.qty'        => 'required|integer|min:1',
            'discount'       => 'nullable|numeric|min:0|max:100',
            'valid_until'    => 'nullable|date',
            'notes'          => 'nullable|string',
        ]);

        $calc = $b2bService->calculateQuote($request->items, (float)($request->discount ?? 0));

        $quote = B2bQuote::create([
            'quote_number'   => 'QTE-' . strtoupper(Str::random(6)),
            'b2b_company_id' => $request->b2b_company_id,
            'user_id'        => auth()->id(),
            'subtotal'       => $calc['subtotal'],
            'discount'       => $calc['discount'],
            'total'          => $calc['total'],
            'status'         => 'submitted',
            'valid_until'    => $request->valid_until ?: now()->addDays(14),
            'notes'          => $request->notes,
        ]);

        foreach ($calc['items'] as $item) {
            B2bQuoteItem::create([
                'b2b_quote_id'    => $quote->id,
                'product_id'      => $item['product_id'],
                'qty'             => $item['qty'],
                'requested_price' => $item['requested_price'],
                'subtotal'        => $item['subtotal'],
            ]);
        }

        return redirect()->route('app-b2b-quotes')->with('success', "Quote #{$quote->quote_number} generated successfully!");
    }

    public function updateStatus(Request $request, B2bQuote $quote)
    {
        $request->validate([
            'status' => 'required|in:draft,submitted,approved,rejected,converted',
        ]);

        $quote->update(['status' => $request->status]);

        return back()->with('success', "Quote #{$quote->quote_number} marked as {$request->status}!");
    }
}
