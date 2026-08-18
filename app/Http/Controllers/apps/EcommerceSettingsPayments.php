<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class EcommerceSettingsPayments extends Controller
{
  public function index()
  {
    $settings = StoreSetting::allAsArray();
    return view('content.apps.app-ecommerce-settings-payments', compact('settings'));
  }

  public function store(Request $request)
  {
    $fields = [
      'payment_provider',
      'paypal_enabled',
      'paypal_email',
      'paypal_client_id',
      'paypal_secret',
      'stripe_enabled',
      'stripe_key',
      'stripe_secret',
      'cod_enabled',
      'manual_payment_instruction'
    ];

    foreach ($fields as $field) {
      StoreSetting::set($field, $request->input($field, '0'));
    }

    return redirect()->back()->with('success', 'Payment settings saved successfully!');
  }
}
