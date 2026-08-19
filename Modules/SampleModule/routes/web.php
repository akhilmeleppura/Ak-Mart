<?php

use Illuminate\Support\Facades\Route;
use Modules\SampleModule\App\Http\Controllers\SampleModuleController;
use  Modules\SampleModule\App\Http\Controllers\Test\Test1Controller;
use Modules\SampleModule\App\Http\Controllers\Test\Test2Controller;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('samplemodules', SampleModuleController::class)->names('samplemodule');
});
 Route::get('/samplemodule', [SampleModuleController::class, 'index'])->name('index');
 Route::get('/samplemodule/sample-page-1', [Test1Controller::class, 'index'])->name('samplemodule.page1');
 Route::get('/samplemodule/sample-page-2', [Test2Controller::class, 'index'])->name('samplemodule.page2');
