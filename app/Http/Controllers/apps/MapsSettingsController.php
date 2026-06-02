<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class MapsSettingsController extends Controller
{
    /**
     * Show the Maps settings page.
     */
    public function index()
    {
        // Load settings from config and DB (if any)
        $config = Config::get('maps');
        $db = DB::table('maps_settings')->first();
        if ($db) {
            $config = array_merge($config, (array) $db);
        }
        return view('content.apps.app-maps-settings', ['settings' => $config]);
    }

    /**
     * Store the Maps settings.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'google_api_key' => 'required|string',
            'default_center.lat' => 'required|numeric',
            'default_center.lng' => 'required|numeric',
            'default_center.zoom' => 'required|integer',
        ]);
        // Flatten nested default_center for storage
        $data = [
            'google_api_key' => $validated['google_api_key'],
            'default_lat' => $validated['default_center']['lat'],
            'default_lng' => $validated['default_center']['lng'],
            'default_zoom' => $validated['default_center']['zoom'],
        ];
        DB::table('maps_settings')->updateOrInsert(['id' => 1], $data);
        return redirect()->back()->with('success', 'Maps settings saved successfully.');
    }
}
