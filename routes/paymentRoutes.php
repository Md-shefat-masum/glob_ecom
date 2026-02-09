<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;


Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {
    // sslcommerz payment routes
    Route::get('sslcommerz/order/payment', [PaymentController::class, 'orderPayment'])->name('order.payment');
    Route::post('sslcommerz/success', [PaymentController::class, 'success'])->name('payment.success');
    Route::post('sslcommerz/failure', [PaymentController::class, 'failure'])->name('failure');
    Route::post('sslcommerz/cancel', [PaymentController::class, 'cancel'])->name('cancel');
    Route::post('sslcommerz/ipn', [PaymentController::class, 'ipn'])->name('payment.ipn');
   
});
