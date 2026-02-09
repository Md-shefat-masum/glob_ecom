<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GeneralInfoController;

Route::group([
        'middleware' => [
            'auth', 
            'CheckUserType', 
            'DemoMode'
        ]
    ], function () {

    // general info routes
  
    Route::get('/general/info', [GeneralInfoController::class, 'generalInfo'])->name('GeneralInfo');
    Route::post('/update/general/info', [GeneralInfoController::class, 'updateGeneralInfo'])->name('UpdateGeneralInfo');
    Route::get('/website/theme/page', [GeneralInfoController::class, 'websiteThemePage'])->name('WebsiteThemePage');
    Route::post('/update/website/theme/color', [GeneralInfoController::class, 'updateWebsiteThemeColor'])->name('UpdateWebsiteThemeColor');
    Route::get('/social/media/page', [GeneralInfoController::class, 'socialMediaPage'])->name('SocialMediaPage');
    Route::post('/update/social/media/link', [GeneralInfoController::class, 'updateSocialMediaLinks'])->name('UpdateSocialMediaLinks');
    Route::get('/seo/homepage', [GeneralInfoController::class, 'seoHomePage'])->name('SeoHomePage');
    Route::post('/update/seo/homepage', [GeneralInfoController::class, 'updateSeoHomePage'])->name('UpdateSeoHomePage');
    Route::get('/custom/css/js', [GeneralInfoController::class, 'customCssJs'])->name('CustomCssJs');
    Route::post('/update/custom/css/js', [GeneralInfoController::class, 'updateCustomCssJs'])->name('UpdateCustomCssJs');
    Route::get('/social/chat/script/page', [GeneralInfoController::class, 'socialChatScriptPage'])->name('SocialChatScriptPage');
    Route::post('/update/google/recaptcha', [GeneralInfoController::class, 'updateGoogleRecaptcha'])->name('UpdateGoogleRecaptcha');
    Route::post('/update/google/analytic', [GeneralInfoController::class, 'updateGoogleAnalytic'])->name('UpdateGoogleAnalytic');
    Route::post('/update/google/tag/manager', [GeneralInfoController::class, 'updateGoogleTagManager'])->name('updateGoogleTagManager');
    Route::post('/update/social/login/info', [GeneralInfoController::class, 'updateSocialLogin'])->name('UpdateSocialLogin');
    Route::post('/update/facebook/pixel', [GeneralInfoController::class, 'updateFacebookPixel'])->name('UpdateFacebookPixel');
    Route::post('/update/messenger/chat/info', [GeneralInfoController::class, 'updateMessengerChat'])->name('UpdateMessengerChat');
    Route::post('/update/tawk/chat/info', [GeneralInfoController::class, 'updateTawkChat'])->name('UpdateTawkChat');
    Route::post('/update/crisp/chat/info', [GeneralInfoController::class, 'updateCrispChat'])->name('UpdateCrispChat');
    Route::get('/change/guest/checkout/status', [GeneralInfoController::class, 'changeGuestCheckoutStatus'])->name('ChangeGuestCheckoutStatus');

});