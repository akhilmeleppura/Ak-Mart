<?php

use Illuminate\Support\Facades\Route;
use Modules\General\App\Http\Controllers\GeneralController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('generals', GeneralController::class)->names('general');
});
