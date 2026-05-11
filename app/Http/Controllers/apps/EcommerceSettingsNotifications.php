<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class EcommerceSettingsNotifications extends Controller
{
  public function index()
  {
    $settings = StoreSetting::allAsArray();
    return view('content.apps.app-ecommerce-settings-notifications', compact('settings'));
  }

  public function store(Request $request)
  {
    $fields = [
      'order_notification',
      'shipping_notification',
      'customer_notification'
    ];

    foreach ($fields as $field) {
      if ($request->has($field)) {
        StoreSetting::set($field, $request->input($field));
      }
    }

    return redirect()->back()->with('success', 'Notification settings saved successfully!');
  }
}
