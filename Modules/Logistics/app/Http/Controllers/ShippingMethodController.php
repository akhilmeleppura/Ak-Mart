<?php

namespace App\Http\Controllers\apps\Logistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ShippingMethod;

class ShippingMethodController extends Controller
{
    public function index()
    {
        $methods = ShippingMethod::all();
        return view('content.apps.logistics.shipping-methods', compact('methods'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'carrier_code' => 'required|string|max:50',
            'base_cost' => 'required|numeric|min:0',
        ]);

        ShippingMethod::create([
            'name' => $request->name,
            'carrier_code' => $request->carrier_code,
            'base_cost' => $request->base_cost,
            'is_active' => true,
            'settings' => $request->settings ?? []
        ]);

        return redirect()->back()->with('success', 'Shipping method added successfully.');
    }

    public function toggle(ShippingMethod $method)
    {
        $method->update(['is_active' => !$method->is_active]);
        return response()->json(['success' => true]);
    }

    public function destroy(ShippingMethod $method)
    {
        $method->delete();
        return redirect()->back()->with('success', 'Shipping method deleted.');
    }
}
