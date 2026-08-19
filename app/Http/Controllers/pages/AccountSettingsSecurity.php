<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountSettingsSecurity extends Controller
{
    public function index(Request $request)
    {
        return app(ProfileController::class)->security($request);
    }
}
