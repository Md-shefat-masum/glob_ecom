<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\PromotionalBannerController;
use App\Http\Controllers\CustomPageController;
use App\Http\Controllers\SideBannerController;
use App\Http\Controllers\GeneralInfoController;
use App\Http\Controllers\TestimonialController;
use App\Http\Controllers\Outlet\OutletController;
use App\Http\Controllers\TermsAndPolicyController;
use App\Http\Controllers\Gallery\VideoGalleryController;


Route::group(['middleware' => ['auth', 'CheckUserType', 'DemoMode']], function () {

    // sliders and banners routes
    Route::get('/view/all/sliders', [BannerController::class, 'viewAllSliders'])->name('ViewAllSliders');
    Route::get('/add/new/slider', [BannerController::class, 'addNewSlider'])->name('AddNewSlider');
    Route::post('/save/new/slider', [BannerController::class, 'saveNewSlider'])->name('SaveNewSlider');
    Route::get('/edit/slider/{slug}', [BannerController::class, 'editSlider'])->name('EditSlider');
    Route::post('/update/slider', [BannerController::class, 'updateSlider'])->name('UpdateSlider');
    Route::get('/rearrange/slider', [BannerController::class, 'rearrangeSlider'])->name('RearrangeSlider');
    Route::post('/update/slider/rearranged/order', [BannerController::class, 'updateRearrangedSliders'])->name('UpdateRearrangedSliders');
    Route::get('/delete/data/{slug}', [BannerController::class, 'deleteData'])->name('DeleteSliderBanner');
    Route::get('/view/all/banners', [BannerController::class, 'viewAllBanners'])->name('ViewAllBanners');
    Route::get('/add/new/banner', [BannerController::class, 'addNewBanner'])->name('AddNewBanner');
    Route::post('/save/new/banner', [BannerController::class, 'saveNewBanner'])->name('SaveNewBanner');
    Route::get('/edit/banner/{slug}', [BannerController::class, 'editBanner'])->name('EditBanner');
    Route::post('/update/banner', [BannerController::class, 'updateBanner'])->name('UpdateBanner');
    Route::get('/rearrange/banners', [BannerController::class, 'rearrangeBanners'])->name('RearrangeBanners');
    Route::post('/update/banners/rearranged/order', [BannerController::class, 'updateRearrangedBanners'])->name('UpdateRearrangedBanners');
    // Promotional Banner CRUD routes
    Route::get('/view/all/promotional/banners', [PromotionalBannerController::class, 'viewAllPromotionalBanners'])->name('ViewAllPromotionalBanners');
    Route::get('/add/new/promotional/banner', [PromotionalBannerController::class, 'addNewPromotionalBanner'])->name('AddNewPromotionalBanner');
    Route::post('/save/new/promotional/banner', [PromotionalBannerController::class, 'saveNewPromotionalBanner'])->name('SaveNewPromotionalBanner');
    Route::get('/edit/promotional/banner/{slug}', [PromotionalBannerController::class, 'editPromotionalBanner'])->name('EditPromotionalBanner');
    Route::post('/update/promotional/banner', [PromotionalBannerController::class, 'updatePromotionalBanner'])->name('UpdatePromotionalBanner');
    Route::get('/delete/promotional/banner/{slug}', [PromotionalBannerController::class, 'deletePromotionalBanner'])->name('DeletePromotionalBanner');
    Route::get('/rearrange/promotional/banners', [PromotionalBannerController::class, 'rearrangePromotionalBanners'])->name('RearrangePromotionalBanners');
    Route::post('/update/promotional/banners/rearranged/order', [PromotionalBannerController::class, 'updateRearrangedPromotionalBanners'])->name('UpdateRearrangedPromotionalBanners');
    
    // Old promotional banner route (keep for backward compatibility)
    Route::get('/view/promotional/banner', [BannerController::class, 'viewPromotionalBanner'])->name('ViewPromotionalBanner');
    Route::post('/update/promotional/banner/old', [BannerController::class, 'updatePromotionalBanner'])->name('UpdatePromotionalBannerOld');
    Route::get('/remove/promotional/banner/header/icon', [BannerController::class, 'removePromotionalHeaderIcon'])->name('RemovePromotionalHeaderIcon');
    Route::get('/remove/promotional/banner/product/image', [BannerController::class, 'removePromotionalProductImage'])->name('RemovePromotionalProductImage');
    Route::get('/remove/promotional/banner/bg/image', [BannerController::class, 'removePromotionalBackgroundImage'])->name('RemovePromotionalBackgroundImage');

    // SideBanner Management
    Route::get('/add/new/side-banner', [SideBannerController::class, 'addNewSideBanner'])->name('AddNewSideBanner');
    Route::post('/save/new/side-banner', [SideBannerController::class, 'saveNewSideBanner'])->name('SaveNewSideBanner');
    Route::get('/view/all/side-banner', [SideBannerController::class, 'viewAllSideBanner'])->name('ViewAllSideBanner');
    Route::get('/delete/side-banner/{slug}', [SideBannerController::class, 'deleteSideBanner'])->name('DeleteSideBanner');
    Route::get('/edit/side-banner/{slug}', [SideBannerController::class, 'editSideBanner'])->name('EditSideBanner');
    Route::post('/update/side-banner', [SideBannerController::class, 'updateSideBanner'])->name('UpdateSideBanner');


    // testimonial routes
    Route::get('/view/testimonials', [TestimonialController::class, 'viewTestimonials'])->name('ViewTestimonials');
    Route::get('/add/testimonial', [TestimonialController::class, 'addTestimonial'])->name('AddTestimonial');
    Route::post('/save/testimonial', [TestimonialController::class, 'saveTestimonial'])->name('SaveTestimonial');
    Route::get('/delete/testimonial/{slug}', [TestimonialController::class, 'deleteTestimonial'])->name('DeleteTestimonial');
    Route::get('/edit/testimonial/{slug}', [TestimonialController::class, 'editTestimonial'])->name('EditTestimonial');
    Route::post('/update/testimonial', [TestimonialController::class, 'updateTestimonial'])->name('UpdateTestimonial');


    // blog routes
    Route::get('/blog/categories', [BlogController::class, 'blogCategories'])->name('BlogCategories');
    Route::post('/save/blog/category', [BlogController::class, 'saveBlogCategory'])->name('SaveBlogCategory');
    Route::get('/delete/blog/category/{slug}', [BlogController::class, 'deleteBlogCategory'])->name('DeleteBlogCategory');
    Route::get('/feature/blog/category/{slug}', [BlogController::class, 'featureBlogCategory'])->name('FeatureBlogCategory');
    Route::get('/get/blog/category/info/{slug}', [BlogController::class, 'getBlogCategoryInfo'])->name('GetBlogCategoryInfo');
    Route::post('/update/blog/category', [BlogController::class, 'updateBlogCategoryInfo'])->name('UpdateBlogCategoryInfo');
    Route::get('/rearrange/blog/category', [BlogController::class, 'rearrangeBlogCategory'])->name('RearrangeBlogCategory');
    Route::post('/save/rearranged/blog/categories', [BlogController::class, 'saveRearrangeCategory'])->name('SaveRearrangeCategory');
    Route::get('/add/new/blog', [BlogController::class, 'addNewBlog'])->name('AddNewBlog');
    Route::post('/save/new/blog', [BlogController::class, 'saveNewBlog'])->name('SaveNewBlog');
    Route::get('/view/all/blogs', [BlogController::class, 'viewAllBlogs'])->name('ViewAllBlogs');
    Route::get('/delete/blog/{slug}', [BlogController::class, 'deleteBlog'])->name('DeleteBlog');
    Route::get('/edit/blog/{slug}', [BlogController::class, 'editBlog'])->name('EditBlog');
    Route::post('/update/blog', [BlogController::class, 'updateBlog'])->name('UpdateBlog');

    // terms and policies routes
    Route::get('/terms/and/condition', [TermsAndPolicyController::class, 'viewTermsAndCondition'])->name('ViewTermsAndCondition');
    Route::post('/update/terms', [TermsAndPolicyController::class, 'updateTermsAndCondition'])->name('UpdateTermsAndCondition');
    Route::get('/view/privacy/policy', [TermsAndPolicyController::class, 'viewPrivacyPolicy'])->name('ViewPrivacyPolicy');
    Route::post('/update/privacy/policy', [TermsAndPolicyController::class, 'updatePrivacyPolicy'])->name('UpdatePrivacyPolicy');
    Route::get('/view/shipping/policy', [TermsAndPolicyController::class, 'viewShippingPolicy'])->name('ViewShippingPolicy');
    Route::post('/update/shipping/policy', [TermsAndPolicyController::class, 'updateShippingPolicy'])->name('UpdateShippingPolicy');
    Route::get('/view/return/policy', [TermsAndPolicyController::class, 'viewReturnPolicy'])->name('ViewReturnPolicy');
    Route::post('/update/return/policy', [TermsAndPolicyController::class, 'updateReturnPolicy'])->name('UpdateReturnPolicy');

    // custom page
    Route::get('create/new/page', [CustomPageController::class, 'createNewPage'])->name('CreateNewPage');
    Route::post('save/custom/page', [CustomPageController::class, 'saveCustomPage'])->name('SaveCustomPage');
    Route::get('view/all/pages', [CustomPageController::class, 'viewCustomPages'])->name('ViewCustomPages');
    Route::get('delete/custom/page/{slug}', [CustomPageController::class, 'deleteCustomPage'])->name('DeleteCustomPage');
    Route::get('edit/custom/page/{slug}', [CustomPageController::class, 'editCustomPage'])->name('EditCustomPage');
    Route::post('update/custom/page', [CustomPageController::class, 'updateCustomPage'])->name('UpdateCustomPage');


    // Outlets
    Route::get('/add/new/outlet', [OutletController::class, 'addNewOutlet'])->name('AddNewOutlet');
    Route::post('/save/new/outlet', [OutletController::class, 'saveNewOutlet'])->name('SaveNewOutlet');
    Route::get('/view/all/outlet', [OutletController::class, 'viewAllOutlet'])->name('ViewAllOutlet');
    Route::get('/delete/outlet/{slug}', [OutletController::class, 'deleteOutlet'])->name('DeleteOutlet');
    Route::get('/edit/outlet/{slug}', [OutletController::class, 'editOutlet'])->name('EditOutlet');
    Route::post('/update/outlet', [OutletController::class, 'updateOutlet'])->name('UpdateOutlet');

    // Video Gallery
    Route::get('/add/new/video-gallery', [VideoGalleryController::class, 'addNewVideoGallery'])->name('AddNewVideoGallery');
    Route::post('/save/new/video-gallery', [VideoGalleryController::class, 'saveNewVideoGallery'])->name('SaveNewVideoGallery');
    Route::get('/view/all/video-gallery', [VideoGalleryController::class, 'viewAllVideoGallery'])->name('ViewAllVideoGallery');
    Route::get('/delete/video-gallery/{slug}', [VideoGalleryController::class, 'deleteVideoGallery'])->name('DeleteVideoGallery');
    Route::get('/edit/video-gallery/{slug}', [VideoGalleryController::class, 'editVideoGallery'])->name('EditVideoGallery');
    Route::post('/update/video-gallery', [VideoGalleryController::class, 'updateVideoGallery'])->name('UpdateVideoGallery');

    //about
    Route::get('/about/us/page', [GeneralInfoController::class, 'aboutUsPage'])->name('AboutUsPage');
    Route::post('/update/about/us', [GeneralInfoController::class, 'updateAboutUsPage'])->name('UpdateAboutUsPage');

    // faq routes
    Route::get('/view/all/faqs', [FaqController::class, 'viewAllFaqs'])->name('ViewAllFaqs');
    Route::get('/add/new/faq', [FaqController::class, 'addNewFaq'])->name('AddNewFaq');
    Route::post('/save/faq', [FaqController::class, 'saveFaq'])->name('SaveFaq');
    Route::get('/delete/faq/{slug}', [FaqController::class, 'deleteFaq'])->name('DeleteFaq');
    Route::get('/edit/faq/{slug}', [FaqController::class, 'editFaq'])->name('EditFaq');
    Route::post('/update/faq', [FaqController::class, 'updateFaq'])->name('UpdateFaq');
});
