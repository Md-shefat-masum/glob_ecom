<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\PermissionRoutesController;


Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

   // customers and system users routes
    Route::get('/view/all/customers', [UserController::class, 'viewAllCustomers'])->name('ViewAllCustomers');
    Route::get('/view/system/users', [UserController::class, 'viewAllSystemUsers'])->name('ViewAllSystemUsers');
    Route::get('/add/new/system/user', [UserController::class, 'addNewSystemUsers'])->name('AddNewSystemUsers');
    Route::post('/create/system/user', [UserController::class, 'createSystemUsers'])->name('CreateSystemUsers');
    Route::get('/edit/system/user/{id}', [UserController::class, 'editSystemUser'])->name('EditSystemUser');
    Route::get('/delete/system/user/{id}', [UserController::class, 'deleteSystemUser'])->name('DeleteSystemUser');
    Route::post('/update/system/user', [UserController::class, 'updateSystemUser'])->name('UpdateSystemUser');
    Route::get('make/user/superadmin/{id}', [UserController::class, 'makeSuperAdmin'])->name('MakeSuperAdmin');
    Route::get('revoke/user/superadmin/{id}', [UserController::class, 'revokeSuperAdmin'])->name('RevokeSuperAdmin');
    Route::get('/change/user/status/{id}', [UserController::class, 'changeUserStatus'])->name('ChangeUserStatus');
    Route::get('/delete/customer/{id}', [UserController::class, 'deleteCustomer'])->name('DeleteCustomer');
    Route::get('download/customer/excel', [UserController::class, 'downloadCustomerExcel'])->name('DownloadCustomerExcel');

    // user role permission routes
    Route::get('/view/permission/routes', [PermissionRoutesController::class, 'viewAllPermissionRoutes'])->name('ViewAllPermissionRoutes');
    Route::get('/regenerate/permission/routes', [PermissionRoutesController::class, 'regeneratePermissionRoutes'])->name('RegeneratePermissionRoutes');
    Route::get('/view/user/roles', [UserRoleController::class, 'viewAllUserRoles'])->name('ViewAllUserRoles');
    Route::get('/new/user/role', [UserRoleController::class, 'newUserRole'])->name('NewUserRole');
    Route::post('save/user/role', [UserRoleController::class, 'saveUserRole'])->name('SaveUserRole');
    Route::get('/delete/user/role/{id}', [UserRoleController::class, 'deleteUserRole'])->name('DeleteUserRole');
    Route::get('/edit/user/role/{id}', [UserRoleController::class, 'editUserRole'])->name('EditUserRole');
    Route::post('update/user/role', [UserRoleController::class, 'updateUserRole'])->name('UpdateUserRole');
    Route::get('/view/user/role/permission', [UserRoleController::class, 'viewUserRolePermission'])->name('ViewUserRolePermission');
    Route::get('/assign/role/permission/{id}', [UserRoleController::class, 'assignRolePermission'])->name('AssignRolePermission');
    Route::post('/save/assigned/role/permission', [UserRoleController::class, 'saveAssignedRolePermission'])->name('SaveAssignedRolePermission');

});