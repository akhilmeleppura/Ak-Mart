<?php

namespace Modules\Billing\App\Http\Controllers\PaymentOptions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Accounting\Services\MenuService;
use App\Models\Payments\PaymentOption;
use Modules\Billing\App\Models\BillingSettingPersionalisedPaymentOption;
use Illuminate\Support\Facades\Log;

class PaymentOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $moduleName = 'Billing'; 
        $currentRoute = request()->route()->getName();

        // Load dynamic menu for this module
        $menu = MenuService::getMenu($moduleName, $currentRoute);

        // Get all payment options
        $paymentOptions = PaymentOption::orderBy('name')->get();
        $defaultOption = $paymentOptions->firstWhere('is_default', true);
        
        // Initialize selected payment options as empty array
        $selectedPaymentOptions = [];
        
        // Check if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Check if user has the required properties
            if ($user && isset($user->id)) {
                // Get company and branch IDs with fallbacks
                $userCompanyId = $user->current_company_id ?? null;
                $userBranchId = $user->current_branch_id ?? null;
                
                // Only try to get personalized options if we have a valid user ID
                try {
                    $personalizedOptions = BillingSettingPersionalisedPaymentOption::where('user_id', $user->id)
                        ->where(function($query) use ($userCompanyId, $userBranchId) {
                            $query->where('company_id', $userCompanyId)
                                  ->orWhereNull('company_id');
                        })
                        ->where(function($query) use ($userBranchId) {
                            $query->where('branch_id', $userBranchId)
                                  ->orWhereNull('branch_id');
                        })
                        ->first();
                        
                    if ($personalizedOptions && isset($personalizedOptions->payment_options_id)) {
                        $selectedPaymentOptions = $personalizedOptions->payment_options_id;
                    }
                } catch (\Exception $e) {
                    // Log the error but continue with empty selected options
                    \Log::error('Error fetching personalized payment options: ' . $e->getMessage());
                }
            }
        }

        return view('billing::payment-options.index', compact(
            'menu', 
            'moduleName', 
            'paymentOptions', 
            'defaultOption',
            'selectedPaymentOptions'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('billing::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('billing::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('billing::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        // Handle setting default option
        if ($request->has('set_default')) {
            $optionId = $request->set_default;
            
            // Unset all current defaults
            PaymentOption::where('is_default', true)->update(['is_default' => false]);
            
            // Set new default
            $option = PaymentOption::findOrFail($optionId);
            $option->is_default = true;
            $option->save();
            
            return redirect()->route('payment-options.index')
                ->with('success', 'Default payment option updated successfully.');
        }
        
        // Handle updating active status
        if ($request->has('active_status')) {
            foreach ($request->active_status as $optionId => $status) {
                $option = PaymentOption::findOrFail($optionId);
                $option->is_active = $status == 1;
                $option->save();
            }
            
            return redirect()->route('payment-options.index')
                ->with('success', 'Payment options updated successfully.');
        }
        
        // Handle updating personalized payment options
        if ($request->has('selected_payment_options')) {
            // Check if user is authenticated
            if (!Auth::check()) {
                return redirect()->route('payment-options.index')
                    ->with('error', 'You must be logged in to update payment options.');
            }
            
            $user = Auth::user();
            
            // Check if user has the required properties
            if (!$user || !isset($user->id)) {
                return redirect()->route('payment-options.index')
                    ->with('error', 'Invalid user information.');
            }
            
            // Get company and branch IDs with fallbacks
            $userCompanyId = $user->current_company_id ?? null;
            $userBranchId = $user->current_branch_id ?? null;
            
            try {
                // Find or create personalized options record
                $personalizedOptions = BillingSettingPersionalisedPaymentOption::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'company_id' => $userCompanyId,
                        'branch_id' => $userBranchId,
                    ],
                    [
                        'payment_options_id' => $request->selected_payment_options,
                    ]
                );
                
                return redirect()->route('billing.payment-options.index')
                    ->with('success', 'Your payment options have been updated successfully.');
            } catch (\Exception $e) {
                // Log the error and return with error message
                Log::error('Error updating personalized payment options: ' . $e->getMessage());
                
                return redirect()->route('billing.payment-options.index')
                    ->with('error', 'An error occurred while updating your payment options.');
            }
        }
        
        return redirect()->route('billing.payment-options.index')
            ->with('error', 'No valid action specified.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}