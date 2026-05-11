<?php

use Illuminate\Support\Facades\Route;
use Modules\Permission\App\Http\Controllers\PermissionController;
use Modules\Permission\App\Http\Controllers\RoleController;


Route::middleware(['auth', 'verified'])->group(function () {
    // Route::resource('permissions', PermissionController::class)->names('permission');
});

Route::middleware(['auth', 'check.permission'])->group(function () {

Route::get('/roles', [RoleController::class, 'index'])->name('role.view');
Route::get('/roles/create', [RoleController::class, 'create'])->name('role.create');
Route::get('/roles/{role}', [RoleController::class, 'show'])->name('role.show');
Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('role.edit');
});

Route::post('/roles', [RoleController::class, 'store'])->name('role.store');
Route::put('/roles/{role}', [RoleController::class, 'update'])->name('role.update');
Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('role.destroy');



Route::prefix('permissions')->name('permissions.')->group(function () {
    Route::get('/', [PermissionController::class, 'index'])->name('index');
    Route::get('/create', [PermissionController::class, 'create'])->name('create');
    Route::post('/assign', [PermissionController::class, 'store'])->name('assign'); // <-- ADD THIS
    // Route::post('/', [PermissionController::class, 'store'])->name('store');
    Route::get('/{id}/show', [PermissionController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [PermissionController::class, 'edit'])->name('edit');
    Route::post('/{id}/update', [PermissionController::class, 'update'])->name('update');
    Route::delete('/{id}/destroy', [PermissionController::class, 'destroy'])->name('destroy');
});
