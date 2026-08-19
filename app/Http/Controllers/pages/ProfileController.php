<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Models\AuditLog;
use App\Models\Branch\Branch;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user's profile overview.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth-login-basic');
        }

        $branch = null;
        if ($user->branch_id) {
            $branch = Branch::find($user->branch_id);
        }

        // Fetch recent audit activity for this user
        $activities = AuditLog::where('user_id', $user->id)
            ->latest()
            ->take(10)
            ->get();

        // Calculate user metrics
        $ordersCount = $user->orders()->count();
        $reviewsCount = $user->reviews()->count();

        $roleName = $user->roles->first()?->name 
            ?? ($user->isSupremeAdmin() ? 'Supreme Admin' 
            : ($user->isSuperAdmin() ? 'Super Admin' 
            : ucfirst(str_replace('_', ' ', $user->user_type ?? 'Staff'))));

        return view('content.pages.pages-profile-user', compact(
            'user',
            'branch',
            'activities',
            'ordersCount',
            'reviewsCount',
            'roleName'
        ));
    }

    /**
     * Display the edit account settings page.
     */
    public function edit(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('auth-login-basic');
        }

        $branch = $user->branch_id ? Branch::find($user->branch_id) : null;
        $branches = Branch::all();

        return view('content.pages.pages-account-settings-account', compact('user', 'branch', 'branches'));
    }

    /**
     * Update permitted profile fields.
     */
    public function update(UpdateProfileRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $oldValues = [
            'name' => $user->name,
            'phone' => $user->phone,
            'locale' => $user->locale,
            'town' => $user->town,
            'country' => $user->country,
        ];

        // Update allowed fields only (never allow user to elevate role/admin permissions via profile)
        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->address_line_1 = $validated['address_line_1'] ?? null;
        $user->address_line_2 = $validated['address_line_2'] ?? null;
        $user->town = $validated['town'] ?? null;
        $user->state = $validated['state'] ?? null;
        $user->post_code = $validated['post_code'] ?? null;
        $user->country = $validated['country'] ?? null;
        $user->marketing_opt_out = $request->has('marketing_opt_out') ? (bool)$request->marketing_opt_out : false;

        // Handle locale change if supplied
        if (!empty($validated['locale'])) {
            $user->locale = $validated['locale'];
            session(['locale' => $validated['locale']]);
            App::setLocale($validated['locale']);
        }

        $user->save();

        // Audit log
        try {
            AuditLog::create([
                'user_id' => $user->id,
                'event' => 'profile_updated',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'old_values' => json_encode($oldValues),
                'new_values' => json_encode([
                    'name' => $user->name,
                    'phone' => $user->phone,
                    'locale' => $user->locale,
                    'town' => $user->town,
                    'country' => $user->country,
                ]),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Profile updated successfully.'),
                'user' => $user,
            ]);
        }

        $response = redirect()->back()->with('success', __('Profile updated successfully.'));
        if (!empty($validated['locale'])) {
            $response->withCookie(cookie()->forever('akmart_locale', $validated['locale']));
        }
        return $response;
    }

    /**
     * Upload and update profile photo.
     */
    public function updatePhoto(Request $request)
    {
        $request->validate([
            'photo' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:2048'],
        ], [
            'photo.required' => __('Please select an image to upload.'),
            'photo.image' => __('The file must be a valid image.'),
            'photo.mimes' => __('Allowed image formats: JPG, PNG, WEBP, GIF.'),
            'photo.max' => __('Profile photo must not exceed 2MB in size.'),
        ]);

        $user = $request->user();

        // Delete previous photo if stored locally
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $file = $request->file('photo');
        $filename = 'profile_' . $user->id . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('profile-photos', $filename, 'public');

        $user->profile_photo_path = $path;
        $user->save();

        // Audit log
        try {
            AuditLog::create([
                'user_id' => $user->id,
                'event' => 'profile_photo_updated',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        $newUrl = asset('storage/' . $path);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Profile photo updated successfully.'),
                'photo_url' => $newUrl,
            ]);
        }

        return redirect()->back()->with('success', __('Profile photo updated successfully.'));
    }

    /**
     * Remove profile photo.
     */
    public function removePhoto(Request $request)
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->profile_photo_path = null;
            $user->save();

            try {
                AuditLog::create([
                    'user_id' => $user->id,
                    'event' => 'profile_photo_removed',
                    'auditable_type' => User::class,
                    'auditable_id' => $user->id,
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Profile photo removed successfully.'),
                'photo_url' => $user->profile_photo_url,
            ]);
        }

        return redirect()->back()->with('success', __('Profile photo removed successfully.'));
    }

    /**
     * Display Security settings page.
     */
    public function security(Request $request)
    {
        $user = $request->user();
        return view('content.pages.pages-account-settings-security', compact('user'));
    }

    /**
     * Change user password.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        if (!Hash::check($validated['currentPassword'], $user->password)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'errors' => ['currentPassword' => [__('The current password provided is incorrect.')]],
                ], 422);
            }
            return redirect()->back()->withErrors(['currentPassword' => __('The current password provided is incorrect.')]);
        }

        $user->password = Hash::make($validated['newPassword']);
        $user->save();

        try {
            AuditLog::create([
                'user_id' => $user->id,
                'event' => 'password_changed',
                'auditable_type' => User::class,
                'auditable_id' => $user->id,
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Password changed successfully.'),
            ]);
        }

        return redirect()->back()->with('success', __('Password changed successfully.'));
    }

    /**
     * Display Notifications settings page.
     */
    public function notifications(Request $request)
    {
        $user = $request->user();
        return view('content.pages.pages-account-settings-notifications', compact('user'));
    }

    /**
     * Save notification preferences.
     */
    public function updateNotifications(Request $request)
    {
        $user = $request->user();
        $user->marketing_opt_out = $request->boolean('marketing_opt_out');
        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Notification preferences updated successfully.'),
            ]);
        }

        return redirect()->back()->with('success', __('Notification preferences updated successfully.'));
    }
}
