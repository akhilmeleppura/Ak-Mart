<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreSetting;

class PaymentSettingsController extends Controller
{
    /**
     * Show the payment gateway configuration page.
     */
    public function index()
    {
        $settings = StoreSetting::allAsArray();
        return view('content.apps.vendor.payment-settings', compact('settings'));
    }

    /**
     * Save gateway credentials.
     */
    public function store(Request $request)
    {
        $keys = [
            'stripe_key', 'stripe_secret', 'stripe_webhook_secret',
            'paypal_client_id', 'paypal_secret', 'paypal_mode',
            'phonepe_merchant_id', 'phonepe_salt_key', 'phonepe_salt_index'
        ];

        foreach ($keys as $key) {
            if ($request->has($key)) {
                StoreSetting::set($key, $request->get($key));
            }
        }

        return redirect()->back()->with('success', 'Payment gateway settings updated.');
    }
}
