<?php

use Illuminate\Support\Facades\Route;
use Modules\SampleModule\Http\Controllers\SampleModuleController;

Route::middleware(['auth:sanctum'])->prefix('v1')->group(function () {
    Route::apiResource('samplemodules', SampleModuleController::class)->names('samplemodule');
});
