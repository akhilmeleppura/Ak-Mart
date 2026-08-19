<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Currency;

class CurrencyController extends Controller
{
    /**
     * List all currencies.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Currency::query();

            // Date Filter
            if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            $currencies = $query->latest()->get();
            return response()->json(['data' => $currencies]);
        }

        return view('content.apps.saas.currencies');
    }

    /**
     * Store a new currency.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|size:3|unique:currencies',
            'symbol' => 'required|string',
            'exchange_rate' => 'required|numeric'
        ]);

        $currency = Currency::create($request->all());

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Currency added successfully.', 'data' => $currency]);
        }

        return redirect()->back()->with('success', 'Currency added successfully.');
    }

    /**
     * Update currency.
     */
    public function update(Request $request, Currency $currency)
    {
        $request->validate([
            'name' => 'required|string',
            'code' => 'required|string|size:3|unique:currencies,code,' . $currency->id,
            'symbol' => 'required|string',
            'exchange_rate' => 'required|numeric'
        ]);

        $currency->update($request->all());

        return response()->json(['success' => true, 'message' => 'Currency updated successfully.']);
    }

    /**
     * Delete currency.
     */
    public function destroy(Currency $currency)
    {
        $currency->delete();
        return response()->json(['success' => true, 'message' => 'Currency deleted successfully.']);
    }

    /**
     * Toggle currency status.
     */
    public function toggle(Currency $currency)
    {
        $currency->update(['is_active' => !$currency->is_active]);
        
        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'Status updated.']);
        }

        return redirect()->back()->with('success', 'Currency status updated.');
    }
}
