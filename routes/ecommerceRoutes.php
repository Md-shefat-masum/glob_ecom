<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PosController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\WishListController;
use App\Http\Controllers\PromoCodeController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProductColorController;
use App\Http\Controllers\ChildCategoryController;
use App\Http\Controllers\DeliveryChargeController;
use App\Http\Controllers\ProductSizeValueController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\PackageProductController;



require 'configRoutes.php';

require 'systemRoutes.php';

Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

    // category routes
    Route::get('/add/new/category', [CategoryController::class, 'addNewCategory'])->name('AddNewCategory');
    Route::post('/save/new/category', [CategoryController::class, 'saveNewCategory'])->name('SaveNewCategory');
    Route::get('/view/all/category', [CategoryController::class, 'viewAllCategory'])->name('ViewAllCategory');
    Route::get('/delete/category/{slug}', [CategoryController::class, 'deleteCategory'])->name('DeleteCategory');
    Route::get('/feature/category/{slug}', [CategoryController::class, 'featureCategory'])->name('FeatureCategory');
    Route::get('/edit/category/{slug}', [CategoryController::class, 'editCategory'])->name('EditCategory');
    Route::post('/update/category', [CategoryController::class, 'updateCategory'])->name('UpdateCategory');
    Route::get('/rearrange/category', [CategoryController::class, 'rearrangeCategory'])->name('RearrangeCategory');
    Route::post('/save/rearranged/order', [CategoryController::class, 'saveRearrangeCategoryOrder'])->name('SaveRearrangeCategoryOrder');


    // subcategory routes
    Route::get('/add/new/subcategory', [SubcategoryController::class, 'addNewSubcategory'])->name('AddNewSubcategory');
    Route::post('/save/new/subcategory', [SubcategoryController::class, 'saveNewSubcategory'])->name('SaveNewSubcategory');
    Route::get('/view/all/subcategory', [SubcategoryController::class, 'viewAllSubcategory'])->name('ViewAllSubcategory');
    Route::get('/delete/subcategory/{slug}', [SubcategoryController::class, 'deleteSubcategory'])->name('DeleteSubcategory');
    Route::get('/feature/subcategory/{id}', [SubcategoryController::class, 'featureSubcategory'])->name('FeatureSubcategory');
    Route::get('/edit/subcategory/{slug}', [SubcategoryController::class, 'editSubcategory'])->name('EditSubcategory');
    Route::post('/update/subcategory', [SubcategoryController::class, 'updateSubcategory'])->name('UpdateSubcategory');
    Route::get('/rearrange/subcategory', [SubcategoryController::class, 'rearrangeSubcategory'])->name('RearrangeSubcategory');
    Route::post('/save/rearranged/subcategory', [SubcategoryController::class, 'saveRearrangedSubcategory'])->name('SaveRearrangedSubcategory');


    // childcategory routes
    Route::get('/add/new/childcategory', [ChildCategoryController::class, 'addNewChildcategory'])->name('AddNewChildcategory');
    Route::post('/category/wise/subcategory', [ChildCategoryController::class, 'subcategoryCategoryWise'])->name('SubcategoryCategoryWise');
    Route::post('/save/new/childcategory', [ChildCategoryController::class, 'saveNewChildcategory'])->name('SaveNewChildcategory');
    Route::get('/view/all/childcategory', [ChildCategoryController::class, 'viewAllChildcategory'])->name('ViewAllChildcategory');
    Route::get('/delete/childcategory/{slug}', [ChildCategoryController::class, 'deleteChildcategory'])->name('DeleteChildcategory');
    Route::get('/edit/childcategory/{slug}', [ChildCategoryController::class, 'editChildcategory'])->name('EditChildcategory');
    Route::post('/update/childcategory', [ChildCategoryController::class, 'updateChildcategory'])->name('UpdateChildcategory');


    // product routes
    Route::get('/add/new/product', [ProductController::class, 'addNewProduct'])->name('AddNewProduct');
    Route::post('/subcategory/wise/childcategory', [ProductController::class, 'childcategorySubcategoryWise'])->name('ChildcategorySubcategoryWise');
    Route::post('/save/new/product', [ProductController::class, 'saveNewProduct'])->name('SaveNewProduct');
    Route::get('/view/all/product', [ProductController::class, 'viewAllProducts'])->name('ViewAllProducts');
    Route::get('/delete/product/{slug}', [ProductController::class, 'deleteProduct'])->name('DeleteProduct');
    Route::get('/edit/product/{slug}', [ProductController::class, 'editProduct'])->name('EditProduct');
    Route::post('/update/product', [ProductController::class, 'updateProduct'])->name('UpdateProduct');
    Route::post('/add/another/variant', [ProductController::class, 'addAnotherVariant'])->name('AddAnotherVariant');
    Route::get('/delete/product/variant/{id}', [ProductController::class, 'deleteProductVariant'])->name('DeleteProductVariant');
    Route::get('/products/from/excel', [ProductController::class, 'productsFromExcel'])->name('ProductsFromExcel');
    Route::post('/upload/product/from/excel', [ProductController::class, 'uploadProductsFromExcel'])->name('UploadProductsFromExcel');

    // Product Color Management
    Route::get('/add/new/product-color', [ProductColorController::class, 'addNewProductColor'])->name('AddNewProductColor');
    Route::post('/save/new/product-color', [ProductColorController::class, 'saveNewProductColor'])->name('SaveNewProductColor');
    Route::get('/view/all/product-color', [ProductColorController::class, 'viewAllProductColor'])->name('ViewAllProductColor');
    Route::get('/delete/product-color/{slug}', [ProductColorController::class, 'deleteProductColor'])->name('DeleteProductColor');
    Route::get('/edit/product-color/{slug}', [ProductColorController::class, 'editProductColor'])->name('EditProductColor');
    Route::post('/update/product-color', [ProductColorController::class, 'updateProductColor'])->name('UpdateProductColor');

    // Product Size Management
    Route::get('/add/new/product-size', [ProductSizeController::class, 'addNewProductSize'])->name('AddNewProductSize');
    Route::post('/save/new/product-size', [ProductSizeController::class, 'saveNewProductSize'])->name('SaveNewProductSize');
    Route::get('/view/all/product-size', [ProductSizeController::class, 'viewAllProductSize'])->name('ViewAllProductSize');
    Route::get('/delete/product-size/{slug}', [ProductSizeController::class, 'deleteProductSize'])->name('DeleteProductSize');
    Route::get('/edit/product-size/{slug}', [ProductSizeController::class, 'editProductSize'])->name('EditProductSize');
    Route::post('/update/product-size', [ProductSizeController::class, 'updateProductSize'])->name('UpdateProductSize');

    // Product Size Value Management
    Route::get('/add/new/product-size-value', [ProductSizeValueController::class, 'addNewProductSizeValue'])->name('AddNewProductSizeValue');
    Route::post('/save/new/product-size-value', [ProductSizeValueController::class, 'saveNewProductSizeValue'])->name('SaveNewProductSizeValue');
    Route::get('/view/all/product-size-value', [ProductSizeValueController::class, 'viewAllProductSizeValue'])->name('ViewAllProductSizeValue');
    Route::get('/delete/product-size-value/{slug}', [ProductSizeValueController::class, 'deleteProductSizeValue'])->name('DeleteProductSizeValue');
    Route::get('/edit/product-size-value/{slug}', [ProductSizeValueController::class, 'editProductSizeValue'])->name('EditProductSizeValue');
    Route::post('/update/product-size-value', [ProductSizeValueController::class, 'updateProductSizeValue'])->name('UpdateProductSizeValue');


    // product review
    Route::get('/view/product/reviews', [ProductController::class, 'viewAllProductReviews'])->name('ViewAllProductReviews');
    Route::get('/approve/product/review/{slug}', [ProductController::class, 'approveProductReview'])->name('ApproveProductReview');
    Route::get('/delete/product/review/{slug}', [ProductController::class, 'deleteProductReview'])->name('DeleteProductReview');
    Route::get('/get/product/review/info/{id}', [ProductController::class, 'getProductReviewInfo'])->name('GetProductReviewInfo');
    Route::post('/submit/reply/product/review', [ProductController::class, 'submitReplyOfProductReview'])->name('SubmitReplyOfProductReview');


    // product question answer
    Route::get('/view/product/question/answer', [ProductController::class, 'viewAllQuestionAnswer'])->name('ViewAllQuestionAnswer');
    Route::get('/delete/question/answer/{id}', [ProductController::class, 'deleteQuestionAnswer'])->name('DeleteQuestionAnswer');
    Route::get('/get/question/answer/info/{id}', [ProductController::class, 'getQuestionAnswerInfo'])->name('GetQuestionAnswerInfo');
    Route::post('/submit/question/answer', [ProductController::class, 'submitAnswerOfQuestion'])->name('SubmitAnswerOfQuestion');



    // order routes
    Route::get('/view/orders', [OrderController::class, 'viewAllOrders'])->name('ViewAllOrders');
    Route::get('/view/trash/orders', [OrderController::class, 'viewAllTrashedOrders'])->name('viewAllTrashedOrders');
    Route::get('/restore/orders/{slug}', [OrderController::class, 'RestoreOrder'])->name('RestoreOrder');
    Route::get('/view/pending/orders', [OrderController::class, 'viewPendigOrders'])->name('ViewPendigOrders');
    Route::get('/view/approved/orders', [OrderController::class, 'viewApprovedOrders'])->name('ViewApprovedOrders');
    Route::get('/view/delivered/orders', [OrderController::class, 'viewDeliveredOrders'])->name('ViewDeliveredOrders');
    Route::get('/view/cancelled/orders', [OrderController::class, 'viewCancelledOrders'])->name('ViewCancelledOrders');
    Route::get('/view/picked/orders', [OrderController::class, 'viewPickedOrders'])->name('ViewPickedOrders');
    Route::get('/view/intransit/orders', [OrderController::class, 'viewIntransitOrders'])->name('ViewIntransitOrders');
    Route::get('/view/dispatch/orders', [OrderController::class, 'viewAllDispatchOrders'])->name('viewDispatchOrders');
    Route::get('/order/details/{slug}', [OrderController::class, 'orderDetails'])->name('OrderDetails');
    Route::get('/cancel/order/{slug}', [OrderController::class, 'cancelOrder'])->name('CancelOrder');
    Route::get('/approve/order/{slug}', [OrderController::class, 'approveOrder'])->name('ApproveOrder');
    Route::get('/intransit/order/{slug}', [OrderController::class, 'intransitOrder'])->name('IntransitOrder');
    Route::get('/deliver/order/{slug}', [OrderController::class, 'deliverOrder'])->name('DeliverOrder');
    Route::post('/order/info/update', [OrderController::class, 'orderInfoUpdate'])->name('OrderInfoUpdate');
    Route::get('/order/edit/{slug}', [OrderController::class, 'orderEdit'])->name('OrderEdit');
    Route::post('/order/update', [OrderController::class, 'orderUpdate'])->name('OrderUpdate');
    Route::post('/add/more/product', [OrderController::class, 'addMoreProduct'])->name('AddMoreProduct');
    Route::post('/get/product/variants', [OrderController::class, 'getProductVariants'])->name('GetProductVariants');
    Route::get('delete/order/{slug}', [OrderController::class, 'deleteOrder'])->name('DeleteOrder');

    // pos routes
    Route::get('/create/new/order', [PosController::class, 'createNewOrder'])->name('CreateNewOrder');
    Route::post('/product/live/search', [PosController::class, 'productLiveSearch'])->name('ProductLiveSearch');
    Route::post('/get/pos/product/variants', [PosController::class, 'getProductVariantsPos'])->name('GetProductVariantsPos');
    Route::post('/check/pos/product/variant', [PosController::class, 'checkProductVariant'])->name('CheckProductVariant');
    Route::post('/add/to/cart', [PosController::class, 'addToCart'])->name('AddToCart');
    Route::get('/remove/cart/item/{index}', [PosController::class, 'removeCartItem'])->name('RemoveCartItem');
    Route::get('/update/cart/item/{index}/{qty}', [PosController::class, 'updateCartItem'])->name('UpdateCartItem');
    Route::get('/update/cart/discount/{index}/{discount}', [PosController::class, 'updateCartItemDiscount'])->name('UpdateCartItemDiscount');
    Route::post('/save/new/customer', [PosController::class, 'saveNewCustomer'])->name('SaveNewCustomer');
    Route::get('/update/order/total/{shipping_charge}/{discount}', [PosController::class, 'updateOrderTotal'])->name('UpdateOrderTotal');
    Route::post('/apply/coupon', [PosController::class, 'applyCoupon'])->name('ApplyCoupon');
    Route::post('/remove/coupon', [PosController::class, 'removeCoupon'])->name('RemoveCoupon');
    Route::post('district/wise/thana', [PosController::class, 'districtWiseThana'])->name('DistrictWiseThana');
    Route::post('district/wise/thana/by/name', [PosController::class, 'districtWiseThanaByName'])->name('DistrictWiseThanaByName');
    Route::post('save/pos/customer/address', [PosController::class, 'saveCustomerAddress'])->name('SaveCustomerAddress');
    Route::get('get/saved/address/{user_id}', [PosController::class, 'getSavedAddress'])->name('GetSavedAddress');
    Route::post('change/delivery/method', [PosController::class, 'changeDeliveryMethod'])->name('ChangeDeliveryMethod');
    Route::post('place/order', [PosController::class, 'placeOrder'])->name('PlaceOrder');
    // Route::get('/edit/place/order/{slug}', [PosController::class, 'editPlaceOrder'])->name('EditPlaceOrder');
    // Route::post('/update/place/order', [PosController::class, 'updatePlaceOrder'])->name('UpdatePlaceOrder');

    // POS Invoice Print Route
    Route::get('/pos/invoice/print/{id}', [InvoiceController::class, 'posInvoicePrint'])->name('POSInvoicePrint');
    Route::get('/pos/invoice/content/{id}', [InvoiceController::class, 'getPrintableContent'])->name('POSInvoiceContent');
    Route::get('/view/all/invoices', [InvoiceController::class, 'index'])->name('ViewAllInvoices');
    Route::get('/invoice/show/{id}', [InvoiceController::class, 'showInvoice'])->name('ShowInvoice');
    Route::get('/invoice/print/{id}', [InvoiceController::class, 'printInvoice'])->name('PrintInvoice');
    Route::post('/invoice/generate/{id}', [InvoiceController::class, 'generateInvoice'])->name('GenerateInvoice');

    // promo codes
    Route::get('/add/new/code', [PromoCodeController::class, 'addPromoCode'])->name('AddPromoCode');
    Route::post('/save/promo/code', [PromoCodeController::class, 'savePromoCode'])->name('SavePromoCode');
    Route::get('/view/all/promo/codes', [PromoCodeController::class, 'viewAllPromoCodes'])->name('ViewAllPromoCodes');
    Route::get('/edit/promo/code/{slug}', [PromoCodeController::class, 'editPromoCode'])->name('EditPromoCode');
    Route::post('/update/promo/code', [PromoCodeController::class, 'updatePromoCode'])->name('UpdatePromoCode');
    Route::get('/remove/promo/code/{slug}', [PromoCodeController::class, 'removePromoCode'])->name('RemovePromoCode');

    // wishlist routes
    Route::get('/view/customers/wishlist', [WishListController::class, 'customersWishlist'])->name('CustomersWishlist');

    // push notification
    Route::get('/send/notification/page', [NotificationController::class, 'sendNotificationPage'])->name('SendNotificationPage');
    Route::get('/view/all/notifications', [NotificationController::class, 'viewAllNotifications'])->name('ViewAllNotifications');
    Route::get('/delete/notification/{id}', [NotificationController::class, 'deleteNotification'])->name('DeleteNotification');
    Route::get('/delete/notification/with/range', [NotificationController::class, 'deleteNotificationRangeWise'])->name('DeleteNotificationRangeWise');
    Route::post('/send/push/notification', [NotificationController::class, 'sendPushNotification'])->name('SendPushNotification');


    // delivery charges
    Route::get('/view/delivery/charges', [DeliveryChargeController::class, 'viewAllDeliveryCharges'])->name('ViewAllDeliveryCharges');
    Route::get('/get/delivery/charge/{id}', [DeliveryChargeController::class, 'getDeliveryCharge'])->name('GetDeliveryCharge');
    Route::post('/update/delivery/charge', [DeliveryChargeController::class, 'updateDeliveryCharge'])->name('UpdateDeliveryCharge');

    // upazaila thana
    Route::get('view/upazila/thana', [DeliveryChargeController::class, 'viewUpazilaThana'])->name('ViewUpazilaThana');
    Route::get('get/upazila/info/{id}', [DeliveryChargeController::class, 'getUpazilaInfo'])->name('getUpazilaInfo');
    Route::post('update/upazila/info', [DeliveryChargeController::class, 'updateUpazilaInfo'])->name('UpdateUpazilaInfo');
    Route::post('save/new/upazila', [DeliveryChargeController::class, 'saveNewUpazila'])->name('SaveNewUpazila');
    Route::get('delete/upazila/{id}', [DeliveryChargeController::class, 'deleteUpazila'])->name('DeleteUpazila');

    // Package Product routes
    Route::get('package-products', [PackageProductController::class, 'index'])->name('PackageProducts.Index');
    Route::get('package-products/data', [PackageProductController::class, 'getData'])->name('PackageProducts.Data');
    Route::get('package-products/create', [PackageProductController::class, 'create'])->name('PackageProducts.Create');
    Route::get('package-products/catalog/search', [PackageProductController::class, 'searchProducts'])->name('PackageProducts.Search');
    Route::get('package-products/product/{productId}/matrix', [PackageProductController::class, 'productMatrix'])->name('PackageProducts.ProductMatrix');
    Route::post('package-products', [PackageProductController::class, 'store'])->name('PackageProducts.Store');
    Route::get('package-products/{id}/edit', [PackageProductController::class, 'edit'])->name('PackageProducts.Edit');
    Route::put('package-products/{id}', [PackageProductController::class, 'update'])->name('PackageProducts.Update');
    Route::delete('package-products/{id}', [PackageProductController::class, 'destroy'])->name('PackageProducts.Destroy');
    Route::get('package-products/{id}/manage-items', [PackageProductController::class, 'manageItems'])->name('PackageProducts.ManageItems');
    Route::post('package-products/{id}/add-item', [PackageProductController::class, 'addItem'])->name('PackageProducts.AddItem');
    Route::put('package-products/{packageId}/items/{itemId}', [PackageProductController::class, 'updateItem'])->name('PackageProducts.UpdateItem');
    Route::delete('package-products/{packageId}/items/{itemId}', [PackageProductController::class, 'removeItem'])->name('PackageProducts.RemoveItem');
    Route::get('get-product-variants/{productId}', [PackageProductController::class, 'getProductVariants'])->name('GetProductVariants');
    Route::post('get-variant-stock/{productId}', [PackageProductController::class, 'getVariantStock'])->name('GetVariantStock');

    // generate report
    Route::get('sales/report', [ReportController::class, 'salesReport'])->name('SalesReport');
    Route::post('generate/sales/report', [ReportController::class, 'generateSalesReport'])->name('GenerateSalesReport');
    Route::get('/view/payment/history', [HomeController::class, 'viewPaymentHistory'])->name('ViewPaymentHistory');
});
