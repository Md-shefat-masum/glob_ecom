<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SmsServiceController;



//auth routes start
require __DIR__.'/authRoutes.php';
//auth routes end

//dashboard routes start
require __DIR__.'/dashboardRoutes.php';
//Dashboard routes end

// payment routes start
require __DIR__.'/paymentRoutes.php';

// file manager routes start
// Route::get('/file-manager', function () {
//     return view('backend.file_manager');
// })->middleware(['auth']);

// Route::group(['prefix' => 'laravel-filemanager', 'middleware' => ['web', 'auth']], function () {
//     \UniSharp\LaravelFilemanager\Lfm::routes();
// });
// file manager routes end

//ecommerce routes
require __DIR__.'/ecommerceRoutes.php';

//inventory routes
require __DIR__.'/inventoryRoutes.php';

//accounts routes
require __DIR__.'/accountRoutes.php';

//crm routes
require __DIR__.'/crmRoutes.php';

//role and permission routes
require __DIR__.'/rolePermissionRoutes.php';

//website config routes
require __DIR__.'/WebConfigRoutes.php';

//cms routes
require __DIR__.'/cmsRoutes.php';

//clear cache routes
require __DIR__.'/cache.php';

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

    // Route::get('/search/products',  [ProductController::class, 'searchProduct'])->name('searchProduct');
    // Route::get('/search/products', function (Illuminate\Http\Request $request) {
    //     $query = request()->get('search');
    //     $products = Product::where('name', 'LIKE', "%{$query}%")
    //                   ->select('id', 'name', 'price')
    //                   ->limit(10)
    //                   ->get();
    
    //     return response()->json($products);
    // });
        // #7EA01D
        // #846F51

    // // vendor routes
    // Route::get('/create/new/vendor', [VendorController::class, 'createNewVendor'])->name('CreateNewVendor');
    // Route::post('/save/vendor', [VendorController::class, 'saveVendor'])->name('SaveVendor');
    // Route::get('/view/all/vendors', [VendorController::class, 'viewAllVendors'])->name('ViewAllVendors');
    // Route::get('/view/vendor/requests', [VendorController::class, 'viewVendorRequests'])->name('ViewVendorRequests');
    // Route::get('/view/inactive/vendors', [VendorController::class, 'viewInactiveVendors'])->name('ViewInactiveVendors');
    // Route::get('/edit/vendor/{vendor_no}', [VendorController::class, 'editVendor'])->name('EditVendor');
    // Route::post('/update/vendor', [VendorController::class, 'updateVendor'])->name('UpdateVendor');
    // Route::get('/approve/vendor/{vendor_no}', [VendorController::class, 'approveVendor'])->name('ApproveVendor');
    // Route::get('/delete/vendor/{vendor_no}', [VendorController::class, 'deleteVendor'])->name('DeleteVendor');
    // Route::get('/download/approved/vendors/excel', [VendorController::class, 'downloadApprovedVendorsExcel'])->name('DownloadApprovedVendorsExcel');

    // // store routes
    // Route::get('/create/new/store', [StoreController::class, 'createNewStore'])->name('CreateNewStore');
    // Route::post('/save/store', [StoreController::class, 'saveStore'])->name('SaveStore');
    // Route::get('/view/all/stores', [StoreController::class, 'viewAllStores'])->name('ViewAllStores');
    // Route::get('/inactive/store/{id}', [StoreController::class, 'inactiveStore'])->name('InactiveStore');
    // Route::get('/activate/store/{id}', [StoreController::class, 'activateStore'])->name('ActivateStore');
    // Route::get('/edit/store/{slug}', [StoreController::class, 'editStore'])->name('EditStore');
    // Route::post('/update/store', [StoreController::class, 'updateStore'])->name('UpdateStore');

    // withdraw routes
    // Route::get('create/new/withdraw', [WithdrawController::class, 'createWithdrawRequest'])->name('CreateWithdrawRequest');
    // Route::post('vendor/balance', [WithdrawController::class, 'getVendorBalance'])->name('CreateWithdrawRequest');
    // Route::post('save/withdraw/request', [WithdrawController::class, 'saveWithdrawRequest'])->name('SaveWithdrawRequest');
    // Route::get('view/all/withdraws', [WithdrawController::class, 'viewAllWithdraws'])->name('ViewAllWithdraws');
    // Route::get('view/withdraw/requests', [WithdrawController::class, 'viewWithdrawRequests'])->name('ViewWithdrawRequests');
    // Route::get('view/completed/withdraws', [WithdrawController::class, 'viewCompletedWithdraws'])->name('ViewCompletedWithdraws');
    // Route::get('view/cancelled/withdraws', [WithdrawController::class, 'viewCancelledWithdraws'])->name('ViewCancelledWithdraws');
    // Route::get('delete/withdraw/{id}', [WithdrawController::class, 'deleteWithdraw'])->name('DeleteWithdraw');
    // Route::get('get/withdraw/info/{id}', [WithdrawController::class, 'getWithdrawInfo'])->name('getWithdrawInfo');
    // Route::get('deny/withdraw/{id}', [WithdrawController::class, 'denyWithdraw'])->name('DenyWithdraw');
    // Route::post('approve/withdraw', [WithdrawController::class, 'approveWithdraw'])->name('ApproveWithdraw');

});