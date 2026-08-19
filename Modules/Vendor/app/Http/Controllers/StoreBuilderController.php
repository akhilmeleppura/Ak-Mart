<?php

namespace App\Http\Controllers\apps\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Storage;

class StoreBuilderController extends Controller
{
    /**
     * Show the store customization page.
     */
    public function index()
    {
        $settings = StoreSetting::allAsArray();
        
        // Default theme settings
        $defaults = [
            'theme_primary_color' => '#7367f0',
            'theme_secondary_color' => '#82868b',
            'store_logo' => null,
            'hero_title' => 'Welcome to Ak Mart',
            'hero_subtitle' => 'The best products at the best prices.',
            'contact_email' => auth()->user()->email,
            'facebook_url' => '',
            'instagram_url' => '',
        ];

        $theme = array_merge($defaults, $settings);

        return view('content.apps.vendor.store-builder', compact('theme'));
    }

    /**
     * Save the theme and store settings.
     */
    public function store(Request $request)
    {
        $data = $request->all();

        // Handle Logo Upload
        if ($request->hasFile('store_logo')) {
            $path = $request->file('store_logo')->store('store_logos', 'public');
            StoreSetting::set('store_logo', Storage::url($path));
        }

        // Save other text/color settings
        $allowedKeys = [
            'theme_primary_color', 'theme_secondary_color', 
            'hero_title', 'hero_subtitle', 
            'contact_email', 'facebook_url', 'instagram_url'
        ];

        foreach ($data as $key => $value) {
            if (in_array($key, $allowedKeys)) {
                StoreSetting::set($key, $value);
            }
        }

        return redirect()->back()->with('success', 'Store theme updated successfully!');
    }
}
