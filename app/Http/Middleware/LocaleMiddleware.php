<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
    /**
     * Supported application locales.
     */
    protected array $supportedLocales = ['en', 'ml', 'hi', 'ar', 'fr', 'de', 'ta', 'kn', 'it'];

    /**
     * Handle an incoming request with multi-layer locale detection.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = null;

        // 1. Session Preference (Active user switch)
        if ($request->session()->has('locale') && in_array($request->session()->get('locale'), $this->supportedLocales)) {
            $locale = $request->session()->get('locale');
        }

        // 2. Authenticated User Database Preference (When session is fresh)
        if (!$locale && $request->user() && !empty($request->user()->locale) && in_array($request->user()->locale, $this->supportedLocales)) {
            $locale = $request->user()->locale;
        }

        // 3. Cookie Preference
        if (!$locale && $request->hasCookie('akmart_locale') && in_array($request->cookie('akmart_locale'), $this->supportedLocales)) {
            $locale = $request->cookie('akmart_locale');
        }

        // 4. Browser Preferred Language
        if (!$locale) {
            $browserPreferred = $request->getPreferredLanguage($this->supportedLocales);
            if ($browserPreferred && in_array($browserPreferred, $this->supportedLocales)) {
                $locale = $browserPreferred;
            }
        }

        // 5. Default Fallback
        if (!$locale || !in_array($locale, $this->supportedLocales)) {
            $locale = config('app.locale', 'en');
        }

        // Set application locale globally
        App::setLocale($locale);

        // Keep session in sync
        if ($request->session()->get('locale') !== $locale) {
            $request->session()->put('locale', $locale);
        }

        $response = $next($request);

        // Ensure persistent cookie reflects current active locale
        $finalLocale = App::getLocale();
        if (method_exists($response, 'withCookie')) {
            $response->withCookie(cookie()->forever('akmart_locale', $finalLocale));
        }

        return $response;
    }
}
