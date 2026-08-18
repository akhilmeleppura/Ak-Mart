<?php

namespace App\Http\Controllers\language;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
  protected array $supportedLocales = ['en', 'ml', 'hi', 'ta', 'kn', 'ar', 'fr', 'de', 'it'];

  public function swap(Request $request, $locale)
  {
    if (!in_array($locale, $this->supportedLocales)) {
      $locale = 'en';
    }

    $request->session()->put('locale', $locale);
    $request->session()->save();
    App::setLocale($locale);

    return redirect()->back();
  }
}