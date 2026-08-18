<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class EcommerceSettingsBranding extends Controller
{
    public function index()
    {
        $settings = StoreSetting::allAsArray();
        return view('content.apps.app-ecommerce-settings-branding', compact('settings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'site_logo_file'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'site_logo_dark_file'  => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
            'site_favicon_file'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,ico,webp|max:2048',
            'email_logo_file'       => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:4096',
        ]);

        $brandingDir = public_path('uploads/branding');
        if (!File::exists($brandingDir)) {
            File::makeDirectory($brandingDir, 0755, true);
        }

        $imageFields = ['site_logo', 'site_logo_dark', 'site_favicon', 'email_logo'];

        foreach ($imageFields as $field) {
            // Handle uploaded file
            $fileKey = $field . '_file';
            if ($request->hasFile($fileKey) && $request->file($fileKey)->isValid()) {
                $file = $request->file($fileKey);
                $filename = $field . '_' . time() . '.' . $file->getClientOriginalExtension();
                $file->move($brandingDir, $filename);
                StoreSetting::set($field, 'uploads/branding/' . $filename);
            }
            // Handle Base64 cropped image from interactive editor
            elseif ($request->filled($field . '_base64')) {
                $data = $request->input($field . '_base64');
                if (preg_match('/^data:image\/(\w+);base64,/', $data, $type)) {
                    $data = substr($data, strpos($data, ',') + 1);
                    $ext = strtolower($type[1]);
                    if (!in_array($ext, ['png', 'jpg', 'jpeg', 'gif', 'webp', 'ico'])) {
                        $ext = 'png';
                    }
                    $data = base64_decode($data);
                    if ($data !== false) {
                        $filename = $field . '_edited_' . time() . '.' . $ext;
                        File::put($brandingDir . '/' . $filename, $data);
                        StoreSetting::set($field, 'uploads/branding/' . $filename);
                    }
                }
            }
            // Handle URL or fallback text input
            elseif ($request->has($field) && !empty($request->input($field))) {
                StoreSetting::set($field, $request->input($field));
            }
        }

        if ($request->filled('brand_primary_color')) {
            StoreSetting::set('brand_primary_color', $request->input('brand_primary_color'));
        }

        return redirect()->route('app-ecommerce-settings-branding')
            ->with('success', 'Store Branding, Logos & Icons saved successfully!');
    }
}
