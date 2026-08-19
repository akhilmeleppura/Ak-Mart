<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\apps\WorkflowAutomationController;

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/automation/rules', [WorkflowAutomationController::class, 'index'])->name('app-automation-rules');
    Route::post('/automation/rules', [WorkflowAutomationController::class, 'store'])->name('app-automation-rules-store');
    Route::post('/automation/rules/{id}/toggle', [WorkflowAutomationController::class, 'toggle'])->name('app-automation-rules-toggle');
    Route::delete('/automation/rules/{id}', [WorkflowAutomationController::class, 'destroy'])->name('app-automation-rules-destroy');
});