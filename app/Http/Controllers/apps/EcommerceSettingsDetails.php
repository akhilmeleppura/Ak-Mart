<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;

class EcommerceSettingsDetails extends Controller
{
    public function index()
    {
        $settings = StoreSetting::allAsArray();
        $branches = \App\Models\Branch\Branch::all();
        return view('content.apps.app-ecommerce-settings-details', compact('settings', 'branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name'     => 'nullable|string|max:255',
            'store_phone'    => 'nullable|string|max:30',
            'store_email'    => 'nullable|email|max:255',
            'sender_email'   => 'nullable|email|max:255',
            'business_name'  => 'nullable|string|max:255',
            'country'        => 'nullable|string|max:100',
            'address'        => 'nullable|string|max:255',
            'city'           => 'nullable|string|max:100',
            'state'          => 'nullable|string|max:100',
            'pincode'        => 'nullable|string|max:20',
            'timezone'       => 'nullable|string|max:100',
            'unit_system'    => 'nullable|string|max:50',
            'weight_unit'    => 'nullable|string|max:20',
            'currency'       => 'nullable|string|max:20',
            'order_prefix'   => 'nullable|string|max:20',
            'order_suffix'   => 'nullable|string|max:20',
            'default_branch' => 'nullable|exists:branches,id',
        ]);

        $fields = [
            'store_name', 'store_phone', 'store_email', 'sender_email',
            'business_name', 'country', 'address', 'city', 'state', 'pincode',
            'timezone', 'unit_system', 'weight_unit', 'currency',
            'order_prefix', 'order_suffix', 'default_branch'
        ];

        foreach ($fields as $field) {
            StoreSetting::set($field, $request->input($field));
        }

        return redirect()->route('app-ecommerce-settings-details')
            ->with('success', 'Store settings saved successfully!');
    }
}
