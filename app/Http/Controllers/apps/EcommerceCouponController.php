<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;

class EcommerceCouponController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Coupon::query();

            // Date Filter
            if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            $coupons = $query->latest()->get();
            $formatted = $coupons->map(function ($coupon) {
                return [
                    'id' => $coupon->id,
                    'code' => $coupon->code,
                    'type' => $coupon->type,
                    'value' => $coupon->value,
                    'status' => $coupon->is_active ? 'active' : 'inactive',
                    'usage' => $coupon->usage_count . ' / ' . ($coupon->usage_limit ?? '∞'),
                    'start_date' => $coupon->start_date ? $coupon->start_date->format('Y-m-d') : '-',
                    'end_date' => $coupon->end_date ? $coupon->end_date->format('Y-m-d') : '-',
                ];
            });
            return response()->json(['data' => $formatted]);
        }
        return view('content.apps.app-ecommerce-coupon-list');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:coupons,code|max:50',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->only(['code', 'type', 'value', 'usage_limit', 'start_date', 'end_date', 'min_spend', 'max_spend']);
        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        Coupon::create($data);

        return response()->json(['success' => 'Coupon created successfully!']);
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        return response()->json($coupon);
    }

    public function update(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);

        $request->validate([
            'code' => 'required|max:50|unique:coupons,code,' . $id,
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = $request->only(['code', 'type', 'value', 'usage_limit', 'start_date', 'end_date', 'min_spend', 'max_spend']);
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $coupon->update($data);

        return response()->json(['success' => 'Coupon updated successfully!']);
    }

    public function destroy($id)
    {
        Coupon::findOrFail($id)->delete();
        return response()->json(['success' => 'Coupon deleted successfully!']);
    }

    /**
     * Bulk Generate Coupons
     */
    public function bulkGenerate(Request $request)
    {
        $request->validate([
            'count' => 'required|integer|min:1|max:500',
            'prefix' => 'nullable|string|max:10',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
        ]);

        $generated = 0;
        for ($i = 0; $i < $request->count; $i++) {
            $code = strtoupper(($request->prefix ?? 'AK') . \Illuminate\Support\Str::random(8));
            
            // Ensure uniqueness
            if (Coupon::where('code', $code)->exists()) {
                $i--; continue;
            }

            Coupon::create([
                'code' => $code,
                'type' => $request->type,
                'value' => $request->value,
                'is_active' => true,
                'usage_count' => 0
            ]);
            $generated++;
        }

        return response()->json(['success' => true, 'message' => "Successfully generated $generated unique coupons."]);
    }
}
