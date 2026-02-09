<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductManagement\ProductManagementController;

/*
|--------------------------------------------------------------------------
| Product Management Routes
|--------------------------------------------------------------------------
*/

Route::group(['middleware' => ['auth']], function () {
    
    // Product Management Routes
    Route::prefix('product-management')->name('product-management.')->group(function () {
        
        // Main product routes
        Route::get('/', [ProductManagementController::class, 'index'])->name('index');
        Route::get('/create', [ProductManagementController::class, 'create'])->name('create');
        Route::post('/store', [ProductManagementController::class, 'store'])->name('store');
        Route::get('/show/{id}', [ProductManagementController::class, 'show'])->name('show');
        Route::get('/pdf/{id}', [ProductManagementController::class, 'generatePDF'])->name('pdf');
        Route::get('/edit/{id}', [ProductManagementController::class, 'edit'])->name('edit');
        Route::put('/update/{id}', [ProductManagementController::class, 'update'])->name('update');
        Route::delete('/delete/{id}', [ProductManagementController::class, 'destroy'])->name('destroy');
        
         // Filter routes
        Route::post('/apply-filters', [ProductManagementController::class, 'applyFilters'])->name('apply-filters');
        Route::post('/clear-filters', [ProductManagementController::class, 'clearFilters'])->name('clear-filters');
        
        // Product details routes
        Route::get('/{productId}/unit-prices', [ProductManagementController::class, 'getUnitPrices'])->name('unit-prices');
        Route::get('/{productId}/variant-stocks', [ProductManagementController::class, 'getVariantStocks'])->name('variant-stocks');
        
        // AJAX routes for dynamic data
        Route::get('/get-subcategories/{categoryId}', [ProductManagementController::class, 'getSubcategories'])->name('get-subcategories');
        Route::get('/get-child-categories/{subcategoryId}', [ProductManagementController::class, 'getChildCategories'])->name('get-child-categories');
        Route::get('/get-models/{brandId}', [ProductManagementController::class, 'getModelsByBrand'])->name('get-models');
        Route::get('/get-variant-groups', [ProductManagementController::class, 'getVariantGroups'])->name('get-variant-groups');
        Route::get('/get-variant-group-keys/{groupId}', [ProductManagementController::class, 'getVariantGroupKeys'])->name('get-variant-group-keys');
        
        // Product details AJAX
        Route::get('/{productId}/data', [ProductManagementController::class, 'getProductData'])->name('get-product-data');
        Route::get('/{productId}/unit-prices', [ProductManagementController::class, 'getUnitPrices'])->name('unit-prices');
        Route::get('/{productId}/variant-stocks', [ProductManagementController::class, 'getVariantStocks'])->name('variant-stocks');
        
        // Bulk actions
        Route::get('/search-products', [ProductManagementController::class, 'searchProducts'])->name('search-products');
        Route::post('/bulk-delete', [ProductManagementController::class, 'bulkDelete'])->name('bulk-delete');
        Route::post('/bulk-status-update', [ProductManagementController::class, 'bulkStatusUpdate'])->name('bulk-status-update');
        Route::post('/check-slug', [ProductManagementController::class, 'checkSlug'])->name('check-slug');

        // Category management
        Route::post('/categories/store', [ProductManagementController::class, 'storeCategory'])->name('categories.store');
        Route::post('/subcategories/store', [ProductManagementController::class, 'storeSubcategory'])->name('subcategories.store');
        Route::post('/child-categories/store', [ProductManagementController::class, 'storeChildCategory'])->name('child-categories.store');
        
        // Brand management
        Route::post('/brands/store', [ProductManagementController::class, 'storeBrand'])->name('brands.store');
        
        // Model management
        Route::post('/models/store', [ProductManagementController::class, 'storeModel'])->name('models.store');
        
        // Unit management
        Route::post('/units/store', [ProductManagementController::class, 'storeUnit'])->name('units.store');
        
        // API Sync
    });
    
});
Route::get('/sync-categories-from-api', [ProductManagementController::class, 'syncCategoriesFromApi'])->name('sync-categories-from-api');
Route::get('/sync-brands-from-api', [ProductManagementController::class, 'syncBrandsFromApi'])->name('sync-brands-from-api');
Route::get('/sync-customers-from-api', [ProductManagementController::class, 'syncCustomersFromApi'])->name('sync-customers-from-api');
Route::get('/sync-from-api', [ProductManagementController::class, 'syncProductsFromApi'])->name('sync-from-api');

