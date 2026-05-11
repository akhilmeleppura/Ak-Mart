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
      'paypal_email',
      'stripe_key',
      'stripe_secret',
      'manual_payment_instruction'
    ];

    foreach ($fields as $field) {
      if ($request->has($field)) {
        StoreSetting::set($field, $request->input($field));
      }
    }

    return redirect()->back()->with('success', 'Payment settings saved successfully!');
  }
}
