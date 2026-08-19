<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginBasic extends Controller
{
  public function index()
  {
    if (Auth::check()) {
      return redirect()->route('dashboard');
    }
    $pageConfigs = ['myLayout' => 'blank'];
    return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
  }

  public function store(Request $request)
  {
    $credentials = $request->validate([
      'email' => ['required', 'string'],
      'password' => ['required', 'string'],
    ]);

    $fieldType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

    if (Auth::attempt([$fieldType => $credentials['email'], 'password' => $credentials['password']], $request->boolean('remember'))) {
      $request->session()->regenerate();
      return redirect()->intended(route('dashboard'));
    }

    return back()->withErrors([
      'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
  }

  public function logout(Request $request)
  {
    Auth::guard('web')->logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('auth-login-basic')->with('success', __('You have been successfully logged out.'));
  }
}
