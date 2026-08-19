<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apps\Vendor\KycController;
use App\Http\Controllers\apps\Vendor\InventoryController;
use App\Http\Controllers\apps\Vendor\ReturnRequestController;
use App\Http\Controllers\apps\Vendor\PosController;
use App\Http\Controllers\apps\Vendor\WalletController;
use App\Http\Controllers\apps\Vendor\StoreBuilderController;
use App\Http\Controllers\apps\Vendor\SupportTicketController;
use App\Http\Controllers\apps\Vendor\PaymentSettingsController;

Route::middleware(['web', 'auth'])->prefix('vendor')->group(function () {
    Route::get('/kyc', [KycController::class, 'index'])->name('app-vendor-kyc');
    Route::post('/kyc', [KycController::class, 'store'])->name('app-vendor-kyc-store');
    Route::get('/inventory', [InventoryController::class, 'index'])->name('app-vendor-inventory');
    Route::post('/inventory/update', [InventoryController::class, 'updateStock'])->name('app-vendor-inventory-update');
    Route::get('/returns', [ReturnRequestController::class, 'index'])->name('app-vendor-returns');
    Route::post('/returns/{returnRequest}', [ReturnRequestController::class, 'updateStatus'])->name('app-vendor-returns-update');
    Route::get('/pos', [PosController::class, 'index'])->name('app-vendor-pos');
    Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('app-vendor-pos-checkout');
    Route::get('/wallet', [WalletController::class, 'index'])->name('app-vendor-wallet');
    Route::post('/wallet/payout', [WalletController::class, 'requestPayout'])->name('app-vendor-wallet-payout');
    Route::get('/store-builder', [StoreBuilderController::class, 'index'])->name('app-vendor-store-builder');
    Route::post('/store-builder', [StoreBuilderController::class, 'store'])->name('app-vendor-store-builder-save');
    Route::get('/support', [SupportTicketController::class, 'index'])->name('app-vendor-support');
    Route::get('/support-tickets', [SupportTicketController::class, 'index']);
    Route::get('/support/{ticket}', [SupportTicketController::class, 'show'])->name('app-vendor-support-show');
    Route::post('/support/{ticket}/reply', [SupportTicketController::class, 'reply'])->name('app-vendor-support-reply');
    Route::post('/support/{ticket}/status', [SupportTicketController::class, 'updateStatus'])->name('app-vendor-support-status');
});