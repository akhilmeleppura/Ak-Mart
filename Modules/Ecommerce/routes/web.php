<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ecommerce Module Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['web', 'auth', 'tenant.subscription'])->group(function () {
    // Ecommerce Products, Orders, Customers, Coupons, Reviews, Branches routes
});
