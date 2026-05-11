<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class EcommerceSettingsShipping extends Controller
{
  public function index()
  {
    $settings = StoreSetting::allAsArray();
    return view('content.apps.app-ecommerce-settings-shipping', compact('settings'));
  }

  public function store(Request $request)
  {
    $fields = [
      'shipping_origin',
      'shipping_zones',
      'shipping_rates'
    ];

    foreach ($fields as $field) {
      if ($request->has($field)) {
        StoreSetting::set($field, $request->input($field));
      }
    }

    return redirect()->back()->with('success', 'Shipping settings saved successfully!');
  }
}
