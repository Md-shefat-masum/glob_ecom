<?php

//auth routes

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;

require __DIR__.'/authRoutes.php';

//dashboard routes 
require __DIR__.'/dashboardRoutes.php';

// payment routes
require __DIR__.'/paymentRoutes.php';

//ecommerce routes
require __DIR__.'/ecommerceRoutes.php';

//inventory routes
require __DIR__.'/inventoryRoutes.php';

//accounts routes
require __DIR__.'/accountRoutes.php';

//crm routes
require __DIR__.'/crmRoutes.php';

//product management routes
require __DIR__.'/productManagementRoutes.php';

//role and permission routes
require __DIR__.'/rolePermissionRoutes.php';

//website config routes
require __DIR__.'/WebConfigRoutes.php';

//cms routes
require __DIR__.'/cmsRoutes.php';

//clear cache routes
require __DIR__.'/cache.php';

//general routes
require __DIR__.'/generalRoutes.php';

//media routes
require __DIR__.'/mediaRoutes.php';

//stock adjustment routes
require __DIR__.'/stockAdjustmentRoutes.php';

//analytics routes
require __DIR__.'/analyticsRoutes.php';

//report routes
require __DIR__.'/reportRoutes.php';

// desktop POS routes
require __DIR__.'/pos_desktop_route.php';

// SR Management (Sales Targets)
require __DIR__.'/srRoutes.php';

Route::get('/barcode_gen', function(){
    return view('backend.product.barcode_gen'); 
});

Route::get('/ttt', function () {
    $path = \Illuminate\Support\Facades\Storage::disk('ftp')->putFile('uploads/test', public_path('logo.jpg'));
    return response()->json([
        'status' => 'OK', 
        $path, 
        \Illuminate\Support\Facades\Storage::disk('ftp')->url($path)
    ]);
});

Route::get('/dump-autoload', function () {
    $a = sms_send('01646376015','SMS from pos');
    dd($a);
});

// Product Order Invoice Routes (Public)
Route::get('/order-invoice/{slug}', [App\Http\Controllers\Inventory\ProductOrderController::class, 'showInvoice'])->name('order.invoice');
Route::get('/order-invoice/{slug}/pdf', [App\Http\Controllers\Inventory\ProductOrderController::class, 'downloadInvoicePDF'])->name('order.invoice.pdf');
Route::post('/order-invoice/{slug}/email', [App\Http\Controllers\Inventory\ProductOrderController::class, 'emailInvoice'])->name('order.invoice.email');

// PWA Service Worker Route
Route::get('/sw.js', function () {
    $version = config('app.version', '1.0.0');
    $swContent = file_get_contents(public_path('sw.js'));
    // Replace all instances of APP_VERSION_PLACEHOLDER with actual version
    $swContent = str_replace('APP_VERSION_PLACEHOLDER', $version, $swContent);
    
    return response($swContent, 200)
        ->header('Content-Type', 'application/javascript')
        ->header('Service-Worker-Allowed', '/')
        ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
        ->header('Pragma', 'no-cache')
        ->header('Expires', '0');
});

// PWA Manifest Route
Route::get('/manifest.json', function () {
    $manifest = [
        'name' => config('app.name', 'POS System'),
        'short_name' => 'POS',
        'description' => 'Point of Sale System',
        'start_url' => '/',
        'display' => 'standalone',
        'background_color' => '#ffffff',
        'theme_color' => '#4f46e5',
        'orientation' => 'portrait-primary',
        'icons' => [
            [
                'src' => url('assets/images/favicon.ico'),
                'sizes' => '48x48',
                'type' => 'image/x-icon'
            ],
            [
                'src' => url('assets/images/favicon.ico'),
                'sizes' => '192x192',
                'type' => 'image/x-icon'
            ],
            [
                'src' => url('assets/images/favicon.ico'),
                'sizes' => '512x512',
                'type' => 'image/x-icon'
            ]
        ],
        'categories' => ['business', 'productivity'],
        'screenshots' => [],
        'prefer_related_applications' => false
    ];
    
    return response()->json($manifest, 200)
        ->header('Content-Type', 'application/manifest+json');
});

Route::get('/brand-upload', function () {
    return 0;
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    \App\Models\Brand::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    try {
        $client = new Client([
            'verify' => false,   // if SSL issue occurs, optional
            'timeout' => 10,
        ]);

        $response = $client->get('https://bme.com.bd/a-brands');

        $data =  $response->getBody()->getContents();
        $data = json_decode($data, true);
        
        foreach($data as $item){
            $item = (object) $item;
            
            $brand = new \App\Models\Brand();
            $brand = $brand::create([
                "id"                => $item->id,
                "name"              => $item->name,
                "seo_name"          => $item->seo_name,
                "logo"              => $item->logo,
                "featured"               => $item->top,
                "slug"              => $item->slug,
                "url"               => $item->url,
                "meta_title"        => $item->meta_title,
                "meta_description"  => $item->meta_description,
                "brand_content"     => $item->brand_content,
                "status"            => $item->status,
            ]);
            
            $brand->id = $item->id;
        }
        
        dd($data[0]);

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/category-upload', function () {
    return 0;
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    \App\Models\Category::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    try {
        $client = new Client([
            'verify' => false,
            'timeout' => 10,
        ]);

        $response = $client->get('https://bme.com.bd/a-cats');
        $data = json_decode($response->getBody()->getContents(), true);

        foreach ($data as $item) {

            $item = (object) $item; // convert array → object

            $data = \App\Models\Category::updateOrCreate(
                ['id' => $item->id], // prevent duplicate insert
                [
                    "name"                  => $item->name,
                    "icon"                  => $item->icon ?? null,
                    "banner_image"          => $item->banner ?? $item->banner_image ?? null,
                    "slug"                  => $item->slug,
                    "status"                => $item->status,            // 1=Active,0=Inactive
                    "featured"              => $item->top ?? 0,     // 0/1
                    "show_on_navbar"        => 0,          // top → show_on_navbar
                    "serial"                => $item->order_sequence ?? -1,
                    
                    // additional fields
                    "seo_name"              => $item->seo_name ?? null,
                    "commision_rate"        => $item->commision_rate ?? 0,
                    "digital"               => $item->digital ?? null,
                    "meta_title"            => $item->meta_title ?? null,
                    "meta_description"      => $item->meta_description ?? null,
                    "short_description"     => $item->category_short_content ?? null,

                    "created_at"            => $item->created_at,
                    "updated_at"            => $item->updated_at,
                ]
            );
            
            $data->id = $item->id;
            $data->save();
        }

        return "Category import completed successfully";

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/subcategory-upload', function () {
    return 0;
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    \App\Models\Subcategory::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    try {
        $client = new Client([
            'verify' => false,
            'timeout' => 10,
        ]);

        $response = $client->get('https://bme.com.bd/a-subcats');
        $data = json_decode($response->getBody()->getContents(), true);

        foreach ($data as $item) {

            $item = (object) $item; // convert array → object

            $data = \App\Models\Subcategory::updateOrCreate(
                ['id' => $item->id], // prevent duplicate insert
                [
                    "category_id"               => $item->category_id,
                    "name"                      => $item->name,
                    "icon"                      => $item->icon ?? null,
                    "image"                     => $item->image ?? null,
                    "slug"                      => $item->slug,
                    "status"                    => $item->status,        // 1=Active, 0=Inactive
                    "featured"                  => $item->featured ?? 0, // 0/1
                    "serial"                    => $item->serial ?? -1,
                    
                    // additional fields
                    "seo_name"                  => $item->seo_name ?? null,
                    "url"                       => $item->url ?? null,
                    "meta_title"                => $item->meta_title ?? null,
                    "meta_description"          => $item->meta_description ?? null,
                    "sub_category_short_content"=> $item->sub_category_short_content ?? null,
                    "sub_category_content"      => $item->sub_category_content ?? null,
                    "phone"                     => $item->phone ?? null,
                    "whatsapp"                  => $item->whatsapp ?? null,
                    "email"                     => $item->email ?? null,

                    "created_at"                => $item->created_at,
                    "updated_at"                => $item->updated_at,
                ]
            );
            
            $data->id = $item->id;
            $data->save();
        }

        return "Subcategory import completed successfully";

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage()
        ], 500);
    }
});

Route::get('/product-upload', function () {
    // return 0;
    DB::statement('SET FOREIGN_KEY_CHECKS=0');
    \App\Models\Product::truncate();
    DB::statement('SET FOREIGN_KEY_CHECKS=1');
    ini_set('memory_limit', '512M');
    set_time_limit(0); // avoid timeout
    try {
        $client = new Client([
            'verify' => false,
            'timeout' => 20,
        ]);

        $response = $client->get('https://bme.com.bd/a-products');
        $data = json_decode($response->getBody()->getContents(), true);
        

        foreach ($data as $item) {

            $item = (object) $item; // convert array → object
            
            $product = \App\Models\Product::updateOrCreate(
                ['id' => $item->id], // prevent duplicate insert
                [
                    "store_id"                     => $item->store_id ?? null,
                    "category_id"                  => $item->category_id ?? null,
                    "subcategory_id"               => $item->subcategory_id ?? null,
                    "childcategory_id"             => $item->subsubcategory_id ?? null,
                    "brand_id"                     => $item->brand_id ?? null,
                    "model_id"                     => $item->model_id ?? null,
                    'model'                        => $item->model ?? null,
                    "name"                         => $item->name,
                    "code"                         => $item->code ?? null,
                    "sku"                          => $item->sku ?? null,
                    "barcode"                      => $item->barcode ?? null,
                    "hsn_code"                     => $item->hsn_code ?? null,
                    "image"                        => $item->thumbnail_img ?? null,
                    "multiple_images"              => $item->photos ?? null,
                    "short_description"            => $item->short_description ?? null,
                    "description"                  => $item->description ?? null,
                    "specification"                => $item->specification ?? null,
                    "attributes"                   => $item->attributes ?? null,
                    "shipping_info"                => $item->shipping_info ?? null,
                    "tax_info"                     => $item->tax_info ?? null,
                    "warrenty_policy"              => $item->warrenty_policy ?? null,
                    "size_chart"                   => $item->size_chart ?? null,
                    "price"                        => $item->unit_price ?? 0,
                    "discount_price"               => $item->discount_price ?? 0,
                    "discount_parcent"             => $item->discount_parcent ?? 0,
                    "reward_points"                => $item->reward_points ?? 0,
                    "stock"                        => $item->stock ?? 0,
                    "min_order_qty"                => $item->min_qty ?? 1,
                    "max_order_qty"                => $item->max_order_qty ?? 0,
                    "low_stock"                    => $item->low_stock ?? 0,
                    "unit_id"                       => $item->unit_id ?? null,
                    "tags"                         => $item->tags ?? null,
                    "video_url"                    => $item->video_url ?? null,
                    "warrenty_id"                  => $item->warrenty_id ?? null,
                    "chest"                        => $item->chest ?? null,
                    "length"                       => $item->length ?? null,
                    "sleeve"                       => $item->sleeve ?? null,
                    "waist"                        => $item->waist ?? null,
                    "weight"                       => $item->weight ?? null,
                    "size_ratio"                    => $item->size_ratio ?? null,
                    "fabrication"                  => $item->fabrication ?? null,
                    "fabrication_gsm_ounce"        => $item->fabrication_gsm_ounce ?? null,
                    "contact_number"               => $item->contact_number ?? null,
                    "contact_description"          => $item->contact_description ?? null,
                    "availability_status"          => $item->availability_status ?? null,
                    "related_similar_products"     => $item->related_similar_products ?? null,
                    "related_recommended_products" => $item->related_recommended_products ?? null,
                    "related_addon_products"       => $item->related_addon_products ?? null,
                    "notification_title"           => $item->notification_title ?? null,
                    "notification_description"     => $item->notification_description ?? null,
                    "notification_button_text"     => $item->notification_button_text ?? null,
                    "notification_button_url"      => $item->notification_button_url ?? null,
                    "notification_image_path"      => $item->notification_image_path ?? null,
                    "notification_image_id"        => $item->notification_image_id ?? null,
                    "notification_is_show"         => $item->notification_is_show ?? 0,
                    "slug"                         => $item->slug ?? null,
                    "flag_id"                      => $item->flag_id ?? null,
                    "special_offer"                => $item->special_offer ?? 0,
                    "has_variant"                  => $item->has_variant ?? 0,
                    "offer_end_time"               => $item->offer_end_time ?? null,
                    "meta_title"                   => $item->meta_title ?? null,
                    "meta_keywords"                => $item->meta_keywords ?? null,
                    "meta_description"             => $item->meta_description ?? null,
                    "meta_image"                   => $item->meta_image ?? null,
                    "status"                       => $item->status ?? 1,
                    "is_package"                   => $item->is_package ?? 0,
                    "is_demo"                      => $item->is_demo ?? 0,
                    "is_product_qty_multiply"      => $item->is_product_qty_multiply ?? 0,
                    "added_by"                     => $item->added_by ?? null,
                    "user_id"                      => $item->user_id ?? null,
                    "subsubcategory_id"            => $item->subsubcategory_id ?? null,
                    "publisher_id"                 => $item->publisher_id ?? null,
                    "photos"                       => $item->photos ?? null,
                    "thumbnail_img"                => $item->thumbnail_img ?? null,
                    "pdf_images"                   => $item->pdf_images ?? null,
                    "image_alt_tag"                => $item->image_alt_tag ?? null,
                    "video_provider"               => $item->video_provider ?? null,
                    "partial_payment_type"         => $item->partial_payment_type ?? null,
                    "partial_payment_price"        => $item->partial_payment_price ?? 0,
                    "stock_availability"           => $item->stock_availability ?? null,
                    "stock_notes"                  => $item->stock_notes ?? null,
                    "payment_type"                 => $item->payment_type ?? null,
                    "unit"                         => $item->unit ?? null,
                    "dealer_min_qty"               => $item->dealer_min_qty ?? 0,
                    "tax_type"                     => $item->tax_type ?? null,
                    "pick_up"                      => $item->pick_up ?? null,
                    "shipping_cost_inside_dhaka"   => $item->shipping_cost_inside_dhaka ?? 0,
                    "shipping_cost_outside_dhaka"  => $item->shipping_cost_outside_dhaka ?? 0,
                    "num_of_sale"                  => $item->num_of_sale ?? 0,
                    "youtube_video_link"           => $item->youtube_video_link ?? null,
                    "meta_img"                     => $item->meta_img ?? null,
                    "pdf"                          => $item->pdf ?? null,
                    "file_name"                    => $item->file_name ?? null,
                    "file_path"                    => $item->file_path ?? null,
                    "product_type"                 => $item->product_type ?? null,
                    "todays_deal"                  => $item->todays_deal ?? 0,
                    "published"                    => $item->published ?? 1,
                    "featured"                     => $item->featured ?? 0,
                    "dealer_price"                 => $item->dealer_price ?? 0,
                    "cash_price"                   => $item->cash_price ?? 0,
                    "purchase_price"               => $item->purchase_price ?? 0,
                    "current_stock"                => $item->current_stock ?? 0,
                    "rating"                       => $item->rating ?? 0,
                    "digital"                      => $item->digital ?? 0,
                    "created_by"                   => $item->created_by ?? null,
                    "updated_by"                   => $item->updated_by ?? null,
                    "created_at"                   => $item->created_at ?? now(),
                    "updated_at"                   => $item->updated_at ?? now(),
                ]
            );
            $product->id = $item->id; // force set
            $product->save();
            
            // dd($item->id, $product->id);
        }

        return "Product import completed successfully";

    } catch (\Exception $e) {
        return response()->json([
            'error' => true,
            'message' => $e->getMessage()
        ], 500);
    }
});



