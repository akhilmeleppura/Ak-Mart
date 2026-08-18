<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocaleMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
   */
  protected array $supportedLocales = ['en', 'ml', 'hi', 'ta', 'kn', 'ar', 'fr', 'de', 'it'];

  public function handle(Request $request, Closure $next): Response
  {
    if (session()->has('locale') && in_array(session()->get('locale'), $this->supportedLocales)) {
      app()->setLocale(session()->get('locale'));
    }

    return $next($request);
  }
}
