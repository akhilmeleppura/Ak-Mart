<?php

use Illuminate\Support\Facades\Route;
use Modules\General\App\Http\Controllers\GeneralController;
use Modules\General\App\Http\Controllers\DocumentTemplate\DocumentTemplateController;
use Modules\General\App\Http\Controllers\Customer\CustomerController;
use Modules\General\App\Http\Controllers\Customer\CustomerTypeController;


Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('generals', GeneralController::class)->names('general');
});
Route::prefix('general/document/templates')->group(function () {
    Route::get('/', [DocumentTemplateController::class, 'index'])->name('general.templates.index');
    Route::get('/create', [DocumentTemplateController::class, 'create'])->name('general.templates.create');
    Route::post('/store', [DocumentTemplateController::class, 'store'])->name('general.templates.store');
    Route::get('/{uuid}/edit', [DocumentTemplateController::class, 'edit'])->name('general.templates.edit');
    Route::put('/{uuid}', [DocumentTemplateController::class, 'update'])->name('general.templates.update');
    Route::delete('/{uuid}', [DocumentTemplateController::class, 'destroy'])->name('general.templates.destroy');
    Route::patch('/{uuid}/status', [DocumentTemplateController::class, 'toggleStatus'])->name('general.templates.toggle-status');
});
Route::prefix('general')->group(function () {
    // Customer Routes
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::patch('/customers/{customer}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('customers.toggle-status');

    // Customer Type Routes (unchanged)
    Route::get('/customer-types', [CustomerTypeController::class, 'index'])->name('customer-types.index');
    Route::get('/customer-types/create', [CustomerTypeController::class, 'create'])->name('customer-types.create');
    Route::post('/customer-types', [CustomerTypeController::class, 'store'])->name('customer-types.store');
    Route::get('/customer-types/{customerType}', [CustomerTypeController::class, 'show'])->name('customer-types.show');
    Route::get('/customer-types/{customerType}/edit', [CustomerTypeController::class, 'edit'])->name('customer-types.edit');
    Route::put('/customer-types/{customerType}', [CustomerTypeController::class, 'update'])->name('customer-types.update');
    Route::delete('/customer-types/{customerType}', [CustomerTypeController::class, 'destroy'])->name('customer-types.destroy');
});
