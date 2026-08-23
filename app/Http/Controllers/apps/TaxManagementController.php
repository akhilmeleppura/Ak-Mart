<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxRule;
use App\Models\StoreSetting;
use App\Models\Product;
use App\Services\TaxEngineService;

class TaxManagementController extends Controller
{
    /**
     * Display Tax & VAT Management Hub
     */
    public function index()
    {
        $taxRules = TaxRule::orderBy('priority', 'asc')->latest()->get();

        $settings = [
            'tax_enabled'            => (bool) StoreSetting::get('tax_enabled', true),
            'vat_default_rate'       => (float) StoreSetting::get('vat_default_rate', 5.00),
            'tax_calculation_mode'   => StoreSetting::get('tax_calculation_mode', 'exclusive'),
            'tax_calculation_basis'  => StoreSetting::get('tax_calculation_basis', 'shipping_address'), // shipping_address, store_address
            'b2b_vat_exemption'      => (bool) StoreSetting::get('b2b_vat_exemption', true),
            'tax_number'             => StoreSetting::get('tax_number', 'VAT-AKM-987654321'),
            'tax_display_on_invoice' => (bool) StoreSetting::get('tax_display_on_invoice', true),
        ];

        // Distinct tax classes used across products
        $taxClasses = [
            'standard'    => ['name' => 'Standard Rate', 'description' => 'General packaged supermarket goods and merchandise'],
            'reduced'     => ['name' => 'Reduced Rate', 'description' => 'Essential groceries, daily staples, baby food'],
            'zero_rate'   => ['name' => 'Zero Rate (0%)', 'description' => 'Unprocessed agricultural fresh produce & raw milk'],
            'luxury'      => ['name' => 'Luxury / Specialty', 'description' => 'Imported confections, specialty beverages & electronics'],
            'exempt'      => ['name' => 'Tax Exempt', 'description' => 'Items legally exempted from VAT / Sales Tax'],
        ];

        $totalRules = $taxRules->count();
        $activeRulesCount = $taxRules->where('is_active', true)->count();
        $totalProducts = Product::where('is_active', true)->count();
        $customTaxProducts = Product::whereNotNull('tax_rate')->count();

        return view('content.apps.ecommerce.settings.taxes', compact(
            'taxRules',
            'settings',
            'taxClasses',
            'totalRules',
            'activeRulesCount',
            'totalProducts',
            'customTaxProducts'
        ));
    }

    /**
     * Store new Area or Class Tax Rule
     */
    public function storeRule(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:100',
            'tax_class'            => 'required|string|max:50',
            'tax_type'             => 'required|string|in:percentage,fixed',
            'rate'                 => 'required|numeric|min:0|max:100',
            'country_code'         => 'nullable|string|max:10',
            'state_name'           => 'nullable|string|max:100',
            'postal_code_pattern'  => 'nullable|string|max:20',
            'priority'             => 'nullable|integer|min:1|max:99',
            'is_compound'          => 'nullable|boolean',
            'is_active'            => 'nullable|boolean',
            'calculation_mode'     => 'nullable|string|in:exclusive,inclusive',
        ]);

        $validated['country_code'] = strtoupper(trim($validated['country_code'] ?? '*')) ?: '*';
        $validated['state_name'] = trim($validated['state_name'] ?? '*') ?: '*';
        $validated['is_compound'] = $request->boolean('is_compound');
        $validated['is_active'] = $request->boolean('is_active', true);
        $validated['priority'] = (int) ($validated['priority'] ?? 1);
        $validated['calculation_mode'] = $validated['calculation_mode'] ?? 'exclusive';

        TaxRule::create($validated);

        return redirect()->back()->with('success', "Tax Rule '{$validated['name']}' created successfully!");
    }

    /**
     * Update Tax Rule
     */
    public function updateRule(Request $request, $id)
    {
        $rule = TaxRule::findOrFail($id);

        $validated = $request->validate([
            'name'                 => 'required|string|max:100',
            'tax_class'            => 'required|string|max:50',
            'tax_type'             => 'required|string|in:percentage,fixed',
            'rate'                 => 'required|numeric|min:0|max:100',
            'country_code'         => 'nullable|string|max:10',
            'state_name'           => 'nullable|string|max:100',
            'postal_code_pattern'  => 'nullable|string|max:20',
            'priority'             => 'nullable|integer|min:1|max:99',
            'is_compound'          => 'nullable|boolean',
            'is_active'            => 'nullable|boolean',
            'calculation_mode'     => 'nullable|string|in:exclusive,inclusive',
        ]);

        $validated['country_code'] = strtoupper(trim($validated['country_code'] ?? '*')) ?: '*';
        $validated['state_name'] = trim($validated['state_name'] ?? '*') ?: '*';
        $validated['is_compound'] = $request->boolean('is_compound');
        $validated['is_active'] = $request->boolean('is_active');
        $validated['priority'] = (int) ($validated['priority'] ?? 1);

        $rule->update($validated);

        return redirect()->back()->with('success', "Tax Rule '{$rule->name}' updated successfully!");
    }

    /**
     * Toggle Rule Active State
     */
    public function toggleRule($id)
    {
        $rule = TaxRule::findOrFail($id);
        $rule->is_active = !$rule->is_active;
        $rule->save();

        return response()->json([
            'success'   => true,
            'is_active' => $rule->is_active,
            'message'   => "Tax Rule '{$rule->name}' is now " . ($rule->is_active ? 'Active' : 'Disabled'),
        ]);
    }

    /**
     * Delete Tax Rule
     */
    public function deleteRule($id)
    {
        $rule = TaxRule::findOrFail($id);
        $name = $rule->name;
        $rule->delete();

        return redirect()->back()->with('success', "Tax Rule '{$name}' removed successfully.");
    }

    /**
     * Save Global VAT & Tax Settings
     */
    public function saveSettings(Request $request)
    {
        StoreSetting::set('tax_enabled', $request->boolean('tax_enabled') ? '1' : '0');
        StoreSetting::set('vat_default_rate', (string) ((float) $request->input('vat_default_rate', 5.0)));
        StoreSetting::set('tax_calculation_mode', $request->input('tax_calculation_mode', 'exclusive'));
        StoreSetting::set('tax_calculation_basis', $request->input('tax_calculation_basis', 'shipping_address'));
        StoreSetting::set('b2b_vat_exemption', $request->boolean('b2b_vat_exemption') ? '1' : '0');
        StoreSetting::set('tax_number', $request->input('tax_number', ''));
        StoreSetting::set('tax_display_on_invoice', $request->boolean('tax_display_on_invoice') ? '1' : '0');

        return redirect()->back()->with('success', 'VAT & Tax Settings saved successfully!');
    }

    /**
     * Simulate Tax Calculation AJAX
     */
    public function simulateTax(Request $request)
    {
        $taxEngine = app(TaxEngineService::class);

        $country = $request->input('country', 'US');
        $state = $request->input('state', 'CA');
        $zip = $request->input('zip', '90210');
        $amount = (float) $request->input('amount', 100.00);

        // Dummy cart
        $cart = [
            1 => ['id' => 1, 'price' => $amount, 'qty' => 1, 'tax_class' => $request->input('tax_class', 'standard')]
        ];

        $res = $taxEngine->calculateCartTax($cart, [
            'country' => $country,
            'state'   => $state,
            'zip'     => $zip
        ]);

        return response()->json([
            'success' => true,
            'result'  => $res,
            'amount'  => $amount,
            'total'   => $amount + ($res['is_inclusive'] ? 0 : $res['total_tax']),
        ]);
    }
}
