<?php

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    protected array $supportedLocales = ['en', 'ml', 'hi', 'ar', 'fr', 'de', 'ta', 'kn', 'it'];

    public function swap(Request $request, $locale)
    {
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = 'en';
        }

        // 1. Session Storage
        $request->session()->put('locale', $locale);
        $request->session()->save();

        // 2. Authenticated User DB Persistence
        if ($request->user()) {
            $user = $request->user();
            $user->locale = $locale;
            $user->save();
        }

        // 3. Application Locale
        App::setLocale($locale);

        // 4. Return with long-lived cookie & redirect
        return redirect()->back()->withCookie(cookie()->forever('akmart_locale', $locale));
    }
}