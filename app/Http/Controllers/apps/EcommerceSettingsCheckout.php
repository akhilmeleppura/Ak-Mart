<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class EcommerceSettingsCheckout extends Controller
{
  public function index()
  {
    $settings = StoreSetting::allAsArray();
    return view('content.apps.app-ecommerce-settings-checkout', compact('settings'));
  }

  public function store(Request $request)
  {
    $fields = [
      'customer_account',
      'contact_method',
      'shipping_address_phone',
      'marketing_consent'
    ];

    foreach ($fields as $field) {
      if ($request->has($field)) {
        StoreSetting::set($field, $request->input($field));
      }
    }

    return redirect()->back()->with('success', 'Checkout settings saved successfully!');
  }
}
