<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Analytics\AnalyticsController;

/*
|--------------------------------------------------------------------------
| Analytics Routes
|--------------------------------------------------------------------------
|
| Here are all analytics and dashboard related routes
|
*/

Route::middleware(['auth'])->group(function () {
    
    // Analytics Dashboard
    Route::get('/analytics/dashboard', [AnalyticsController::class, 'index'])->name('analytics.dashboard');

    // API Endpoints for AJAX data loading
    Route::prefix('api/analytics')->group(function () {
        Route::get('/overview', [AnalyticsController::class, 'getOverview'])->name('api.analytics.overview');
        Route::get('/top-rated-products', [AnalyticsController::class, 'getTopRatedProducts'])->name('api.analytics.topRated');
        Route::get('/top-viewed-products', [AnalyticsController::class, 'getTopViewedProducts'])->name('api.analytics.topViewed');
        Route::get('/top-categories', [AnalyticsController::class, 'getTopCategories'])->name('api.analytics.topCategories');
        Route::get('/sales-chart', [AnalyticsController::class, 'getSalesChart'])->name('api.analytics.salesChart');
        Route::get('/category-distribution', [AnalyticsController::class, 'getCategoryDistribution'])->name('api.analytics.categoryDistribution');
        Route::get('/top-selling-products', [AnalyticsController::class, 'getTopSellingProducts'])->name('api.analytics.topSelling');
    });
});

