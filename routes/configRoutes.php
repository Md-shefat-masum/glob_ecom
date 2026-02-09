<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\ProductModelController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\StorageController;

Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

    // config setup
    Route::get('config/setup', [ConfigController::class, 'configSetup'])->name('ConfigSetup');
    Route::post('update/config/setup', [ConfigController::class, 'updateConfigSetup'])->name('UpdateConfigSetup');

    // config routes for falg
    Route::get('/view/all/flags', [ConfigController::class, 'viewAllFlags'])->name('ViewAllFlags');
    Route::get('/delete/flag/{slug}', [ConfigController::class, 'deleteFlag'])->name('DeleteFlag');
    Route::get('/feature/flag/{id}', [ConfigController::class, 'featureFlag'])->name('FeatureFlag');
    Route::get('/get/flag/info/{slug}', [ConfigController::class, 'getFlagInfo'])->name('GetFlagInfo');
    Route::post('/update/flag', [ConfigController::class, 'updateFlagInfo'])->name('UpdateFlagInfo');
    Route::post('/create/new/flag', [ConfigController::class, 'createNewFlag'])->name('CreateNewFlag');
    Route::get('/rearrange/flags', [ConfigController::class, 'rearrangeFlags'])->name('RearrangeFlags');
    Route::post('/save/rearranged/flags', [ConfigController::class, 'saveRearrangedFlags'])->name('SaveRearrangedFlags');

    // config routes for unit
    Route::get('/view/all/units', [ConfigController::class, 'viewAllUnits'])->name('ViewAllUnits');
    Route::get('/delete/unit/{id}', [ConfigController::class, 'deleteUnit'])->name('DeleteUnit');
    Route::get('/get/unit/info/{id}', [ConfigController::class, 'getUnitInfo'])->name('GetUnitInfo');
    Route::post('/update/unit', [ConfigController::class, 'updateUnitInfo'])->name('UpdateUnitInfo');
    Route::post('/create/new/unit', [ConfigController::class, 'createNewUnit'])->name('CreateNewUnit');

    // config routes for sim
    Route::get('/view/all/sims', [ConfigController::class, 'viewAllSims'])->name('ViewAllSims');
    Route::get('/delete/sim/{id}', [ConfigController::class, 'deleteSim'])->name('DeleteSim');
    Route::get('/get/sim/info/{id}', [ConfigController::class, 'getSimInfo'])->name('GetSimInfo');
    Route::post('/update/sim', [ConfigController::class, 'updateSimInfo'])->name('UpdateSimInfo');
    Route::post('/create/new/sim', [ConfigController::class, 'createNewSim'])->name('CreateNewSim');


    // config routes for device condition
    Route::post('/create/new/device/condition', [ConfigController::class, 'addNewDeviceCondition'])->name('AddNewDeviceCondition');
    Route::get('/view/all/device/conditions', [ConfigController::class, 'viewAllDeviceConditions'])->name('ViewAllDeviceConditions');
    Route::get('/delete/device/condition/{id}', [ConfigController::class, 'deleteDeviceCondition'])->name('DeleteDeviceCondition');
    Route::get('/get/device/condition/info/{id}', [ConfigController::class, 'getDeviceConditionInfo'])->name('GetDeviceConditionInfo');
    Route::post('/update/device/condition', [ConfigController::class, 'updateDeviceCondition'])->name('UpdateDeviceCondition');
    Route::get('/rearrange/device/condition', [ConfigController::class, 'rearrangeDeviceCondition'])->name('RearrangeDeviceCondition');
    Route::post('/save/rearranged/device/condition', [ConfigController::class, 'saveRearrangeDeviceCondition'])->name('SaveRearrangeDeviceCondition');


    // config for product warrenty
    Route::post('/create/new/warrenty', [ConfigController::class, 'addNewProductWarrenty'])->name('AddNewProductWarrenty');
    Route::get('/view/all/warrenties', [ConfigController::class, 'viewAllProductWarrenties'])->name('ViewAllProductWarrenties');
    Route::get('/delete/warrenty/{id}', [ConfigController::class, 'deleteProductWarrenty'])->name('DeleteProductWarrenty');
    Route::get('/get/warrenty/info/{id}', [ConfigController::class, 'getProductWarrentyInfo'])->name('GetProductWarrentyInfo');
    Route::post('/update/warrenty', [ConfigController::class, 'updateProductWarrenty'])->name('UpdateProductWarrenty');
    Route::get('/rearrange/warrenty', [ConfigController::class, 'rearrangeWarrenty'])->name('RearrangeWarrenty');
    Route::post('/save/rearranged/warrenty', [ConfigController::class, 'saveRearrangeWarrenties'])->name('SaveRearrangeWarrenties');


    // brand
    Route::get('/add/new/brand', [BrandController::class, 'addNewBrand'])->name('AddNewBrand');
    Route::post('/save/new/brand', [BrandController::class, 'saveNewBrand'])->name('SaveNewBrand');
    Route::get('/view/all/brands', [BrandController::class, 'viewAllBrands'])->name('ViewAllBrands');
    Route::get('/rearrange/brands', [BrandController::class, 'rearrangeBrands'])->name('RearrangeBrands');
    Route::post('/save/rearranged/brands', [BrandController::class, 'saveRearrangeBrands'])->name('saveRearrangeBrands');
    Route::get('/feature/brand/{id}', [BrandController::class, 'featureBrand'])->name('FeatureBrand');
    Route::get('/edit/brand/{slug}', [BrandController::class, 'editBrand'])->name('EditBrand');
    Route::post('/update/brand', [BrandController::class, 'updateBrand'])->name('UpdateBrand');
    Route::get('/delete/brand/{slug}', [BrandController::class, 'deleteBrand'])->name('DeleteBrand');

    // model
    Route::get('/add/new/model', [ProductModelController::class, 'addNewModel'])->name('AddNewModel');
    Route::post('/save/new/model', [ProductModelController::class, 'saveNewModel'])->name('SaveNewModel');
    Route::get('/view/all/models', [ProductModelController::class, 'viewAllModels'])->name('ViewAllModels');
    Route::get('/delete/model/{id}', [ProductModelController::class, 'deleteModel'])->name('DeleteModel');
    Route::get('/edit/model/{slug}', [ProductModelController::class, 'editModel'])->name('EditModel');
    Route::post('/update/model', [ProductModelController::class, 'updateModel'])->name('UpdateModel');
    Route::post('/brand/wise/model', [ProductModelController::class, 'brandWiseModel'])->name('BrandWiseModel');

    // Variant Management
    Route::get('/variant-management', [\App\Http\Controllers\VariantManagementController::class, 'index'])->name('variant-management.index');
    Route::get('/variant-management/groups', [\App\Http\Controllers\VariantManagementController::class, 'getGroups'])->name('variant-management.groups');
    Route::post('/variant-management/groups', [\App\Http\Controllers\VariantManagementController::class, 'storeGroup'])->name('variant-management.groups.store');
    Route::put('/variant-management/groups/{id}', [\App\Http\Controllers\VariantManagementController::class, 'updateGroup'])->name('variant-management.groups.update');
    Route::delete('/variant-management/groups/{id}', [\App\Http\Controllers\VariantManagementController::class, 'deleteGroup'])->name('variant-management.groups.delete');
    Route::get('/variant-management/keys/{groupId}', [\App\Http\Controllers\VariantManagementController::class, 'getKeysByGroup'])->name('variant-management.keys.by-group');
    Route::post('/variant-management/keys', [\App\Http\Controllers\VariantManagementController::class, 'storeKey'])->name('variant-management.keys.store');
    Route::post('/variant-management/keys/{id}', [\App\Http\Controllers\VariantManagementController::class, 'updateKey'])->name('variant-management.keys.update');
    Route::delete('/variant-management/keys/{id}', [\App\Http\Controllers\VariantManagementController::class, 'deleteKey'])->name('variant-management.keys.delete');
    Route::get('/variant-management/products', [\App\Http\Controllers\VariantManagementController::class, 'getVariantProducts'])->name('variant-management.products');
    Route::delete('/variant-management/variants/{id}/detach', [\App\Http\Controllers\VariantManagementController::class, 'detachVariant'])->name('variant-management.variants.detach');

    // colors
    Route::post('/create/new/color', [ColorController::class, 'addNewColor'])->name('AddNewColor');
    Route::get('/view/all/colors', [ColorController::class, 'viewAllColors'])->name('ViewAllColors');
    Route::get('/delete/color/{id}', [ColorController::class, 'deleteColor'])->name('DeleteColor');
    Route::get('/get/color/info/{id}', [ColorController::class, 'getColorInfo'])->name('GetColorInfo');
    Route::post('/update/color', [ColorController::class, 'updateColor'])->name('UpdateColor');

    // storage
    Route::post('/create/new/storage', [StorageController::class, 'addNewStorage'])->name('AddNewStorage');
    Route::get('/view/all/storages', [StorageController::class, 'viewAllStorages'])->name('ViewAllStorages');
    Route::get('/delete/storage/{id}', [StorageController::class, 'deleteStorage'])->name('DeleteStorage');
    Route::get('/get/storage/info/{id}', [StorageController::class, 'getStorageInfo'])->name('GetStorageInfo');
    Route::post('/update/storage', [StorageController::class, 'updateStorage'])->name('UpdateStorage');
    Route::get('/rearrange/storage/types', [StorageController::class, 'rearrangeStorage'])->name('RearrangeStorage');
    Route::post('/save/rearranged/storages', [StorageController::class, 'saveRearrangeStorage'])->name('SaveRearrangeStorage');

    // config routes for sizes
    Route::get('/view/all/sizes', [ConfigController::class, 'viewAllSizes'])->name('ViewAllSizes');
    Route::get('/delete/size/{id}', [ConfigController::class, 'deleteSize'])->name('DeleteSize');
    Route::get('/get/size/info/{id}', [ConfigController::class, 'getSizeInfo'])->name('GetSizeInfo');
    Route::post('/update/size', [ConfigController::class, 'updateSizeInfo'])->name('UpdateSizeInfo');
    Route::post('/create/new/size', [ConfigController::class, 'createNewSize'])->name('CreateNewSize');
    Route::get('/rearrange/size', [ConfigController::class, 'rearrangeSize'])->name('RearrangeSize');
    Route::post('/save/rearranged/sizes', [ConfigController::class, 'saveRearrangedSizes'])->name('SaveRearrangedSizes');

});