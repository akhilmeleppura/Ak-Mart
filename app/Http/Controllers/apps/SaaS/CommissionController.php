<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommissionRule;
use App\Models\Category;

use App\Models\CommissionTier;

class CommissionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = CommissionRule::with(['category', 'branch']);

            // Date Filter
            if ($request->has('start_date') && $request->start_date && $request->has('end_date') && $request->end_date) {
                $query->whereBetween('created_at', [$request->start_date . ' 00:00:00', $request->end_date . ' 23:59:59']);
            }

            $rules = $query->latest()->get();
            return response()->json(['data' => $rules]);
        }

        $categories = Category::all();
        $tiers = CommissionTier::orderBy('min_amount')->get();
        return view('content.apps.saas.commission-rules', compact('categories', 'tiers'));
    }

    public function storeTier(Request $request)
    {
        $request->validate([
            'min_amount' => 'required|numeric|min:0',
            'max_amount' => 'nullable|numeric|gt:min_amount',
            'percentage' => 'required|numeric|min:0|max:100'
        ]);

        CommissionTier::create($request->all());

        return response()->json(['success' => true, 'message' => 'Commission tier created.']);
    }

    public function deleteTier(CommissionTier $tier)
    {
        $tier->delete();
        return response()->json(['success' => true, 'message' => 'Commission tier deleted.']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'type'        => 'required|in:flat,percentage',
            'value'       => 'required|numeric|min:0',
            'is_global'   => 'boolean',
            'category_id' => 'nullable|exists:categories,id',
            'branch_id'   => 'nullable|exists:branches,id',
        ]);

        CommissionRule::create([
            'name'        => $request->name,
            'type'        => $request->type,
            'value'       => $request->value,
            'is_global'   => $request->boolean('is_global'),
            'category_id' => $request->category_id,
            'branch_id'   => $request->branch_id,
            'is_active'   => true,
        ]);

        return response()->json(['success' => true, 'message' => 'Commission rule created.']);
    }

    public function update(Request $request, CommissionRule $commissionRule)
    {
        $request->validate([
            'name'      => 'required|string|max:255',
            'type'      => 'required|in:flat,percentage',
            'value'     => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $commissionRule->update($request->only(['name', 'type', 'value', 'is_active', 'is_global', 'category_id', 'branch_id']));

        return response()->json(['success' => true, 'message' => 'Commission rule updated.']);
    }

    public function destroy(CommissionRule $commissionRule)
    {
        $commissionRule->delete();
        return response()->json(['success' => true, 'message' => 'Commission rule deleted.']);
    }
}
