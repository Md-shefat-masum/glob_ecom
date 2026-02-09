<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthenticationController;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SupportController;
use App\Http\Controllers\Api\FilterController;
use App\Http\Controllers\Api\ProductController;

Route::group(['namespace' => 'Api'], function () {

    // authentication api | middleware(['throttle:5,1']) means 5 requests can be made in 1 minute
    Route::post('user/registration', [AuthenticationController::class, 'userRegistration']); //->middleware(['throttle:5,1']);
    Route::post('user/verification', [AuthenticationController::class, 'userVerification']);
    Route::post('user/login', [AuthenticationController::class, 'userLogin']);
    Route::post('forget/password', [AuthenticationController::class, 'forgetPassword']); // forget password api
    Route::post('verify/reset/code', [AuthenticationController::class, 'verifyResetCode']); // forget password api
    Route::post('change/password', [AuthenticationController::class, 'changePassword']); // forget password api

    // available social login credentials
    Route::post('social/login/credentials', [AuthenticationController::class, 'socialLoginCredentials']);
    Route::post('social/login', [AuthenticationController::class, 'socialLogin']);

    Route::post('subscribe/for/updates', [ApiController::class, 'subscriptionForUpdates']);
    Route::post('upload/profile/photo', [ProfileController::class, 'uploadProfilePhoto']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('user/profile/info', [ApiController::class, 'userProfileInfo']);
        Route::post('user/profile/update', [ApiController::class, 'userProfileUpdate']); //for app only

        Route::post('user/profile/update/web', [ProfileController::class, 'userProfileUpdateWeb']); //for web only
        Route::post('user/change/password/web', [ProfileController::class, 'userChangePasswordWeb']); //for web only
        Route::get('send/account/delete/request', [ProfileController::class, 'sendAccountDeleteRequest']);

        // user payment card api
        Route::post('add/new/card', [ProfileController::class, 'addNewCard']);
        Route::get('get/my/cards', [ProfileController::class, 'getMyCards']);
        Route::post('update/my/card', [ProfileController::class, 'updateMyCard']);
        Route::post('delete/my/card', [ProfileController::class, 'deleteMyCard']);

        // user multiple address api
        Route::post('add/new/address', [ProfileController::class, 'addNewAddress']);
        Route::get('get/all/address', [ProfileController::class, 'getAllAddress']);
        Route::post('update/my/address', [ProfileController::class, 'updateMyAddress']);
        Route::post('delete/my/address', [ProfileController::class, 'deleteMyAddress']);

        // product review submit
        Route::post('product/review/submit', [ApiController::class, 'submitProductReview']);
    });
    Route::post('product/question/submit', [ApiController::class, 'submitProductQuestion']);

    Route::get('get/category/tree', [ApiController::class, 'getCategoryTree']);
    Route::get('category/list', [ApiController::class, 'getCategoryList']);
    Route::get('get/featured/subcategories', [ApiController::class, 'getFeaturedSubcategory']);
    Route::post('subcategory/of/category', [ApiController::class, 'getSubcategoryOfCategory']);
    Route::post('childcategory/of/subcategory', [ApiController::class, 'getChildcategoryOfSubcategory']);
    Route::get('get/all/products', [ApiController::class, 'getAllProducts']);
    Route::post('get/related/products', [ApiController::class, 'getRelatedProducts']);
    Route::post('get/you/may/like/products', [ApiController::class, 'getYouMayLikeProducts']);
    Route::post('category/wise/products', [ApiController::class, 'categoryWiseProducts']);
    Route::post('subcategory/wise/products', [ApiController::class, 'subcategoryWiseProducts']);
    Route::post('childcategory/wise/products', [ApiController::class, 'childcategoryWiseProducts']);
    Route::get('product/details/{id}', [ApiController::class, 'productDetails']);
    Route::post('flag/wise/products', [ApiController::class, 'flagWiseProducts']);
    Route::get('featured/flag/wise/products', [ApiController::class, 'featuredFlagWiseProducts']);
    Route::post('flag/wise/all/products', [ApiController::class, 'flagWiseAllProducts']);
    Route::get('featured/brand/wise/products', [ApiController::class, 'featuredBrandWiseProducts']);
    Route::get('get/all/flags', [ApiController::class, 'getAllFlags']);
    Route::get('get/all/brands', [ApiController::class, 'getAllBrands']);
    Route::post('search/products', [ApiController::class, 'searchProducts']);
    Route::get('search/products', [ApiController::class, 'searchProductsGet']);
    Route::post('live/search/products', [ApiController::class, 'searchLiveProducts']);
    Route::get('get/terms/and/condition', [ApiController::class, 'termsAndCondition']);
    Route::get('get/privacy/policy', [ApiController::class, 'privacyPolicy']);
    Route::get('get/shipping/policy', [ApiController::class, 'shippingPolicy']);
    Route::get('get/return/policy', [ApiController::class, 'returnPolicy']);
    Route::get('get/about/us', [ApiController::class, 'aboutUs']);
    Route::get('get/all/faq', [ApiController::class, 'getAllFaq']);
    Route::get('general/info', [ApiController::class, 'generalInfo']);
    Route::get('get/all/sliders', [ApiController::class, 'getAllSliders']);
    Route::get('get/all/banners', [ApiController::class, 'getAllBanners']);
    Route::get('get/promotional/banner', [ApiController::class, 'getPromotionalBanner']);
    Route::post('submit/contact/us/request', [ApiController::class, 'submitContactRequest']);
    Route::get('get/all/testimonials', [ApiController::class, 'getAllTestimonials']);
    Route::get('get/payment/gateways', [ApiController::class, 'getPaymentGateways']);
    Route::post('order/preview', [ApiController::class, 'orderPreview']);
    Route::get('get/delivery/charge/{district}', [ApiController::class, 'getdeliveryCharge']);

    // new api for districts and thana
    Route::get('get/all/districts', [ApiController::class, 'getAllDistricts']);
    Route::post('district/wise/thana', [ApiController::class, 'getDistrictWiseThana']);
    Route::get('get/districts/with/thana', [ApiController::class, 'getDistrictsWithThana']);

    // unique api
    Route::get('best/selling/product', [ApiController::class, 'bestSellingProduct']);
    Route::get('products/for/you/with/login', [ApiController::class, 'productsForYouLoggedIn'])->middleware('auth:sanctum');
    Route::get('products/for/you', [ApiController::class, 'productsForYou']);

    // order api start
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('order/checkout', [ApiController::class, 'orderCheckout']);
        Route::post('order/checkout/app/only', [ApiController::class, 'orderCheckoutAppOnly']);
        Route::get('get/my/orders', [ApiController::class, 'getMyOrders']);
        Route::get('order/details/{slug}', [ApiController::class, 'orderDetails']);
    });
    Route::post('order/progress', [ApiController::class, 'orderProgress']);
    Route::post('guest/order/checkout', [ApiController::class, 'guestOrderCheckout']); // for guest
    Route::post('submit/shipping/billing/info', [ApiController::class, 'shippingBillingInfo']);
    Route::post('order/payment/cod', [ApiController::class, 'orderCashOnDelivery']);
    // order api end

    // cart & checkout api for app only start
    Route::post('add/to/cart', [CartController::class, 'addToCart']);
    Route::post('incr/cart/qty', [CartController::class, 'incrCartQty']);
    Route::post('decr/cart/qty', [CartController::class, 'decrCartQty']);
    Route::post('delete/cart/item', [CartController::class, 'deleteCartItem']);
    Route::post('get/cart/items', [CartController::class, 'getCartItems']);

    Route::get('get/all/coupons', [CartController::class, 'getAllCoupons']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('apply/coupon', [CartController::class, 'applyCoupon']);
        Route::post('order/cart/checkout', [CartController::class, 'cartCheckout']);
        Route::post('order/checkout/buy/now/app', [CartController::class, 'checkoutBuyNow']); // for app only

        // wishlists
        Route::post('add/to/wishlist', [CartController::class, 'addToWishList']);
        Route::get('get/my/wishlist', [CartController::class, 'getMyWishList']);
        Route::post('delete/my/wishlist', [CartController::class, 'deleteMyWishList']);
    });
    Route::post('guest/order/checkout/buy/now/app', [CartController::class, 'guestCartCheckoutBuyNow']);
    Route::post('guest/order/cart/checkout', [CartController::class, 'guestCartCheckout']);
    // cart & checkout api for app only end

    // support ticket api routes
    Route::post('upload/support/ticket/file', [SupportController::class, 'uploadSupportTicketFile']);
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('submit/support/ticket', [SupportController::class, 'submitSupportTicket']);
        Route::post('send/support/ticket/message', [SupportController::class, 'sendSupportTicketMessage']);
        Route::get('get/all/support/tickets', [SupportController::class, 'getAllSupportTickets']);
        Route::post('get/all/support/ticket/messages', [SupportController::class, 'getAllSupportTicketMessages']);
    });

    // filter api
    Route::post('/filter/search/results', [FilterController::class, 'filterSearchResults']);
    Route::post('/filter/products', [FilterController::class, 'filterProducts']);

    // filter criteria api
    Route::get('get/all/storages', [FilterController::class, 'getAllStorages']);
    Route::get('get/all/sims', [FilterController::class, 'getAllSims']);
    Route::get('get/all/device/conditions', [FilterController::class, 'getAllDeviceConditions']);
    Route::get('get/all/warrenty/types', [FilterController::class, 'getAllWarrentyTypes']);
    Route::get('get/all/regions', [FilterController::class, 'getAllRegions']);

    // blog api
    Route::get('get/all/blog/categories', [BlogController::class, 'getAllBlogCategories']);
    Route::get('get/all/blogs', [BlogController::class, 'getAllBlogs']);
    Route::post('get/category/wise/blogs', [BlogController::class, 'getCategoryWiseBlogs']);
    Route::get('blog/details/{slug}', [BlogController::class, 'blogDetails']);

    Route::get('products/{slug}', [ProductController::class, 'product_details']);
});

Route::group(['namespace' => 'App\Http\Controllers', 'prefix' => 'v1'], function () {

    Route::group(['prefix' => 'delivery/pathao'], function () {
        Route::get('store-info', "Api\PathaoController@store_info");
        Route::get('cities', "Api\PathaoController@cities");
        Route::get('zones/{cityId}', "Api\PathaoController@zones");
        Route::get('areas/{zoneId}', "Api\PathaoController@areas");
        Route::post('create-order', "Api\PathaoController@createOrder");
        Route::post('price-plan', "Api\PathaoController@getPricePlan");
    });

    Route::group(['prefix' => 'media'], function () {
        Route::post('/upload', 'Management\MediaUpController@upload');
        Route::delete('/delete', 'Management\MediaUpController@delete');

        Route::get('/folders', 'Management\MediaUpController@folders');
        Route::post('/new-folder', 'Management\MediaUpController@new_folder');
        Route::get('/files/{folder_id}', 'Management\MediaUpController@files_by_folder_id');
    });
});
