<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactRequestontroller;
use App\Http\Controllers\SupportTicketController;
use App\Http\Controllers\SubscribedUsersController;
use App\Http\Controllers\Outlet\CustomerSourceController;
use App\Http\Controllers\Customer\CustomerCategoryController;
use App\Http\Controllers\Customer\CustomerEcommerceController;
use App\Http\Controllers\Customer\CustomerContactHistoryController;
use App\Http\Controllers\Customer\CustomerNextContactDateController;
use App\Http\Controllers\Customer\BulkSmsBdManagementController;

Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

    // Customer Source Type 
    Route::get('/add/new/customer-source', [CustomerSourceController::class, 'addNewCustomerSource'])->name('AddNewCustomerSource');    
    Route::post('/save/new/customer-source', [CustomerSourceController::class, 'saveNewCustomerSource'])->name('SaveNewCustomerSource');
    Route::get('/view/all/customer-source', [CustomerSourceController::class, 'viewAllCustomerSource'])->name('ViewAllCustomerSource');
    Route::get('/delete/customer-source/{slug}', [CustomerSourceController::class, 'deleteCustomerSource'])->name('DeleteCustomerSource');
    Route::get('/edit/customer-source/{slug}', [CustomerSourceController::class, 'editCustomerSource'])->name('EditCustomerSource');      
    Route::post('/update/customer-source', [CustomerSourceController::class, 'updateCustomerSource'])->name('UpdateCustomerSource');

    // Customer Category 
    Route::get('/add/new/customer-category', [CustomerCategoryController::class, 'addNewCustomerCategory'])->name('AddNewCustomerCategory');    
    Route::post('/save/new/customer-category', [CustomerCategoryController::class, 'saveNewCustomerCategory'])->name('SaveNewCustomerCategory');
    Route::get('/view/all/customer-category', [CustomerCategoryController::class, 'viewAllCustomerCategory'])->name('ViewAllCustomerCategory');
    Route::get('/delete/customer-category/{slug}', [CustomerCategoryController::class, 'deleteCustomerCategory'])->name('DeleteCustomerCategory');
    Route::get('/edit/customer-category/{slug}', [CustomerCategoryController::class, 'editCustomerCategory'])->name('EditCustomerCategory');      
    Route::post('/update/customer-category', [CustomerCategoryController::class, 'updateCustomerCategory'])->name('UpdateCustomerCategory');

    // Customer Ecommerce
    Route::get('/add/new/customer-ecommerce', [CustomerEcommerceController::class, 'addNewCustomerEcommerce'])->name('AddNewCustomerEcommerce');    
    Route::post('/save/new/customer-ecommerce', [CustomerEcommerceController::class, 'saveNewCustomerEcommerce'])->name('SaveNewCustomerEcommerce');
    Route::get('/view/all/customer-ecommerce', [CustomerEcommerceController::class, 'viewAllCustomerEcommerce'])->name('ViewAllCustomerEcommerce');
    Route::get('/delete/customer-ecommerce/{slug}', [CustomerEcommerceController::class, 'deleteCustomerEcommerce'])->name('DeleteCustomerEcommerce');
    Route::get('/edit/customer-ecommerce/{slug}', [CustomerEcommerceController::class, 'editCustomerEcommerce'])->name('EditCustomerEcommerce');      
    Route::post('/update/customer-ecommerce', [CustomerEcommerceController::class, 'updateCustomerEcommerce'])->name('UpdateCustomerEcommerce');

    
    // Customer Contact History
    Route::get('/add/new/customer-contact-history', [CustomerContactHistoryController::class, 'addNewCustomerContactHistory'])->name('AddNewCustomerContactHistories');    
    Route::post('/save/new/customer-contact-history', [CustomerContactHistoryController::class, 'saveNewCustomerContactHistory'])->name('SaveNewCustomerContactHistories');
    Route::get('/view/all/customer-contact-history', [CustomerContactHistoryController::class, 'viewAllCustomerContactHistory'])->name('ViewAllCustomerContactHistories');
    Route::get('/delete/customer-contact-history/{slug}', [CustomerContactHistoryController::class, 'deleteCustomerContactHistory'])->name('DeleteCustomerContactHistories');
    Route::get('/edit/customer-contact-history/{slug}', [CustomerContactHistoryController::class, 'editCustomerContactHistory'])->name('EditCustomerContactHistories');      
    Route::post('/update/customer-contact-history', [CustomerContactHistoryController::class, 'updateCustomerContactHistory'])->name('UpdateCustomerContactHistories');

    // Customer Next Contact Date
    Route::get('/add/new/customer-next-contact-date', [CustomerNextContactDateController::class, 'addNewCustomerNextContactDate'])->name('AddNewCustomerNextContactDate');    
    Route::post('/save/new/customer-next-contact-date', [CustomerNextContactDateController::class, 'saveNewCustomerNextContactDate'])->name('SaveNewCustomerNextContactDate');
    Route::get('/view/all/customer-next-contact-date', [CustomerNextContactDateController::class, 'viewAllCustomerNextContactDate'])->name('ViewAllCustomerNextContactDate');
    Route::get('/delete/customer-next-contact-date/{slug}', [CustomerNextContactDateController::class, 'deleteCustomerNextContactDate'])->name('DeleteCustomerNextContactDate');
    Route::get('/edit/customer-next-contact-date/{slug}', [CustomerNextContactDateController::class, 'editCustomerNextContactDate'])->name('EditCustomerNextContactDate');      
    Route::post('/update/customer-next-contact-date', [CustomerNextContactDateController::class, 'updateCustomerNextContactDate'])->name('UpdateCustomerNextContactDate');


    // support ticket routes
    Route::get('/pending/support/tickets', [SupportTicketController::class, 'pendingSupportTickets'])->name('PendingSupportTickets');
    Route::get('/solved/support/tickets', [SupportTicketController::class, 'solvedSupportTickets'])->name('SolvedSupportTickets');
    Route::get('/on/hold/support/tickets', [SupportTicketController::class, 'onHoldSupportTickets'])->name('OnHoldSupportTickets');
    Route::get('/rejected/support/tickets', [SupportTicketController::class, 'rejectedSupportTickets'])->name('RejectedSupportTickets');
    Route::get('/delete/support/ticket/{slug}', [SupportTicketController::class, 'deleteSupportTicket'])->name('DeleteSupportTicket');
    Route::get('/support/status/change/{slug}', [SupportTicketController::class, 'changeStatusSupport'])->name('ChangeStatusSupport');
    Route::get('/support/status/on/hold/{slug}', [SupportTicketController::class, 'changeStatusSupportOnHold'])->name('ChangeStatusSupportOnHold');
    Route::get('/support/status/in/progress/{slug}', [SupportTicketController::class, 'changeStatusSupportInProgress'])->name('ChangeStatusSupportInProgress');
    Route::get('/support/status/rejected/{slug}', [SupportTicketController::class, 'changeStatusSupportRejected'])->name('ChangeStatusSupportRejected');
    Route::get('/view/support/messages/{slug}', [SupportTicketController::class, 'viewSupportMessage'])->name('ViewSupportMessage');
    Route::post('/send/support/message', [SupportTicketController::class, 'sendSupportMessage'])->name('SendSupportMessage');

     // subscribed users routes
    Route::get('/view/all/subscribed/users', [SubscribedUsersController::class, 'viewAllSubscribedUsers'])->name('ViewAllSubscribedUsers');
    Route::get('/delete/subcribed/users/{id}', [SubscribedUsersController::class, 'deleteSubscribedUsers'])->name('DeleteSubscribedUsers');
    Route::get('/download/subscribed/users/excel', [SubscribedUsersController::class, 'downloadSubscribedUsersExcel'])->name('DownloadSubscribedUsersExcel');
    Route::get('/subscribed/users/send-email', [SubscribedUsersController::class, 'sendEmailPage'])->name('SendEmailSubscribedUsers');
    Route::post('/subscribed/users/send-email', [SubscribedUsersController::class, 'sendBulkEmail'])->name('SendBulkEmailSubscribedUsers');


   // contact request routes
    Route::get('/view/all/contact/requests', [ContactRequestontroller::class, 'viewAllContactRequests'])->name('ViewAllContactRequests');
    Route::get('/delete/contact/request/{id}', [ContactRequestontroller::class, 'deleteContactRequests'])->name('DeleteContactRequests');
    Route::get('/change/request/status/{id}', [ContactRequestontroller::class, 'changeRequestStatus'])->name('ChangeRequestStatus');

    // Bulk SMS BD Management routes
    Route::prefix('bulk-sms-bd')->name('bulk-sms-bd.')->group(function () {
        Route::get('/', [BulkSmsBdManagementController::class, 'index'])->name('index');
        Route::get('/customers', [BulkSmsBdManagementController::class, 'getCustomers'])->name('customers');
        Route::post('/send-single', [BulkSmsBdManagementController::class, 'sendSingle'])->name('send-single');
        Route::post('/send-one-to-many', [BulkSmsBdManagementController::class, 'sendOneToMany'])->name('send-one-to-many');
        Route::post('/send-many-to-many', [BulkSmsBdManagementController::class, 'sendManyToMany'])->name('send-many-to-many');
        Route::get('/get-balance', [BulkSmsBdManagementController::class, 'getBalance'])->name('get-balance');
    });
    
});