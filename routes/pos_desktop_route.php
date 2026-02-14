<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Pos\DesktopPosController;

/*
|--------------------------------------------------------------------------
| Desktop POS Routes
|--------------------------------------------------------------------------
|
| All routes for the desktop POS single-page screen. These are protected
| by the standard auth middleware and live inside the dashboard layout.
|
*/

Route::group(['middleware' => ['auth']], function () {
    // POS desktop main page
    Route::get('/pos/desktop', [DesktopPosController::class, 'index'])->name('pos.desktop.index');
    Route::get('/pos/desktop/target-stats', [DesktopPosController::class, 'targetStats'])->name('pos.desktop.target-stats');
    Route::get('/pos/mobile', [DesktopPosController::class, 'mobile'])->name('pos.desktop.mobile');

    // Product & category data
    Route::get('/pos/desktop/categories', [DesktopPosController::class, 'categories'])->name('pos.desktop.categories');
    Route::get('/pos/categories', [DesktopPosController::class, 'nestedCategories'])->name('pos.categories');
    Route::get('/pos/desktop/search', [DesktopPosController::class, 'searchProducts'])->name('pos.desktop.search');
    Route::get('/pos/desktop/products', [DesktopPosController::class, 'productsByCategory'])->name('pos.desktop.products');
    Route::post('/pos/desktop/products-by-barcode', [DesktopPosController::class, 'productsByBarcode'])->name('pos.desktop.products-by-barcode');

    // Barcode + cart
    Route::post('/pos/desktop/barcode-lookup', [DesktopPosController::class, 'barcodeLookup'])->name('pos.desktop.barcode');
    Route::post('/pos/desktop/add-to-cart', [DesktopPosController::class, 'addToCart'])->name('pos.desktop.add-to-cart');

    // Hold orders
    Route::post('/pos/desktop/hold', [DesktopPosController::class, 'holdOrder'])->name('pos.desktop.hold');
    Route::get('/pos/desktop/hold/{id}', [DesktopPosController::class, 'getHold'])->name('pos.desktop.get-hold');
    Route::get('/pos/desktop/holds', [DesktopPosController::class, 'listHolds'])->name('pos.desktop.holds');

    // Customer + coupon + totals
    Route::get('/pos/desktop/customers', [DesktopPosController::class, 'searchCustomer'])->name('pos.desktop.customer.search');
    Route::post('/pos/desktop/customers/create', [DesktopPosController::class, 'createCustomer'])->name('pos.desktop.customer.create');
    Route::post('/pos/desktop/customers/update', [DesktopPosController::class, 'createCustomer'])->name('pos.desktop.customer.update');
    Route::post('/pos/desktop/customers/delete', [DesktopPosController::class, 'deleteCustomer'])->name('pos.desktop.customer.delete');

    Route::post('/pos/desktop/apply-coupon', [DesktopPosController::class, 'applyCoupon'])->name('pos.desktop.apply-coupon');
    Route::post('/pos/desktop/calculate-totals', [DesktopPosController::class, 'calculateTotals'])->name('pos.desktop.calculate-totals');
    Route::post('/pos/desktop/calculate-totals', [DesktopPosController::class, 'calculateTotals'])->name('pos.desktop.calculate-totals');
    
    // Payment methods
    Route::get('/pos/get-payment-methods', [DesktopPosController::class, 'getPaymentMethods'])->name('pos.get-payment-methods');

    // Checkout + printing
    Route::post('/pos/desktop/create-order', [DesktopPosController::class, 'createOrder'])->name('pos.desktop.create-order');
    Route::post('/pos/desktop/preview', [DesktopPosController::class, 'preview'])->name('pos.desktop.preview');
    Route::get('/pos/desktop/print/{slug}', [DesktopPosController::class, 'print'])->name('pos.desktop.print');

    Route::get('/pos/desktop/curstomer-source', [DesktopPosController::class, 'customerSource'])->name('pos.desktop.customer-source');
    Route::get('/pos/desktop/delivery-methods', [DesktopPosController::class, 'deliveryMethods'])->name('pos.desktop.delivery-methods');
    Route::get('/pos/desktop/outlets', [DesktopPosController::class, 'outlets'])->name('pos.desktop.outlets');
    Route::get('/pos/desktop/courier-methods', [DesktopPosController::class, 'courierMethods'])->name('pos.desktop.courier-methods');
});


