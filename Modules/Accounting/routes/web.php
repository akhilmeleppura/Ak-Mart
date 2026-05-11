<?php

use Illuminate\Support\Facades\Route;
use Modules\Accounting\Http\Controllers\AccountingController;
use Modules\Accounting\App\Http\Controllers\SubCategoryController;
use Modules\Accounting\App\Http\Controllers\PrefixController;
use Modules\Accounting\App\Http\Controllers\Journal\JournalEntriesController;
use Modules\Accounting\App\Http\Controllers\Ledger\LedgerController;
use Modules\Accounting\Http\Controllers\TrialBalance\TrialBalanceController;
use Modules\Accounting\App\Http\Controllers\CustomerLedger\CustomerLedgerController;




Route::middleware(['auth', 'check.permission'])->prefix('accounting')->group(function () {
   // Route::prefix('accounting')->group(function () {
   Route::get('/chart-of-accounts', [AccountingController::class, 'index'])->name('accounting.index');
   //   Route::get('/charts-of-account', [AccountingController::class, 'chartofaccounts'])->name('index');
   // Route::get('/prefix', [AccountingController::class, 'prefix'])->name('index');
   Route::post('/chart/store', [AccountingController::class, 'store'])->name('accounting.chart.store');

   Route::get('/subcategory', [SubCategoryController::class, 'index'])->name('subcategory.index');
   Route::post('/subcategory/store', [SubCategoryController::class, 'store'])->name('accounting.subcategory.store');

   Route::get('/prefix', [PrefixController::class, 'index'])->name('accounting.prefix.index');
   Route::post('/prefix', [PrefixController::class, 'store'])->name('accounting.prefix.store');

   Route::prefix('journal')->group(function () {

      Route::get('/', [JournalEntriesController::class, 'index'])->name('accounting.journal.index');
      Route::get('/create', [JournalEntriesController::class, 'create'])->name('accounting.journal.create');
      Route::post('/', [JournalEntriesController::class, 'store'])->name('accounting.journal.store');
      Route::get('/{journal}/view', [JournalEntriesController::class, 'show'])->name('accounting.journal.view');
      Route::get('/{journal}/edit', [JournalEntriesController::class, 'edit'])->name('accounting.journal.edit');
      Route::put('/{journal}', [JournalEntriesController::class, 'update'])->name('accounting.journal.update');
      Route::delete('/{journal}', [JournalEntriesController::class, 'destroy'])->name('accounting.journal.destroy');
   });

   //  Route::prefix('ledger')->group(function () {
   //  Route::get('/', [LedgerController::class, 'index'])->name('accounting.ledger.index');

   //  });

});

Route::get('accounting/journal/entries-list', [JournalEntriesController::class, 'list'])->name('accounting.journal.entriesList');

Route::prefix('accounting')->group(function () {

   Route::get('ledger', [LedgerController::class, 'index'])->name('accounting.ledger.index');
   Route::get('ledger/create', [LedgerController::class, 'create'])->name('accounting.ledger.create');
   Route::get('ledger/{ledger}/edit', [LedgerController::class, 'edit'])->name('accounting.ledger.edit');
   Route::delete('ledger/{ledger}', [LedgerController::class, 'destroy'])->name('accounting.ledger.destroy');
});
Route::get('accounting/ledger/entries-list', [LedgerController::class, 'entriesList'])->name('accounting.ledger.entriesList');
// Route::get('/accounting/ledger/{id}/view', [LedgerController::class, 'showLedgerDetails'])->name('accounting.ledger.view');
// Route::get('/accounting/ledger/{id}/view', [LedgerController::class, 'details'])->name('accounting.ledger.view');
Route::get('/accounting/trial-balance', [TrialBalanceController::class, 'index'])->name('accounting.trial-balance.index');
Route::get('/accounting/trial-balance/export-pdf', [TrialBalanceController::class, 'exportPdf'])->name('accounting.trial-balance.export-pdf');
// Route::get('/accounting/ledger/customer/{id}', [LedgerController::class, 'customerDetails'])->name('accounting.ledger.customerDetails');
// Route::get('accounting/journal/create', [JournalEntriesController::class, 'create'])->name('accounting.journal.create');
Route::post('/journal', [JournalEntriesController::class, 'store'])->name('accoutnig.journal.store');

 Route::get('/accounting/ledger/{id}/view', [LedgerController::class, 'details'])->name('accounting.ledger.details');
 Route::get('/accounting/ledger/customer/{id}', [LedgerController::class, 'customerDetails'])->name('accounting.ledger.customerDetails');


 // Customer Ledger Routes
Route::prefix('accounting/customer-ledger')->name('accounting.customer-ledger.')->group(function () {
    Route::get('/', [CustomerLedgerController::class, 'index'])->name('index');
    Route::get('/entriesList', [CustomerLedgerController::class, 'entriesList'])->name('entriesList');
    Route::get('/{id}/view', [CustomerLedgerController::class, 'details'])->name('details');
    Route::get('/create', [CustomerLedgerController::class, 'create'])->name('create');
    Route::post('/', [CustomerLedgerController::class, 'store'])->name('store');
    Route::get('/{id}', [CustomerLedgerController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [CustomerLedgerController::class, 'edit'])->name('edit');
    Route::put('/{id}', [CustomerLedgerController::class, 'update'])->name('update');
    Route::delete('/{id}', [CustomerLedgerController::class, 'destroy'])->name('destroy');
        Route::get('/invoice-preview/{id}', [CustomerLedgerController::class, 'invoicePreview'])->name('invoice.preview');
    Route::get('/debit-note-preview/{id}', [CustomerLedgerController::class, 'debitNotePreview'])->name('debit-note.preview');
    Route::get('/credit-note-preview/{id}', [CustomerLedgerController::class, 'creditNotePreview'])->name('credit-note.preview');
});