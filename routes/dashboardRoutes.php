<?php

use App\Http\Middleware\DemoMode;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckUserType;
use App\Http\Controllers\Analytics\HomePageAnalytics;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CkeditorController;
use App\Http\Controllers\ProductController;



Route::middleware([CheckUserType::class, DemoMode::class])->group(function(){
   
   //Dashboard routes
    Route::get('/', [HomePageAnalytics::class, 'index'])->name('home');
    Route::get('/home', [HomePageAnalytics::class, 'index']);
    Route::get('/home/analytics/data', [HomePageAnalytics::class, 'summary'])->name('home.analytics');
    Route::get('/crm-home', [HomeController::class, 'crm_index'])->name('crm.home');
    Route::get('/accounts-home', [HomeController::class, 'accounts_index'])->name('accounts.home');
    Route::get('/inventory-home', [HomeController::class, 'inventory_dashboard'])->name('inventory.home');
    Route::get('/inventory/products/{identifier}/barcode-data', [ProductController::class, 'getBarcodeData'])->name('inventory.products.barcode-data');
});