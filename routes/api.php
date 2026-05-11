<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\StorefrontController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/payment/webhook', [\App\Http\Controllers\apps\PaymentWebhookController::class, 'handle']);

// Storefront API v1
Route::prefix('v1')->group(function () {
    Route::get('/categories', [StorefrontController::class, 'categories']);
    Route::get('/products', [StorefrontController::class, 'products']);
    Route::get('/products/{id}', [StorefrontController::class, 'productDetails']);
});
