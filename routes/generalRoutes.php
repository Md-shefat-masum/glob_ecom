<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SmsServiceController;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

    // demo products route
    Route::get('generate/demo/products', [ProductController::class, 'generateDemoProducts'])->name('GenerateDemoProducts');
    Route::post('save/generated/demo/products', [ProductController::class, 'saveGeneratedDemoProducts'])->name('SaveGeneratedDemoProducts');
    Route::get('remove/demo/products/page', [ProductController::class, 'removeDemoProductsPage'])->name('RemoveDemoProductsPage');
    Route::get('remove/demo/products', [ProductController::class, 'removeDemoProducts'])->name('RemoveDemoProducts');
    
    // backup download
    Route::get('/download/database/backup', [BackupController::class, 'downloadDBBackup'])->name('DownloadDBBackup');
    Route::get('/download/product/files/backup', [BackupController::class, 'downloadProductFilesBackup'])->name('DownloadProductFilesBackup');
    Route::get('/download/user/files/backup', [BackupController::class, 'downloadUserFilesBackup'])->name('DownloadUserFilesBackup');
    Route::get('/download/banner/files/backup', [BackupController::class, 'downloadBannerFilesBackup'])->name('DownloadBannerFilesBackup');
    Route::get('/download/category/files/backup', [BackupController::class, 'downloadCategoryFilesBackup'])->name('DownloadCategoryFilesBackup');
    Route::get('/download/subcategory/files/backup', [BackupController::class, 'downloadSubcategoryFilesBackup'])->name('DownloadSubcategoryFilesBackup');
    Route::get('/download/flag/files/backup', [BackupController::class, 'downloadFlagFilesBackup'])->name('DownloadFlagFilesBackup');
    Route::get('/download/ticket/files/backup', [BackupController::class, 'downloadTicketFilesBackup'])->name('DownloadTicketFilesBackup');
    Route::get('/download/blog/files/backup', [BackupController::class, 'downloadBlogFilesBackup'])->name('DownloadBlogFilesBackup');
    Route::get('/download/other/files/backup', [BackupController::class, 'downloadOtherFilesBackup'])->name('DownloadOtherFilesBackup');

    // sms service
    Route::get('/view/sms/templates', [SmsServiceController::class, 'viewSmsTemplates'])->name('ViewSmsTemplates');
    Route::get('/create/sms/template', [SmsServiceController::class, 'createSmsTemplate'])->name('CreateSmsTemplate');
    Route::post('/save/sms/template', [SmsServiceController::class, 'saveSmsTemplate'])->name('SaveSmsTemplate');
    Route::get('get/sms/template/info/{id}', [SmsServiceController::class, 'getSmsTemplateInfo'])->name('GetSmsTemplateInfo');
    Route::get('delete/sms/template/{id}', [SmsServiceController::class, 'deleteSmsTemplate'])->name('DeleteSmsTemplate');
    Route::get('/send/sms/page', [SmsServiceController::class, 'sendSmsPage'])->name('SendSmsPage');
    Route::post('/get/template/description', [SmsServiceController::class, 'getTemplateDescription'])->name('GetTemplateDescription');
    Route::post('/update/sms/template', [SmsServiceController::class, 'updateSmsTemplate'])->name('UpdateSmsTemplate');
    Route::post('/send/sms', [SmsServiceController::class, 'sendSms'])->name('SendSms');
    Route::get('/view/sms/history', [SmsServiceController::class, 'viewSmsHistory'])->name('ViewSmsHistory');
    Route::get('/delete/sms/with/range', [SmsServiceController::class, 'deleteSmsHistoryRange'])->name('DeleteSmsHistoryRange');
    Route::get('/delete/sms/{id}', [SmsServiceController::class, 'deleteSmsHistory'])->name('DeleteSmsHistory');

});

// // Public route to serve uploads files (without authentication)
// // This is a fallback for direct file access
// Route::get('/uploads/{path}', function ($path) {
//     $filePath = public_path('uploads/' . $path);
    
//     if (!File::exists($filePath)) {
//         abort(404);
//     }
    
//     $file = File::get($filePath);
//     $type = File::mimeType($filePath);
    
//     return Response::make($file, 200)->header('Content-Type', $type);
// })->where('path', '.*');