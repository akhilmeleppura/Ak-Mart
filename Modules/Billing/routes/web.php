<?php

use Illuminate\Support\Facades\Route;
use Modules\Billing\App\Http\Controllers\BillingController;
use Modules\Billing\App\Http\Controllers\Invoices\InvoiceController;
use Modules\Billing\App\Http\Controllers\CreditNotes\CreditNoteController;
use Modules\Billing\App\Http\Controllers\DebitNotes\DebitNoteController;
use Modules\Billing\App\Http\Controllers\PaymentOptions\PaymentOptionController;

Route::middleware(['auth', 'verified'])->group(function () {
  Route::prefix('accounting/billings')->group(function () {
    // Main billing routes
    Route::get('/', [InvoiceController::class, 'index'])->name('accounting.billings.index');
    Route::get('/invoices/add', [InvoiceController::class, 'create'])->name('accounting.billings.create');
    Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('billing.invoices.edit');

    // Invoice routes
    Route::post('/store', [InvoiceController::class, 'store'])->name('billing.invoices.store');
    Route::get('/invoices/list', [InvoiceController::class, 'getInvoices'])->name('billing.invoices.index');
    Route::delete('invoices/{invoice}', [InvoiceController::class, 'destroy'])->name('billing.invoices.destroy');
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('billing.invoices.show');
    Route::get('/invoices/{id}/download', [InvoiceController::class, 'download'])->name('billing.invoices.download');
    Route::get('/invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('billing.invoices.print');
    Route::put('invoices/{id}', [InvoiceController::class, 'update'])->name('billing.invoices.update');
    Route::get('/billing/items/search', [InvoiceController::class, 'searchItems'])->name('billing.items.search');


    // Debit Note routes
    Route::get('/debit-notes', [DebitNoteController::class, 'index'])->name('billing.debit-notes.index');
    Route::get('/debit-notes/list', [DebitNoteController::class, 'getDebitNotes'])->name('billing.debit-notes.list');
    Route::get('/debit-notes/create', [DebitNoteController::class, 'create'])->name('billing.debit-notes.create');
    Route::post('/debit-notes', [DebitNoteController::class, 'store'])->name('billing.debit-notes.store');
    Route::get('/debit-notes/{id}', [DebitNoteController::class, 'show'])->name('billing.debit-notes.show');
    Route::get('/debit-notes/{id}/edit', [DebitNoteController::class, 'edit'])->name('billing.debit-notes.edit');
    Route::put('/debit-notes/{id}', [DebitNoteController::class, 'update'])->name('billing.debit-notes.update');
    Route::delete('/debit-notes/{id}', [DebitNoteController::class, 'destroy'])->name('billing.debit-notes.destroy');
    Route::get('/debit-notes/{id}/download', [DebitNoteController::class, 'download'])
      ->name('billing.debit-notes.download');

    Route::get('/debit-notes/{id}/print', [DebitNoteController::class, 'print'])
      ->name('billing.debit-notes.print');

    // Credit Note routes
    Route::get('/credit-notes', [CreditNoteController::class, 'index'])->name('billing.credit-notes.index');
    Route::get('/credit-notes/list', [CreditNoteController::class, 'getCreditNotes'])->name('billing.credit-notes.list');
    Route::get('/credit-notes/create', [CreditNoteController::class, 'create'])->name('billing.credit-notes.create');
    Route::post('/credit-notes', [CreditNoteController::class, 'store'])->name('billing.credit-notes.store');
    Route::get('/credit-notes/{id}', [CreditNoteController::class, 'show'])->name('billing.credit-notes.show');
    Route::get('/credit-notes/{id}/edit', [CreditNoteController::class, 'edit'])->name('billing.credit-notes.edit');
    Route::put('/credit-notes/{id}', [CreditNoteController::class, 'update'])->name('billing.credit-notes.update');
    Route::delete('/credit-notes/{id}', [CreditNoteController::class, 'destroy'])->name('billing.credit-notes.destroy');
    Route::get('/credit-notes/{id}/download', [CreditNoteController::class, 'download'])->name('billing.credit-notes.download');
    Route::get('/credit-notes/{id}/print', [CreditNoteController::class, 'print'])->name('billing.credit-notes.print');

    // Generic routes (moved to the end to avoid conflicts)
    Route::get('/{id}', [BillingController::class, 'show'])->name('billings.show');
    Route::get('invoices/{id}/edit', [BillingController::class, 'edit'])->name('billings.edit');
    Route::delete('/{id}', [BillingController::class, 'destroy'])->name('billings.destroy');

    // Route::get('/payments', [PaymentOptionController::class, 'index'])->name('billing.payment-options.index');
  });
});
    Route::get('/payment-options', [PaymentOptionController::class, 'index'])->name('billing.payment-options.index');
Route::put('/payment-options', [PaymentOptionController::class, 'update'])->name('payment-options.update');