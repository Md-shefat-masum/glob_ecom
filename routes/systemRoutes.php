<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemController;

Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

    // system routes for email config
    Route::get('/view/email/credential', [SystemController::class, 'viewEmailCredentials'])->name('ViewEmailCredentials');
    Route::get('/view/email/templates', [SystemController::class, 'viewEmailTemplates'])->name('ViewEmailTemplates');
    Route::get('/change/mail/template/status/{templateId}', [SystemController::class, 'changeMailTemplateStatus'])->name('ChangeMailTemplateStatus');
    Route::post('/save/new/email/configure', [SystemController::class, 'saveEmailCredential'])->name('SaveEmailCredential');
    Route::get('/delete/email/config/{slug}', [SystemController::class, 'deleteEmailCredential'])->name('DeleteEmailCredential');
    Route::get('/get/email/config/info/{slug}', [SystemController::class, 'getEmailCredentialInfo'])->name('GetEmailCredentialInfo');
    Route::post('/update/email/config', [SystemController::class, 'updateEmailCredentialInfo'])->name('UpdateEmailCredentialInfo');

    // system route for sms gateway
    Route::get('/setup/sms/gateways', [SystemController::class, 'viewSmsGateways'])->name('ViewSmsGateways');
    Route::post('/update/sms/gateway/info', [SystemController::class, 'updateSmsGatewayInfo'])->name('UpdateSmsGatewayInfo');
    Route::get('/change/gateway/status/{provider}', [SystemController::class, 'changeGatewayStatus'])->name('ChangeGatewayStatus');

    // system route for payment gateway
    Route::get('/setup/payment/gateways', [SystemController::class, 'viewPaymentGateways'])->name('ViewPaymentGateways');
    Route::post('/update/payment/gateway/info', [SystemController::class, 'updatePaymentGatewayInfo'])->name('UpdatePaymentGatewayInfo');
    Route::get('/change/payment/gateway/status/{provider}', [SystemController::class, 'changePaymentGatewayStatus'])->name('ChangePaymentGatewayStatus');

});