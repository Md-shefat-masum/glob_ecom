<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StockManagement\StockAdjustmentController;

Route::middleware(['auth'])->prefix('stock-adjustment')->name('stock-adjustment.')->group(function () {
    // Index - List all stock logs
    Route::get('/', [StockAdjustmentController::class, 'index'])->name('index');
    
    // Create - Show create form
    Route::get('/create', [StockAdjustmentController::class, 'create'])->name('create');
    
    // Store - Save stock adjustment
    Route::post('/store', [StockAdjustmentController::class, 'store'])->name('store');
    
    // Ajax routes
    Route::get('/search-products', [StockAdjustmentController::class, 'searchProducts'])->name('search-products');
    Route::get('/product/{id}', [StockAdjustmentController::class, 'getProductDetails'])->name('product-details');
});

