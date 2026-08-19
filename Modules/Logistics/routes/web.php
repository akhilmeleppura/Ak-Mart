<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apps\Logistics\ShippingMethodController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/logistics/shipping', [ShippingMethodController::class, 'index'])->name('app-logistics-shipping');
    Route::post('/logistics/shipping', [ShippingMethodController::class, 'store'])->name('app-logistics-shipping-store');
    Route::post('/logistics/shipping/{method}/toggle', [ShippingMethodController::class, 'toggle'])->name('app-logistics-shipping-toggle');
    Route::delete('/logistics/shipping/{method}', [ShippingMethodController::class, 'destroy'])->name('app-logistics-shipping-destroy');
});