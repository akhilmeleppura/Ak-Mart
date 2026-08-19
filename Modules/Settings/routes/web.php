<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apps\SettingsHubController;
use App\Http\Controllers\apps\EcommerceSettingsBranding;
use App\Http\Controllers\apps\MapsSettingsController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/settings/store', [SettingsHubController::class, 'showSection'])->defaults('section', 'store')->name('app-ecommerce-settings-details');
    Route::post('/settings/store/save', [SettingsHubController::class, 'saveSection'])->defaults('section', 'store')->name('app-ecommerce-settings-store-save');
    Route::get('/settings/branding', [EcommerceSettingsBranding::class, 'index'])->name('app-ecommerce-settings-branding');
    Route::post('/settings/branding/save', [EcommerceSettingsBranding::class, 'saveSettings'])->name('app-ecommerce-settings-branding-save');
    Route::get('/settings/{section}', [SettingsHubController::class, 'showSection'])->name('settings.section');
    Route::post('/settings/{section}/save', [SettingsHubController::class, 'saveSection'])->name('settings.section.save');
    Route::post('/settings/test-smtp', [SettingsHubController::class, 'testSmtp'])->name('settings.test-smtp');
    Route::post('/settings/test-whatsapp', [SettingsHubController::class, 'testWhatsApp'])->name('settings.test-whatsapp');
    Route::post('/settings/maps/save', [MapsSettingsController::class, 'store'])->name('app-ecommerce-settings-maps-save');
});