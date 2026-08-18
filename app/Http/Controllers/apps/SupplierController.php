<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $suppliers = Supplier::latest()->get();
            return response()->json(['data' => $suppliers]);
        }

        $suppliers = Supplier::latest()->paginate(15);
        return view('content.apps.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric'
        ]);

        $supplier = Supplier::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Supplier created successfully.', 'supplier' => $supplier]);
        }

        return redirect()->back()->with('success', 'Supplier created successfully.');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'balance' => 'nullable|numeric'
        ]);

        $supplier->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Supplier updated successfully.']);
        }

        return redirect()->back()->with('success', 'Supplier updated successfully.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return response()->json(['success' => true, 'message' => 'Supplier deleted successfully.']);
    }
}
