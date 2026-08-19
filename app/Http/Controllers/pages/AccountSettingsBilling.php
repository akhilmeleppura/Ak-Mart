<?php

namespace App\Http\Controllers\pages;

use App\Http\Controllers\apps\SaaS\SubscriptionController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountSettingsBilling extends Controller
{
    public function index(Request $request)
    {
        return app(SubscriptionController::class)->index($request);
    }
}
