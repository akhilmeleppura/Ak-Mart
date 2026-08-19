<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apps\SaaS\SubscriptionController;
use App\Http\Controllers\apps\SaaS\KycAdminController;
use App\Http\Controllers\apps\SaaS\PlatformAnalyticsController;
use App\Http\Controllers\apps\SaaS\CommissionController;
use App\Http\Controllers\apps\SaaS\CurrencyController;
use App\Http\Controllers\apps\SaaS\LanguageController;
use App\Http\Controllers\apps\SaaS\SeoController;
use App\Http\Controllers\apps\SaaS\DunningController;

Route::middleware(['web', 'auth'])->prefix('saas')->group(function () {
    Route::get('/analytics', [PlatformAnalyticsController::class, 'index'])->name('app-saas-analytics');
    Route::get('/commissions', [CommissionController::class, 'index'])->name('app-saas-commissions');
    Route::post('/commissions/rule', [CommissionController::class, 'storeRule'])->name('app-saas-commissions-rule');
    Route::post('/commissions/tier', [CommissionController::class, 'storeTier'])->name('app-saas-commissions-tier');
    Route::get('/currencies', [CurrencyController::class, 'index'])->name('app-saas-currencies');
    Route::post('/currencies', [CurrencyController::class, 'store'])->name('app-saas-currencies-store');
    Route::get('/languages', [LanguageController::class, 'index'])->name('app-saas-languages');
    Route::post('/languages', [LanguageController::class, 'store'])->name('app-saas-languages-store');
    Route::get('/seo', [SeoController::class, 'index'])->name('app-saas-seo');
    Route::get('/kyc', [KycAdminController::class, 'index'])->name('app-saas-kyc-admin');
    Route::get('/kyc-admin', [KycAdminController::class, 'index']);
    Route::get('/kyc/{vendorKyc}', [KycAdminController::class, 'show'])->name('app-saas-kyc-show');
    Route::post('/kyc/{vendorKyc}/approve', [KycAdminController::class, 'approve'])->name('app-saas-kyc-approve');
    Route::post('/kyc/{vendorKyc}/reject', [KycAdminController::class, 'reject'])->name('app-saas-kyc-reject');
    Route::post('/kyc/{vendorKyc}/review', [KycAdminController::class, 'markUnderReview'])->name('app-saas-kyc-review');
    Route::get('/dunning', [DunningController::class, 'index'])->name('app-saas-dunning');
});