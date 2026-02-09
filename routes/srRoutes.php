<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SrManagement\SalesTargetController;

/*
|--------------------------------------------------------------------------
| SR Management - Sales Targets
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('sales-targets')->name('sales_targets.')->group(function () {
    Route::get('/', [SalesTargetController::class, 'index'])->name('index');
    Route::get('/analytics', [SalesTargetController::class, 'analytics'])->name('analytics');
    Route::get('/users-list', [SalesTargetController::class, 'usersList'])->name('users_list');
    Route::get('/create', [SalesTargetController::class, 'create'])->name('create');
    Route::post('/store', [SalesTargetController::class, 'store'])->name('store');
    Route::get('/{id}/edit', [SalesTargetController::class, 'edit'])->name('edit');
    Route::get('/{id}', [SalesTargetController::class, 'show'])->name('show');
    Route::post('/update', [SalesTargetController::class, 'update'])->name('update');
});
