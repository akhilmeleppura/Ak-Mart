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
      'notify_signup_email', 'notify_signup_app',
      'notify_reset_email', 'notify_reset_app',
      'notify_invite_email', 'notify_invite_app',
      'notify_purchase_email', 'notify_purchase_app',
      'notify_cancel_email', 'notify_cancel_app',
      'notify_refund_email', 'notify_refund_app'
    ];

    foreach ($fields as $field) {
      StoreSetting::set($field, $request->input($field, '0'));
    }

    return redirect()->back()->with('success', 'Notification settings saved successfully!');
  }
}
