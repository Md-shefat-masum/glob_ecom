<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Customer\CustomerController;
use App\Http\Controllers\Outlet\SupplierSourceController;
use App\Http\Controllers\Inventory\ProductSupplierController;
use App\Http\Controllers\Inventory\ProductWarehouseController;
use App\Http\Controllers\Inventory\Models\ProductWarehouseRoom;
use App\Http\Controllers\Inventory\ProductPurchaseOrderController;
use App\Http\Controllers\Inventory\ProductWarehouseRoomController;
use App\Http\Controllers\Inventory\ProductPurchaseChargeController;
use App\Http\Controllers\Inventory\ProductPurchaseQuotationController;
use App\Http\Controllers\Inventory\ProductPurchasReturnController;
use App\Http\Controllers\Inventory\ProductWarehouseRoomCartoonController;
use App\Http\Controllers\Inventory\ProductOrderController;
use App\Http\Controllers\Inventory\ProductOrderReturnController;
use App\Http\Controllers\Customer\CustomerPaymentController;
use App\Http\Controllers\Product\ManualProductReturnController;

Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

    // product warehouse routes
    Route::get('/add/new/product-warehouse', [ProductWarehouseController::class, 'addNewProductWarehouse'])->name('AddNewProductWarehouse');
    //  Route::post('/subcategory/wise/childcategory', [ProductController::class, 'childcategorySubcategoryWise'])->name('ChildcategorySubcategoryWise');
    Route::post('/save/new/product-warehouse', [ProductWarehouseController::class, 'saveNewProductWarehouse'])->name('SaveNewProductWarehouse');
    Route::get('/view/all/product-warehouse', [ProductWarehouseController::class, 'viewAllProductWarehouse'])->name('ViewAllProductWarehouse');
    Route::get('/delete/product-warehouse/{slug}', [ProductWarehouseController::class, 'deleteProductWarehouse'])->name('DeleteProductWarehouse');
    Route::get('/edit/product-warehouse/{slug}', [ProductWarehouseController::class, 'editProductWarehouse'])->name('EditProductWarehouse');
    Route::post('/update/product-warehouse', [ProductWarehouseController::class, 'updateProductWarehouse'])->name('UpdateProductWarehouse');
    //  Route::post('/add/another/variant', [ProductController::class, 'addAnotherVariant'])->name('AddAnotherVariant');
    //  Route::get('/delete/product/variant/{id}', [ProductController::class, 'deleteProductVariant'])->name('DeleteProductVariant');
    //  Route::get('/products/from/excel', [ProductController::class, 'productsFromExcel'])->name('ProductsFromExcel');
    //  Route::post('/upload/product/from/excel', [ProductController::class, 'uploadProductsFromExcel'])->name('UploadProductsFromExcel');


    // product warehouse rooms routes
    Route::get('/add/new/product-warehouse-room', [ProductWarehouseRoomController::class, 'addNewProductWarehouseRoom'])->name('AddNewProductWarehouseRoom');
    //  Route::post('/subcategory/wise/childcategory', [ProductController::class, 'childcategorySubcategoryWise'])->name('ChildcategorySubcategoryWise');
    Route::post('/save/new/product-warehouse-room', [ProductWarehouseRoomController::class, 'saveNewProductWarehouseRoom'])->name('SaveNewProductWarehouseRoom');
    Route::get('/view/all/product-warehouse-room', [ProductWarehouseRoomController::class, 'viewAllProductWarehouseRoom'])->name('ViewAllProductWarehouseRoom');
    Route::get('/delete/product-warehouse-room/{slug}', [ProductWarehouseRoomController::class, 'deleteProductWarehouseRoom'])->name('DeleteProductWarehouseRoom');
    Route::get('/edit/product-warehouse-room/{slug}', [ProductWarehouseRoomController::class, 'editProductWarehouseRoom'])->name('EditProductWarehouseRoom');
    Route::post('/update/product-warehouse-room', [ProductWarehouseRoomController::class, 'updateProductWarehouseRoom'])->name('UpdateProductWarehouseRoom');
    Route::post('/get-product-warehouse-rooms', [ProductWarehouseRoomController::class, 'getProductWarehouseRooms'])->name('get.product.warehouse.rooms');
    Route::get('/get-warehouse-rooms/{warehouseId}', function ($warehouseId) {
        $rooms = ProductWarehouseRoom::where('product_warehouse_id', $warehouseId)->get();
        return response()->json(['rooms' => $rooms]);
    });


    // product warehouse room cartoon routes
    Route::get('/add/new/product-warehouse-room-cartoon', [ProductWarehouseRoomCartoonController::class, 'addNewProductWarehouseRoomCartoon'])->name('AddNewProductWarehouseRoomCartoon');
    //  Route::post('/subcategory/wise/childcategory', [ProductController::class, 'childcategorySubcategoryWise'])->name('ChildcategorySubcategoryWise');
    Route::post('/save/new/product-warehouse-room-cartoon', [ProductWarehouseRoomCartoonController::class, 'saveNewProductWarehouseRoomCartoon'])->name('SaveNewProductWarehouseRoomCartoon');
    Route::get('/view/all/product-warehouse-room-cartoon', [ProductWarehouseRoomCartoonController::class, 'viewAllProductWarehouseRoomCartoon'])->name('ViewAllProductWarehouseRoomCartoon');
    Route::get('/delete/product-warehouse-room-cartoon/{slug}', [ProductWarehouseRoomCartoonController::class, 'deleteProductWarehouseRoomCartoon'])->name('DeleteProductWarehouseRoomCartoon');
    Route::get('/edit/product-warehouse-room-cartoon/{slug}', [ProductWarehouseRoomCartoonController::class, 'editProductWarehouseRoomCartoon'])->name('EditProductWarehouseRoomCartoon');
    Route::post('/update/product-warehouse-room-cartoon', [ProductWarehouseRoomCartoonController::class, 'updateProductWarehouseRoomCartoon'])->name('UpdateProductWarehouseRoomCartoon');
    Route::post('/get-product-warehouse-room-cartoons', [ProductWarehouseRoomCartoonController::class, 'getProductWarehouseRoomCartoon'])->name('get.product.warehouse.room.cartoon');

    // product supplier routes
    Route::get('/add/new/product-supplier', [ProductSupplierController::class, 'addNewProductSupplier'])->name('AddNewProductSupplier');
    Route::post('/save/new/product-supplier', [ProductSupplierController::class, 'saveNewProductSupplier'])->name('SaveNewProductSupplier');
    Route::get('/view/all/product-supplier', [ProductSupplierController::class, 'viewAllProductSupplier'])->name('ViewAllProductSupplier');
    Route::get('/delete/product-supplier/{slug}', [ProductSupplierController::class, 'deleteProductSupplier'])->name('DeleteProductSupplier');
    Route::get('/edit/product-supplier/{slug}', [ProductSupplierController::class, 'editProductSupplier'])->name('EditProductSupplier');
    Route::post('/update/product-supplier', [ProductSupplierController::class, 'updateProductSupplier'])->name('UpdateProductSupplier');

    // Supplier Source Type 
    Route::get('/add/new/supplier-source', [SupplierSourceController::class, 'addNewSupplierSource'])->name('AddNewSupplierSource');
    Route::post('/save/new/supplier-source', [SupplierSourceController::class, 'saveNewSupplierSource'])->name('SaveNewSupplierSource');
    Route::get('/view/all/supplier-source', [SupplierSourceController::class, 'viewAllSupplierSource'])->name('ViewAllSupplierSource');
    Route::get('/delete/supplier-source/{slug}', [SupplierSourceController::class, 'deleteSupplierSource'])->name('DeleteSupplierSource');
    Route::get('/edit/supplier-source/{slug}', [SupplierSourceController::class, 'editSupplierSource'])->name('EditSupplierSource');
    Route::post('/update/supplier-source', [SupplierSourceController::class, 'updateSupplierSource'])->name('UpdateSupplierSource');


    Route::get('/get-warehouse-rooms', [ProductWarehouseController::class, 'getWarehouseRooms']);
    Route::get('/get-warehouse-room-cartoons', [ProductWarehouseController::class, 'getWarehouseRoomCartoons']);

    // Route::get('get-rooms/{warehouseId}', [WarehouseController::class, 'getRooms'])->name('get.rooms');
    // Route::get('get-cartoons/{roomId}', [WarehouseController::class, 'getCartoons'])->name('get.cartoons');

    Route::get('/api/get-rooms/{warehouseId}', [ProductWarehouseController::class, 'apiGetetWarehouseRooms']);
    Route::get('/api/get-cartoons/{warehouseId}/{roomId}', [ProductWarehouseController::class, 'apiGetetWarehouseRoomCartoons']);


    // purchase product quotation routes
    Route::get('/add/new/purchase-product/quotation', [ProductPurchaseQuotationController::class, 'addNewPurchaseProductQuotation'])->name('AddNewPurchaseProductQuotation');
    Route::post('/save/new/purchase-product/quotation', [ProductPurchaseQuotationController::class, 'saveNewPurchaseProductQuotation'])->name('SaveNewPurchaseProductQuotation');
    Route::get('/view/all/purchase-product/quotation', [ProductPurchaseQuotationController::class, 'viewAllPurchaseProductQuotation'])->name('ViewAllPurchaseProductQuotation');
    Route::get('/delete/purchase-product/quotation/{slug}', [ProductPurchaseQuotationController::class, 'deletePurchaseProductQuotation'])->name('DeletePurchaseProductQuotation');
    Route::get('/edit/purchase-product/quotation/{slug}', [ProductPurchaseQuotationController::class, 'editPurchaseProductQuotation'])->name('EditPurchaseProductQuotation');
    Route::get('/edit/purchase-product/sales/quotation/{slug}', [ProductPurchaseQuotationController::class, 'editPurchaseProductSalesQuotation'])->name('EditPurchaseProductSalesQuotation');
    Route::get('api/edit/purchase-product/quotation/{slug}', [ProductPurchaseQuotationController::class, 'apiEditPurchaseProduct'])->name('ApiEditPurchaseProductQuotation');
    Route::post('/update/purchase-product/quotation', [ProductPurchaseQuotationController::class, 'updatePurchaseProductQuotation'])->name('UpdatePurchaseProductQuotation');
    Route::post('/update/purchase-product/sales/quotation', [ProductPurchaseQuotationController::class, 'updatePurchaseProductSalesQuotation'])->name('UpdatePurchaseProductSalesQuotation');

    Route::get('/api/products/search', [ProductPurchaseQuotationController::class, 'searchProduct'])->name('SearchProduct');

    // purchase product order routes
    Route::get('/add/new/purchase-product/order', [ProductPurchaseOrderController::class, 'addNewPurchaseProductOrder'])->name('AddNewPurchaseProductOrder');
    Route::post('/save/new/purchase-product/order', [ProductPurchaseOrderController::class, 'saveNewPurchaseProductOrder'])->name('SaveNewPurchaseProductOrder');
    Route::get('/view/all/purchase-product/order', [ProductPurchaseOrderController::class, 'viewAllPurchaseProductOrder'])->name('ViewAllPurchaseProductOrder');
    Route::get('/delete/purchase-product/order/{slug}', [ProductPurchaseOrderController::class, 'deletePurchaseProductOrder'])->name('DeletePurchaseProductOrder');
    Route::get('/edit/purchase-product/order/{slug}', [ProductPurchaseOrderController::class, 'editPurchaseProductOrder'])->name('EditPurchaseProductOrder');
    Route::get('/edit/purchase-product/order/confirm/{slug}', [ProductPurchaseOrderController::class, 'editPurchaseProductOrderConfirm'])->name('EditPurchaseProductOrderConfirm');
    Route::get('api/edit/purchase-product/order/{slug}', [ProductPurchaseOrderController::class, 'apiEditPurchaseProduct'])->name('ApiEditPurchaseProductOrder');
    Route::post('/update/purchase-product/order', [ProductPurchaseOrderController::class, 'updatePurchaseProductOrder'])->name('UpdatePurchaseProductOrder');
    Route::get('/print-purchase-barcode/{purchase_id}', [ProductPurchaseOrderController::class, 'printPurchaseBarcode'])->name('PrintPurchaseBarcode');
    Route::get('/api/purchase-barcode-units/{purchase_id}', [ProductPurchaseOrderController::class, 'apiGetPurchaseBarcodeUnits'])->name('ApiGetPurchaseBarcodeUnits');
    Route::post('/api/purchase-barcode-unit/update-code', [ProductPurchaseOrderController::class, 'apiUpdateBarcodeUnitCode'])->name('ApiUpdateBarcodeUnitCode');


    // purchase product order routes
    Route::get('/add/new/purchase-return/order', [ProductPurchasReturnController::class, 'addNewPurchaseReturnOrder'])->name('AddNewPurchaseReturnOrder');
    Route::post('/save/new/purchase-return/order', [ProductPurchasReturnController::class, 'saveNewPurchaseReturnOrder'])->name('SaveNewPurchaseReturnOrder');
    Route::get('/view/all/purchase-return/order', [ProductPurchasReturnController::class, 'viewAllPurchaseReturnOrder'])->name('ViewAllPurchaseReturnOrder');
    Route::get('/delete/purchase-return/order/{slug}', [ProductPurchasReturnController::class, 'deletePurchaseReturnOrder'])->name('DeletePurchaseReturnOrder');
    Route::get('/edit/purchase-return/order/{slug}', [ProductPurchasReturnController::class, 'editPurchaseReturnOrder'])->name('EditPurchaseReturnOrder');
    Route::get('/edit/purchase-return/order/confirm/{slug}', [ProductPurchasReturnController::class, 'editPurchaseReturnOrderConfirm'])->name('EditPurchaseReturnOrderConfirm');
    Route::get('api/edit/purchase-return/order/{slug}', [ProductPurchasReturnController::class, 'apiEditPurchaseReturn'])->name('ApiEditPurchaseReturnOrder');
    Route::post('/update/purchase-return/order', [ProductPurchasReturnController::class, 'updatePurchaseReturnOrder'])->name('UpdatePurchaseReturnOrder');

    // purchase product other charge
    Route::get('/add/new/purchase-product/charge', [ProductPurchaseChargeController::class, 'addNewPurchaseProductCharge'])->name('AddNewPurchaseProductCharge');
    Route::post('/save/new/purchase-product/charge', [ProductPurchaseChargeController::class, 'saveNewPurchaseProductCharge'])->name('SaveNewPurchaseProductCharge');
    Route::get('/view/all/purchase-product/charge', [ProductPurchaseChargeController::class, 'viewAllPurchaseProductCharge'])->name('ViewAllPurchaseProductCharge');
    Route::get('/delete/purchase-product/charge/{slug}', [ProductPurchaseChargeController::class, 'deletePurchaseProductCharge'])->name('DeletePurchaseProductCharge');
    Route::get('/edit/purchase-product/charge/{slug}', [ProductPurchaseChargeController::class, 'editPurchaseProductCharge'])->name('EditPurchaseProductCharge');
    Route::post('/update/purchase-product/charge', [ProductPurchaseChargeController::class, 'updatePurchaseProductCharge'])->name('UpdatePurchaseProductCharge');

    // Customer 
    Route::get('/add/new/customers', [CustomerController::class, 'addNewCustomer'])->name('AddNewCustomers');
    Route::post('/save/new/customers', [CustomerController::class, 'saveNewCustomer'])->name('SaveNewCustomers');
    Route::get('/view/all/customer', [CustomerController::class, 'viewAllCustomer'])->name('ViewAllCustomer');
    Route::get('/delete/customers/{slug}', [CustomerController::class, 'deleteCustomer'])->name('DeleteCustomers');
    Route::get('/edit/customers/{slug}', [CustomerController::class, 'editCustomer'])->name('EditCustomers');
    Route::post('/update/customers', [CustomerController::class, 'updateCustomer'])->name('UpdateCustomers');
    Route::post('/customers/store', [CustomerController::class, 'customer_store']);
    Route::get('/customers/{user_id?}', [CustomerController::class, 'customers']);

    // generate report
    Route::get('/product/purchase/report', [ReportController::class, 'productPurchaseReport'])->name('productPurchaseReport');
    Route::post('/generate/product/purchase/report', [ReportController::class, 'generateProductPurchaseReport'])->name('generateProductPurchaseReport');

    // product order management

    Route::get('/add/new/product-order/manage', [ProductOrderController::class, 'addNewProductOrder'])->name('AddNewProductOrder');
    Route::post('/save/new/product-order/manage', [ProductOrderController::class, 'saveNewProductOrder'])->name('SaveNewProductOrder');
    Route::get('/view/all/product-order/manage', [ProductOrderController::class, 'viewAllProductOrder'])->name('ViewAllProductOrder');
    Route::get('/delete/product-order/manage/{slug}', [ProductOrderController::class, 'deleteProductOrder'])->name('DeleteProductOrder');
    Route::get('/edit/product-order/manage/{slug}', [ProductOrderController::class, 'editProductOrder'])->name('EditProductOrder');
    Route::get('/edit/product-order/manage/confirm/{slug}', [ProductOrderController::class, 'editProductOrderConfirm'])->name('EditProductOrderConfirm');
    Route::get('api/edit/product-order/manage/{slug}', [ProductOrderController::class, 'apiEditProduct'])->name('ApiEditProductOrder');
    Route::post('/update/product-order/manage', [ProductOrderController::class, 'updateProductOrder'])->name('UpdateProductOrder');
    Route::get('/show/product-order/manage/{slug}', [ProductOrderController::class, 'showProductOrder'])->name('ShowProductOrder');
    Route::get('/pay-due/product-order/manage/{slug}', [ProductOrderController::class, 'payDueProductOrder'])->name('PayDueProductOrder');
    Route::post('/process-payment/product-order/manage/{slug}', [ProductOrderController::class, 'processPaymentProductOrder'])->name('ProcessPaymentProductOrder');
    Route::get('/print/product-order/manage/{slug}', [ProductOrderController::class, 'printProductOrder'])->name('PrintProductOrder');
    Route::get('/return/product-order/manage/{slug}', [ProductOrderController::class, 'returnProductOrder'])->name('ReturnProductOrder');
    Route::post('/process-return/product-order/manage', [ProductOrderController::class, 'processReturnProductOrder'])->name('ProcessReturnProductOrder');
    
    // Courier Management Routes
    Route::get('/product-order/{id}/courier', [\App\Http\Controllers\Courier\CourierController::class, 'showCourier'])->name('ShowCourierOrder');
    
    /** get customer due */
    Route::get('/api/customer-payment-info/{customer_id}', [ProductOrderController::class, 'getCustomerPaymentUpdate'])->name('GetCustomerPaymentInfo');
    
    // Delivery Information Routes
    Route::get('/api/districts', [ProductOrderController::class, 'getDistricts'])->name('GetDistricts');
    Route::get('/api/upazilas/{district_id}', [ProductOrderController::class, 'getUpazilas'])->name('GetUpazilas');
    Route::get('/api/customer-delivery-info/{customer_id}', [ProductOrderController::class, 'getCustomerDeliveryInfo'])->name('GetCustomerDeliveryInfo');
    Route::post('/api/customer-delivery-info/{customer_id}', [ProductOrderController::class, 'saveCustomerDeliveryInfo'])->name('SaveCustomerDeliveryInfo');

    // Product Order Return Management
    Route::get('/view/all/product-order-returns', [ProductOrderReturnController::class, 'index'])->name('ViewAllProductOrderReturns');
    Route::get('/create/product-order-return/{slug}', [ProductOrderReturnController::class, 'create'])->name('CreateProductOrderReturn');
    Route::post('/store/product-order-return', [ProductOrderReturnController::class, 'store'])->name('StoreProductOrderReturn');
    Route::get('/show/product-order-return/{slug}', [ProductOrderReturnController::class, 'show'])->name('ShowProductOrderReturn');
    Route::get('/edit/product-order-return/{slug}', [ProductOrderReturnController::class, 'edit'])->name('EditProductOrderReturn');
    Route::post('/update/product-order-return/{slug}', [ProductOrderReturnController::class, 'update'])->name('UpdateProductOrderReturn');
    Route::get('/delete/product-order-return/{slug}', [ProductOrderReturnController::class, 'destroy'])->name('DeleteProductOrderReturn');
    Route::get('/print/product-order-return/{slug}', [ProductOrderReturnController::class, 'printReturn'])->name('PrintProductOrderReturn');
    
    // API endpoints for return history and original invoice
    Route::get('/api/return-history/{order_id}', [ProductOrderReturnController::class, 'getReturnHistory'])->name('GetReturnHistory');
    Route::get('/api/original-invoice/{order_id}', [ProductOrderReturnController::class, 'getOriginalInvoice'])->name('GetOriginalInvoice');

    // Customer Payment Management
    Route::get('/customer-payment-create/{order_id}', [CustomerPaymentController::class, 'createWithOrder'])->name('CreateCustomerPaymentWithOrder');
    Route::get('/customer-payment-create', [CustomerPaymentController::class, 'create'])->name('CreateCustomerPayment');
    Route::post('/customer-payment-store', [CustomerPaymentController::class, 'store'])->name('StoreCustomerPayment');
    Route::get('/customer-payment-return', [CustomerPaymentController::class, 'createReturn'])->name('CreateCustomerPaymentReturn');
    Route::post('/customer-payment-return-process', [CustomerPaymentController::class, 'processReturn'])->name('ProcessCustomerPaymentReturn');
    Route::get('/customer-payments', [CustomerPaymentController::class, 'index'])->name('ViewAllCustomerPayments');
    Route::get('/customer-payment-history/{customer_id}', [CustomerPaymentController::class, 'history'])->name('ViewCustomerPaymentHistory');

    // Supplier Payment Management
    Route::get('/supplier-payments', [\App\Http\Controllers\Account\SupplierPaymentController::class, 'index'])->name('ViewAllSupplierPayments');
    Route::get('/supplier-payment-create-due/{supplier_id?}', [\App\Http\Controllers\Account\SupplierPaymentController::class, 'createDue'])->name('CreateSupplierPaymentDue');
    Route::get('/supplier-payment-create-advance/{supplier_id?}', [\App\Http\Controllers\Account\SupplierPaymentController::class, 'createAdvance'])->name('CreateSupplierPaymentAdvance');
    Route::get('/api/supplier-due-purchases/{supplier_id}', [\App\Http\Controllers\Account\SupplierPaymentController::class, 'getSupplierDuePurchases'])->name('GetSupplierDuePurchases');
    Route::get('/api/account-balance/{payment_type_id}', [\App\Http\Controllers\Account\SupplierPaymentController::class, 'getAccountBalance'])->name('GetAccountBalance');
    Route::post('/supplier-payment-store', [\App\Http\Controllers\Account\SupplierPaymentController::class, 'store'])->name('StoreSupplierPayment');
    Route::get('/supplier-payments-list/{supplier_id?}', [\App\Http\Controllers\Account\SupplierPaymentController::class, 'viewPayments'])->name('ViewSupplierPayments');
    
    // API endpoint for customer due orders
    Route::get('/api/customer-due-orders/{customer_id}', [CustomerPaymentController::class, 'getCustomerDueOrders'])->name('GetCustomerDueOrders');

    // Manual Product Return Management
    Route::get('/product-return-manual', [ManualProductReturnController::class, 'create'])->name('CreateManualProductReturn');
    Route::post('/product-return-manual-store', [ManualProductReturnController::class, 'store'])->name('StoreManualProductReturn');
    Route::get('/product-return-manual-list', [ManualProductReturnController::class, 'index'])->name('ViewAllManualProductReturns');
    Route::get('/product-return-manual-show/{slug}', [ManualProductReturnController::class, 'show'])->name('ShowManualProductReturn');
    Route::get('/product-return-manual-edit/{slug}', [ManualProductReturnController::class, 'edit'])->name('EditManualProductReturn');
    Route::post('/product-return-manual-update/{slug}', [ManualProductReturnController::class, 'update'])->name('UpdateManualProductReturn');
    Route::get('/product-return-manual-delete/{slug}', [ManualProductReturnController::class, 'destroy'])->name('DeleteManualProductReturn');

    // Order List Management (Vue + pagination, default order_source=pos)
    Route::get('/product-order/list', [ProductOrderController::class, 'orderListPage'])->name('OrderListPage');
});
