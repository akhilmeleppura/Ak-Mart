<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\B2bCompany;
use App\Models\B2bBuyer;
use App\Models\User;
use App\Models\Product;
use App\Models\B2bTierPrice;
use Illuminate\Support\Str;

class B2bCompanyController extends Controller
{
    public function index()
    {
        $companies = B2bCompany::withCount(['buyers', 'quotes'])->latest()->get();
        $totalCreditExtended = $companies->sum('credit_limit');
        $totalOutstandingBalance = $companies->sum('current_balance');
        $users = User::all();

        return view('content.apps.b2b.companies', compact(
            'companies',
            'totalCreditExtended',
            'totalOutstandingBalance',
            'users'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'contact_email'   => 'required|email',
            'contact_phone'   => 'nullable|string',
            'tax_id'          => 'nullable|string',
            'credit_limit'    => 'nullable|numeric|min:0',
            'payment_terms'   => 'required|in:prepaid,net_15,net_30,net_60',
            'billing_address' => 'nullable|string',
        ]);

        $company = B2bCompany::create([
            'name'            => $request->name,
            'company_code'    => 'B2B-' . strtoupper(Str::random(5)),
            'contact_email'   => $request->contact_email,
            'contact_phone'   => $request->contact_phone,
            'tax_id'          => $request->tax_id,
            'credit_limit'    => $request->credit_limit ?? 0,
            'payment_terms'   => $request->payment_terms,
            'billing_address' => $request->billing_address,
            'status'          => 'active',
        ]);

        return redirect()->route('app-b2b-companies')->with('success', "B2B Company {$company->name} registered!");
    }

    public function show(B2bCompany $company)
    {
        $company->load(['buyers.user', 'quotes.user', 'tierPrices.product']);
        $users = User::all();
        $products = Product::where('is_active', true)->get();

        return view('content.apps.b2b.company-details', compact('company', 'users', 'products'));
    }

    public function addBuyer(Request $request, B2bCompany $company)
    {
        $request->validate([
            'user_id'            => 'required|exists:users,id',
            'role'               => 'required|in:admin,buyer,approver',
            'spending_limit'     => 'nullable|numeric|min:0',
            'can_approve_orders' => 'nullable|boolean',
        ]);

        B2bBuyer::updateOrCreate(
            ['b2b_company_id' => $company->id, 'user_id' => $request->user_id],
            [
                'role'               => $request->role,
                'spending_limit'     => $request->spending_limit,
                'can_approve_orders' => (bool)$request->can_approve_orders,
            ]
        );

        return back()->with('success', 'B2B Buyer added to company account successfully!');
    }

    public function addTierPrice(Request $request, B2bCompany $company)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'min_qty'    => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
        ]);

        B2bTierPrice::updateOrCreate(
            [
                'b2b_company_id' => $company->id,
                'product_id'     => $request->product_id,
                'min_qty'        => $request->min_qty,
            ],
            ['unit_price' => $request->unit_price]
        );

        return back()->with('success', 'Negotiated wholesale tier price saved!');
    }
}
