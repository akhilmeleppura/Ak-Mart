<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class EcommerceSettingsLocations extends Controller
{
  public function index()
  {
    $settings = StoreSetting::allAsArray();
    return view('content.apps.app-ecommerce-settings-locations', compact('settings'));
  }

  public function store(Request $request)
  {
    $fields = [
      'location_name',
      'location_email',
      'location_phone',
      'location_address'
    ];

    foreach ($fields as $field) {
      if ($request->has($field)) {
        StoreSetting::set($field, $request->input($field));
      }
    }

    return redirect()->back()->with('success', 'Location settings saved successfully!');
  }
}
