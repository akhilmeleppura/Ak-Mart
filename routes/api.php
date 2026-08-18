<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\StorefrontController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/payment/webhook', [\App\Http\Controllers\apps\PaymentWebhookController::class, 'handle']);

// Storefront RESTful API v1
Route::prefix('v1')->group(function () {
    Route::get('/products', [StorefrontController::class, 'products']);
    Route::get('/products/{id}', [StorefrontController::class, 'productDetails']);
    Route::get('/categories', [StorefrontController::class, 'categories']);
    Route::post('/orders', [StorefrontController::class, 'placeOrder']);
    Route::post('/checkout', [StorefrontController::class, 'placeOrder']);
    Route::get('/orders/{orderNumber}', [StorefrontController::class, 'getOrder']);
    Route::post('/coupons/validate', [StorefrontController::class, 'validateCoupon']);
});
