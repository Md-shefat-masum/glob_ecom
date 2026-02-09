<!-- Left Menu Start -->
<?php
function checkAuth($routes)
{
    if (
        App\Models\UserRolePermission::where('user_id', Auth::user()->id)
            ->where('route', 'like', '%' . $routes . '%')
            ->exists()
    ) {
        return true;
    } else {
        return false;
    }
}

$eCommerceModule = App\Models\UserRolePermission::where('user_id', Auth::user()->id)
    ->where(function ($query) {
        $query
            ->Where('route', 'like', '%view/all/category%')

            ->orWhere('route', 'like', '%view/all/subcategory%')

            ->orWhere('route', 'like', '%view/all/childcategory%')

            ->orWhere('route', 'like', '%view/all/product%')

            ->orWhere('route', 'like', '%view/all/product-color%')

            ->orWhere('route', 'like', '%view/all/product-size%')

            ->orWhere('route', 'like', '%view/all/product-size-value%')

            ->orwhere('route', 'like', '%view/product/reviews%')

            ->orwhere('route', 'like', '%view/product/question/answer%')

            ->orwhere('route', 'like', '%view/orders%')
            ->orWhere('route', 'like', '%view/trash/orders%')
            ->orWhere('route', 'like', '%restore/orders%')
            ->orWhere('route', 'like', '%view/pending/orders%')
            ->orWhere('route', 'like', '%view/approved/orders%')
            ->orWhere('route', 'like', '%view/delivered/orders%')
            ->orWhere('route', 'like', '%view/cancelled/orders%')
            ->orWhere('route', 'like', '%view/picked/orders%')
            ->orWhere('route', 'like', '%view/intransit/orders%')

            ->orwhere('route', 'like', '%create/new/order%')

            ->orWhere('route', 'like', '%view/all/promo/codes%')

            ->orWhere('route', 'like', '%get/wishlist/count%')

            ->orwhere('route', 'like', '%send/notification/page%')
            ->orWhere('route', 'like', '%view/all/notifications%')

            ->orwhere('route', 'like', '%view/delivery/charges%')

            ->orwhere('route', 'like', '%view/upazila/thana%')

            ->orwhere('route', 'like', '%sales/report%')
            ->orWhere('route', 'like', '%generate/sales/report%')

            ->orwhere('route', 'like', '%sales/report%')
            ->orwhere('route', 'like', '%view/payment/history%');
    })
    ->get();

$inventoryModule = App\Models\UserRolePermission::where('user_id', Auth::user()->id)
    ->where(function ($query) {
        $query
            ->Where('route', 'like', '%view/all/product-warehouse%')

            ->orWhere('route', 'like', '%view/all/product-warehouse-room%')
            ->orWhere('route', 'like', '%view/all/product-warehouse-room-cartoon%')
            ->orWhere('route', 'like', '%view/all/product-supplier%')
            ->orWhere('route', 'like', '%view/all/supplier-source%')
            ->orWhere('route', 'like', '%view/all/purchase-product/quotation%')
            ->orWhere('route', 'like', '%view/all/purchase-product/order%')
            ->orWhere('route', 'like', '%view/all/purchase-product/charge%')
            ->orWhere('route', 'like', '%view/all/customer%')
            ->orWhere('route', 'like', '%product/purchase/report%')

            ->orwhere('route', 'like', '%view/payment/history%');
    })
    ->get();

$accountModule = App\Models\UserRolePermission::where('user_id', Auth::user()->id)
    ->where(function ($query) {
        $query
            ->Where('route', 'like', '%view/all/payment-type%')

            ->orWhere('route', 'like', '%view/all/expense-category%')
            ->orWhere('route', 'like', '%view/all/ac-account%')
            ->orWhere('route', 'like', '%view/all/expense%')
            ->orWhere('route', 'like', '%view/all/deposit%')
            ->orWhere('route', 'like', '%ledger%')
            ->orwhere('route', 'like', '%view/payment/history%');
    })
    ->get();

$crmModule = App\Models\UserRolePermission::where('user_id', Auth::user()->id)
    ->where(function ($query) {
        $query
            ->Where('route', 'like', '%view/all/customer-source%')

            ->orWhere('route', 'like', '%view/all/customer-category%')
            ->orWhere('route', 'like', '%view/all/customer-ecommerce%')
            ->orWhere('route', 'like', '%view/all/customer-contact-history%')
            ->orWhere('route', 'like', '%view/all/customer-next-contact-date%')
            ->orWhere('route', 'like', '%pending/support/tickets%')
            ->orWhere('route', 'like', '%solved/support/tickets%')
            ->orWhere('route', 'like', '%on/hold/support/tickets%')
            ->orWhere('route', 'like', '%rejected/support/tickets%')
            ->orWhere('route', 'like', '%view/all/contact/requests%')
            ->orwhere('route', 'like', '%view/all/subscribed/users%');
    })
    ->get();

$rolePermissionModule = App\Models\UserRolePermission::where('user_id', Auth::user()->id)
    ->where(function ($query) {
        $query
            ->Where('route', 'like', '%view/system/users%')

            ->orWhere('route', 'like', '%view/user/roles%')
            ->orWhere('route', 'like', '%view/user/role/permission%')
            ->orwhere('route', 'like', '%view/permission/routes%');
    })
    ->get();

$websiteConfigModule = App\Models\UserRolePermission::where('user_id', Auth::user()->id)
    ->where(function ($query) {
        $query
            ->Where('route', 'like', '%general/info%')

            ->orWhere('route', 'like', '%website/theme/page%')
            ->orWhere('route', 'like', '%social/media/page%')
            ->orWhere('route', 'like', '%seo/homepage%')
            ->orWhere('route', 'like', '%custom/css/js%')
            ->orwhere('route', 'like', '%social/chat/script/page%');
    })
    ->get();

$cmsModule = App\Models\UserRolePermission::where('user_id', Auth::user()->id)
    ->where(function ($query) {
        $query
            ->Where('route', 'like', '%view/all/sliders%')

            ->orWhere('route', 'like', '%view/all/banners%')
            ->orWhere('route', 'like', '%view/promotional/banner%')
            ->orWhere('route', 'like', '%view/all/side-banner%')
            ->orWhere('route', 'like', '%blog/categories%')
            ->orWhere('route', 'like', '%view/all/blogs%')
            ->orWhere('route', 'like', '%terms/and/condition%')
            ->orWhere('route', 'like', '%view/privacy/policy%')
            ->orWhere('route', 'like', '%view/shipping/policy%')
            ->orWhere('route', 'like', '%view/return/policy%')
            ->orWhere('route', 'like', '%view/all/pages%')
            ->orWhere('route', 'like', '%view/all/outlet%')
            ->orWhere('route', 'like', '%view/all/video-gallery%')
            ->orWhere('route', 'like', '%about/us/page%')
            ->orwhere('route', 'like', '%view/all/faqs%');
    })
    ->get();

$backupModule = App\Models\UserRolePermission::where('user_id', Auth::user()->id)
    ->where('route', 'like', '%backup%')
    ->get();
?>


<ul class="metismenu list-unstyled" id="side-menu">
    <li>
        <a href="{{ url('/home') }}" data-active-paths="{{ url('/home') }}">
            <i class="feather-home"></i>
            <span> Ecommerce Dashboard</span>
        </a>
    </li>

    @if (checkAuth('crm-home'))
        <li>
            <a href="{{ url('/crm-home') }}" data-active-paths="{{ url('/crm-home') }}">
                <i class="feather-headphones"></i>
                <span> CRM Dashboard</span>
            </a>
        </li>
    @endif

    @if (checkAuth('accounts-home'))
        <li>
            <a href="{{ url('/accounts-home') }}" data-active-paths="{{ url('/accounts-home') }}">
                <i class="feather-dollar-sign"></i>
                <span> Accounts Dashboard</span>
            </a>
        </li>
    @endif

    @if (checkAuth('inventory-home'))
        <li>
            <a href="{{ url('/inventory-home') }}" data-active-paths="{{ url('/inventory-home') }}">
                <i class="feather-shopping-bag"></i>
                <span> Inventory Dashboard</span>
            </a>
        </li>
    @endif

    {{-- E commerce module --}}
    @if ($eCommerceModule->count())
        <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
        <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">E-commerce Modules</li>
        <li>
            @php
                $configRoutes = ['config/setup', 'view/email/credential', 'setup/payment/gateways'];
                $isConfigActive = false;
                foreach ($configRoutes as $route) {
                    if (checkAuth($route)) {
                        $isConfigActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isConfigActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-settings"></i><span>Config</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('config/setup'))
                    <li><a href="{{ url('/config/setup') }}" data-active-paths="{{ url('/config/setup') }}">Setup Your
                            Config</a></li>
                @endif
                @if (checkAuth('view/email/credential'))
                    <li><a href="{{ url('/view/email/credential') }}"
                            data-active-paths="{{ url('/view/email/credential') }}">Email Configure (SMTP)</a></li>
                @endif
                @if (checkAuth('setup/payment/gateways'))
                    <li><a href="{{ url('/setup/payment/gateways') }}"
                            data-active-paths="{{ url('/setup/payment/gateways') }}">Payment Gateway</a></li>
                @endif

            </ul>
        </li>
        <li>
            @php
                $attributeRoutes = [
                    'view/all/sizes',
                    'view/all/colors',
                    'view/all/units',
                    'view/all/brands',
                    'view/all/models',
                    'view/all/flags',
                ];
                $isAttributeActive = false;
                foreach ($attributeRoutes as $route) {
                    if (checkAuth($route)) {
                        $isAttributeActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isAttributeActive)
                <a href="javascript: void(0);" class="has-arrow">
                    <i class="feather-settings"></i>
                    <span>Product Attributes</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                {{-- Fashion Industry --}}
                @if (DB::table('config_setups')->where('code', 'product_size')->first() && checkAuth('view-all-sizes'))
                    <li>
                        <a href="{{ url('/view/all/sizes') }}"
                            data-active-paths="{{ url('/view/all/sizes') }},{{ url('/rearrange/size') }}">
                            Product Sizes
                        </a>
                    </li>
                @endif

                {{-- Common --}}
                @if (DB::table('config_setups')->where('code', 'color')->first() && checkAuth('view-all-colors'))
                    <li>
                        <a href="{{ url('/view/all/colors') }}" data-active-paths="{{ url('/view/all/colors') }}">
                            Product Colors
                        </a>
                    </li>
                @endif

                @if (DB::table('config_setups')->where('code', 'measurement_unit')->first() && checkAuth('view-all-units'))
                    <li>
                        <a href="{{ url('/view/all/units') }}" data-active-paths="{{ url('/view/all/units') }}">
                            Measurement Units
                        </a>
                    </li>
                @endif

                @if (checkAuth('view/all/brands'))
                    <li>
                        <a href="{{ url('/view/all/brands') }}"
                            data-active-paths="{{ url('/view/all/brands') }},{{ url('/add/new/brand') }},{{ url('/rearrange/brands') }},{{ url('edit/brand/*') }}">
                            Product Brands
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/all/models'))
                    <li>
                        <a href="{{ url('/view/all/models') }}"
                            data-active-paths="{{ url('/view/all/models') }}, {{ url('add/new/model') }},{{ url('edit/model/*') }}">
                            Models of Brand
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/all/flags'))
                    <li>
                        <a href="{{ url('/view/all/flags') }}" data-active-paths="{{ url('/view/all/flags') }}">
                            Product Flags
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li>
            @if (checkAuth('view/all/category'))
                <a href="{{ url('/view/all/category') }}"
                    data-active-paths="{{ url('/view/all/category') }},{{ url('/add/new/category') }},{{ url('/edit/category/*') }},{{ url('/rearrange/category') }}">
                    <i class="feather-sliders"></i>
                    <span>Category</span>
                    <span style="color:lightgreen" title="Total Products">
                        ({{ DB::table('categories')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/subcategory'))
                <a href="{{ url('/view/all/subcategory') }}"
                    data-active-paths="{{ url('/view/all/subcategory') }},{{ url('/add/new/subcategory') }},{{ url('/edit/subcategory/*') }},{{ url('/rearrange/subcategory') }}">
                    <i class="feather-command"></i>
                    <span>Subcategory</span>
                    <span style="color:lightgreen" title="Total Products">
                        ({{ DB::table('subcategories')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/childcategory'))
                <a href="{{ url('/view/all/childcategory') }}"
                    data-active-paths="{{ url('/view/all/childcategory') }},{{ url('/add/new/childcategory') }},{{ url('/edit/childcategory/*') }},{{ url('/rearrange/childcategory') }}">
                    <i class="feather-git-pull-request"></i><span>Child
                        Category</span>
                    <span style="color:lightgreen" title="Total Products">
                        ({{ DB::table('child_categories')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @php
                $productRoutes = ['view/all/product', 'view/product/reviews', 'view/product/question-answer'];
                $isProductActive = false;
                foreach ($productRoutes as $route) {
                    if (checkAuth($route)) {
                        $isProductActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isProductActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-box"></i><span>Manage
                        Products</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('view/all/product'))
                    <li>
                        <a href="{{ url('/view/all/product') }}"
                            data-active-paths="{{ url('/view/all/product') }},{{ url('/add/new/product') }},{{ url('/edit/product/*') }},{{ url('/rearrange/product') }}">
                            View All Products
                            <span style="color:lightgreen" title="Total Products">
                                ({{ DB::table('products')->count() }})
                            </span>
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/product/reviews'))
                    <li>
                        <a href="{{ url('/view/product/reviews') }}"
                            data-active-paths="{{ url('/view/product/reviews') }}">
                            Products's Review
                            <span style="color:goldenrod" title="Indicate Pending Review">
                                (@php
                                    echo DB::table('product_reviews')->where('status', 0)->count();
                                @endphp)
                            </span>
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/product/question/answer'))
                    <li>
                        <a href="{{ url('/view/product/question/answer') }}"
                            data-active-paths="{{ url('/view/product/question/answer') }}">
                            Product Ques/Ans
                            <span style="color:goldenrod" title="Indicate Unanswered Questions">
                                (@php
                                    echo DB::table('product_question_answers')
                                        ->whereNull('answer')
                                        ->orWhere('answer', '=', '')
                                        ->count();
                                @endphp)
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li>
            @if (checkAuth('package-products'))
                <a href="{{ url('/package-products') }}"
                    data-active-paths="{{ url('/package-products') }}, {{ url('/package-products/create') }}, {{ url('/package-products/*/edit') }}, {{ url('/package-products/*/manage-items') }}">
                    <i class="feather-package"></i> Package Products
                    <span style="color:lightgreen" title="Total Package Products">
                        ({{ DB::table('products')->where('is_package', true)->count() }})
                    </span>
                </a>
            @endif
        </li>


        <li>
            @php
                $orderRoutes = [
                    'view/orders',
                    'view/pending/orders',
                    'view/approved/orders',
                    'view/intransit/orders',
                    'view/delivered/orders',
                    'view/picked/orders',
                    'view/cancelled/orders',
                ];
                $isOrderActive = false;
                foreach ($orderRoutes as $route) {
                    if (checkAuth($route)) {
                        $isOrderActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isOrderActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-shopping-cart"></i><span>Manage
                        Orders</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('view/orders'))
                    <li>
                        <a style="color: white !important;" href="{{ url('/view/orders') }}"
                            data-active-paths="{{ url('/view/orders') }}, {{ url('create/new/order') }},{{ url('order/details/*') }}">
                            All Orders
                            (@php echo DB::table('orders')->count(); @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/pending/orders'))
                    <li><a style="color: skyblue !important;" href="{{ url('/view/pending/orders') }}"
                            data-active-paths="{{ url('/view/pending/orders') }}, {{ url('order/edit/*') }}">
                            Pending Orders
                            (@php
                                echo DB::table('orders')->where('order_status', 0)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/approved/orders'))
                    <li><a style="color: wheat !important;" href="{{ url('/view/approved/orders') }}"
                            data-active-paths="{{ url('/view/approved/orders') }}">
                            Approved Orders
                            (@php
                                echo DB::table('orders')->where('order_status', 1)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/intransit/orders'))
                    <li><a style="color: violet !important;" href="{{ url('/view/intransit/orders') }}"
                            data-active-paths="{{ url('/view/intransit/orders') }}">
                            Intransit Orders
                            (@php
                                echo DB::table('orders')->where('order_status', 2)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/delivered/orders'))
                    <li><a style="color: #0c0 !important;" href="{{ url('/view/delivered/orders') }}"
                            data-active-paths="{{ url('/view/delivered/orders') }}">
                            Delivered Orders
                            (@php
                                echo DB::table('orders')->where('order_status', 3)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/picked/orders'))
                    <li><a style="color: tomato !important;" href="{{ url('/view/picked/orders') }}"
                            data-active-paths="{{ url('/view/picked/orders') }}">
                            Picked Orders
                            (@php
                                echo DB::table('orders')->where('order_status', 5)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/cancelled/orders'))
                    <li><a style="color: red !important;" href="{{ url('/view/cancelled/orders') }}"
                            data-active-paths="{{ url('/view/cancelled/orders') }}">
                            Cancelled Orders
                            (@php
                                echo DB::table('orders')->where('order_status', 4)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li>
            @if (checkAuth('ViewAllInvoices'))
                <a href="{{ route('ViewAllInvoices') }}" data-active-paths="{{ route('ViewAllInvoices') }}">
                    <i class="feather-file-text"></i>
                    <span>Invoices</span>
                    <span style="color:lightgreen" title="Total Invoices">
                        (@php echo DB::table('orders')->where('order_from', 3)->where('invoice_generated', 1)->count(); @endphp)
                    </span>
                </a>
            @endif
        </li>

        <li>
            @if (checkAuth('view/all/promo/codes'))
                <a href="{{ url('/view/all/promo/codes') }}"
                    data-active-paths="{{ url('/view/all/promo/codes') }},{{ url('/add/new/code') }},{{ url('/edit/promo/code/*') }}">
                    <i class="feather-gift"></i>
                    <span>Promo Codes</span>
                    <span style="color:lightgreen" title="Total Products">
                        ({{ DB::table('promo_codes')->count() }})
                    </span>
                </a>
            @endif
        </li>

        <li>
            @php
                $pushNotificationRoutes = ['send/notification/page', 'view/all/notifications'];
                $isPushNotificationActive = false;
                foreach ($pushNotificationRoutes as $route) {
                    if (checkAuth($route)) {
                        $isPushNotificationActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isPushNotificationActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-bell"></i><span>Push
                        Notification</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('send/notification/page'))
                    <li><a href="{{ url('/send/notification/page') }}"
                            data-active-paths="{{ url('/send/notification/page') }}">Send Notification</a></li>
                @endif
                @if (checkAuth('view/all/notifications'))
                    <li><a href="{{ url('/view/all/notifications') }}"
                            data-active-paths="{{ url('/view/all/notifications') }}">Previous Notifications</a></li>
                @endif
            </ul>
        </li>

        @if (checkAuth('view/customers/wishlist'))
            <li><a href="{{ url('/view/customers/wishlist') }}"
                    data-active-paths="{{ url('/view/customers/wishlist') }}"><i
                        class="feather-heart"></i><span>Customer's
                        Wishlist</span></a></li>
        @endif
        @if (checkAuth('view/delivery/charges'))
            <li><a href="{{ url('/view/delivery/charges') }}"
                    data-active-paths="{{ url('/view/delivery/charges') }}"><i
                        class="feather-truck"></i><span>Delivery Charges</span></a>
            </li>
        @endif
        @if (checkAuth('view/upazila/thana'))
            <li><a href="{{ url('/view/upazila/thana') }}" data-active-paths="{{ url('/view/upazila/thana') }}"><i
                        class="dripicons-location"></i><span>Upazila & Thana</span></a>
            </li>
        @endif
        @if (checkAuth('view/payment/history'))
            <li><a href="{{ url('/view/payment/history') }}"
                    data-active-paths="{{ url('/view/payment/history') }}"><i
                        class="feather-dollar-sign"></i><span>Payment History</span></a>
            </li>
        @endif

        <li>
            @php
                $reportRoutes = ['sales/report'];
                $isReportActive = false;
                foreach ($reportRoutes as $route) {
                    if (checkAuth($route)) {
                        $isReportActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isReportActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-printer"></i><span>Generate
                        Report</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('sales/report'))
                    <li><a href="{{ url('/sales/report') }}" data-active-paths="{{ url('/sales/report') }}">Sales
                            Report</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    {{-- inventory module --}}
    @if ($inventoryModule->count())
        <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
        <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Inventory Modules</li>
        <li>
            @if (checkAuth('view/all/product-warehouse'))
                <a href="{{ url('/view/all/product-warehouse') }}"
                    data-active-paths="{{ url('/view/all/product-warehouse') }}, {{ url('/add/new/product-warehouse') }}, {{ url('/edit/product-warehouse/*') }}">
                    <i class="feather-box"></i>
                    <span>Product Warehouse</span>
                    <span style="color:lightgreen" title="Total Product Warehouses">
                        ({{ DB::table('product_warehouses')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/product-warehouse-room'))
                <a href="{{ url('/view/all/product-warehouse-room') }}"
                    data-active-paths="{{ url('/view/all/product-warehouse-room') }}, {{ url('/add/new/product-warehouse-room') }}, {{ url('/edit/product-warehouse-room/*') }}">
                    <i class="feather-box"></i>Warehouse Room
                    <span style="color:lightgreen" title="Total Product Warehouse Rooms">
                        ({{ DB::table('product_warehouse_rooms')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/product-warehouse-room-cartoon'))
                <a href="{{ url('/view/all/product-warehouse-room-cartoon') }}"
                    data-active-paths="{{ url('/view/all/product-warehouse-room-cartoon') }}, {{ url('/add/new/product-warehouse-room-cartoon') }}, {{ url('/edit/product-warehouse-room-cartoon/*') }}">>
                    <i class="feather-box"></i> Room Cartoon
                    <span style="color:lightgreen" title="Total Product Warehouse Room cartoons">
                        ({{ DB::table('product_warehouse_room_cartoons')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/supplier-source'))
                <a href="{{ url('/view/all/supplier-source') }}"
                    data-active-paths="{{ url('/view/all/supplier-source') }}, {{ url('/add/new/supplier-source') }}, {{ url('/edit/supplier-source/*') }}">
                    <i class="feather-box"></i> Supplier Src Type
                    <span style="color:lightgreen" title="Total CS Types">
                        ({{ DB::table('supplier_source_types')->count() }})
                    </span>
                </a>
            @endif
        </li>

        <li>
            @if (checkAuth('view/all/product-supplier'))
                <a href="{{ url('/view/all/product-supplier') }}"
                    data-active-paths="{{ url('/view/all/product-supplier') }}, {{ url('/add/new/product-supplier') }}, {{ url('/edit/product-supplier/*') }}">
                    <i class="feather-box"></i> Product Suppliers
                    <span style="color:lightgreen" title="Total Product Suppliers">
                        ({{ DB::table('product_suppliers')->count() }})
                    </span>
                </a>
            @endif
        </li>

        <li>
            @php
                $purchaseRoutes = [
                    'view/all/purchase-product/charge',
                    'view/all/purchase-product/quotation',
                    'view/all/purchase-product/order',
                ];
                $isPurchaseActive = false;
                foreach ($purchaseRoutes as $route) {
                    if (checkAuth($route)) {
                        $isPurchaseActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isPurchaseActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-box"></i><span>Product
                        Purchase</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('view/all/purchase-product/charge'))
                    <li><a href="{{ url('/view/all/purchase-product/charge') }}"
                            data-active-paths="{{ url('/view/all/purchase-product/charge') }}, {{ url('/add/new/purchase-product/charge') }}, {{ url('/edit/purchase-product/charge/*') }}">
                            Other Charge Types
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/all/purchase-product/quotation'))
                    <li>
                        <a href="{{ url('/view/all/purchase-product/quotation') }}"
                            data-active-paths="{{ url('/view/all/purchase-product/quotation') }}, {{ url('/add/new/purchase-product/quotation') }}, {{ url('/edit/purchase-product/quotation/*') }}, {{ url('edit/purchase-product/sales/quotation/*') }}">
                            View All Quotations
                            <span style="color:lightgreen" title="Total Product Purchase Quotations">
                                ({{ DB::table('product_purchase_quotations')->count() }})
                            </span>
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/all/purchase-product/order'))
                    <li>
                        <a href="{{ url('/view/all/purchase-product/order') }}"
                            data-active-paths="{{ url('/view/all/purchase-product/order') }}, {{ url('/add/new/purchase-product/order') }}, {{ url('/edit/purchase-product/order/*') }}, {{ url('edit/purchase-product/sales/order/*') }}">
                            View All Orders
                            <span style="color:lightgreen" title="Total Product Purchase Orders">
                                ({{ DB::table('product_purchase_orders')->count() }})
                            </span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>

        <li>
            @php
                $reportRoutes = ['product/purchase/report'];
                $isReportActive = false;
                foreach ($reportRoutes as $route) {
                    if (checkAuth($route)) {
                        $isReportActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isReportActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-printer"></i><span>Generate
                        Report</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('product/purchase/report'))
                    <li><a href="{{ url('product/purchase/report') }}"
                            data-active-paths="{{ url('/product/purchase/report') }}">Product Purchase Report</a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    {{-- accounts module --}}
    @if ($accountModule->count())
        <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
        <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Accounts Modules</li>

        <li>
            @if (checkAuth('view/all/payment-type'))
                <a href="{{ url('/view/all/payment-type') }}"
                    data-active-paths="{{ url('/view/all/payment-type') }}, {{ url('/add/new/payment-type') }}, {{ url('/edit/payment-type/*') }}">
                    <i class="feather-box"></i> Payment Types
                    <span style="color:lightgreen" title="Total CS Types">
                        ({{ DB::table('db_paymenttypes')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>

            @if (checkAuth('view/all/expense-category'))
                <a href="{{ url('/view/all/expense-category') }}"
                    data-active-paths="{{ url('/view/all/expense-category') }}, {{ url('/add/new/expense-category') }}, {{ url('/edit/expense-category/*') }}">
                    <i class="feather-box"></i> Expense Categories
                    <span style="color:lightgreen" title="Total Categories">
                        ({{ DB::table('db_expense_categories')->count() }})
                    </span>
                </a>
            @endif

        </li>
        <li>
            @if (checkAuth('view/all/ac-account'))
                <a href="{{ url('/view/all/ac-account') }}"
                    data-active-paths="{{ url('/view/all/ac-account') }}, {{ url('/add/new/ac-account') }}, {{ url('/edit/ac-account/*') }}">
                    <i class="feather-box"></i> All Accounts
                    <span style="color:lightgreen" title="Total Accounts">
                        ({{ DB::table('ac_accounts')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/expense'))
                <a href="{{ route('ViewAllExpense') }}"
                    data-active-paths="{{ route('ViewAllExpense') }}, {{ url('/add/new/expense') }}, {{ url('/edit/expense/*') }}">
                    <i class="feather-box"></i> All Expenses
                    <span style="color:lightgreen" title="Total Expenses">
                        ({{ DB::table('db_expenses')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/deposit'))
                <a href="{{ route('ViewAllDeposit') }}"
                    data-active-paths="{{ route('ViewAllDeposit') }}, {{ url('/add/new/deposit') }}, {{ url('/edit/deposit/*') }}">
                    <i class="feather-box"></i> All Deposits
                    <span style="color:lightgreen" title="Total Deposits">
                        ({{ DB::table('ac_transactions')->count() }})
                    </span>
                </a>
            @endif
        </li>


        <li>
            @php
                $reportRoutes = ['ledger/journal', 'ledger', 'ledger/balance-sheet', 'ledger/income-statement'];
                $isReportActive = false;
                foreach ($reportRoutes as $route) {
                    if (checkAuth($route)) {
                        $isReportActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isReportActive)
                <a href="javascript: void(0);" class="has-arrow"><i
                        class="feather-settings"></i><span>Reports</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('ledger/journal'))
                    <li>
                        <a href="{{ route('journal.index') }}" data-active-paths="{{ route('journal.index') }}">
                            <i class="feather-box"></i>
                            <span>Journal</span>
                        </a>
                    </li>
                @endif
                @if (checkAuth('ledger'))
                    <li>
                        <a href="{{ route('ledger.index') }}" data-active-paths="{{ route('ledger.index') }}">
                            <i class="feather-box"></i>
                            <span>Ledger</span>
                        </a>
                    </li>
                @endif
                @if (checkAuth('ledger/balance-sheet'))
                    <li>
                        <a href="{{ route('ledger.balance_sheet') }}"
                            data-active-paths="{{ route('ledger.balance_sheet') }}">
                            <i class="feather-box"></i>
                            <span>Balance Sheet</span>
                        </a>
                    </li>
                @endif
                @if (checkAuth('ledger/income/statement'))
                    <li>
                        <a href="{{ route('ledger.income_statement') }}"
                            data-active-paths="{{ route('ledger.income_statement') }}">
                            <i class="feather-box"></i>
                            <span>Income Statement</span>
                        </a>
                    </li>
                @endif
            </ul>
        </li>
    @endif

    {{-- crm module --}}
    @if ($crmModule->count())
        <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
        <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">CRM Modules</li>
        <li>
            @if (checkAuth('view/all/customer-source'))
                <a href="{{ url('/view/all/customer-source') }}"
                    data-active-paths="{{ url('/view/all/customer-source') }}, {{ url('/add/new/customer-source') }}, {{ url('/edit/customer-source/*') }}">
                    <i class="feather-box"></i> Customer Src Type
                    <span style="color:lightgreen" title="Total CS Types">
                        ({{ DB::table('customer_source_types')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>

            @if (checkAuth('view/all/customer-category'))
                <a href="{{ url('/view/all/customer-category') }}"
                    data-active-paths="{{ url('/view/all/customer-category') }}, {{ url('/add/new/customer-category') }}, {{ url('/edit/customer-category/*') }}">
                    <i class="feather-box"></i> Customer Category
                    <span style="color:lightgreen" title="Total Categories">
                        ({{ DB::table('customer_categories')->count() }})
                    </span>
                </a>
            @endif

        </li>
        <li>
            @if (checkAuth('view/all/customer'))
                <a href="{{ url('/view/all/customer') }}"
                    data-active-paths="{{ url('/view/all/customer') }}, {{ url('/add/new/customers') }}, {{ url('/edit/customers/*') }}">
                    <i class="feather-box"></i> Customers
                    <span style="color:lightgreen" title="Total Customers">
                        ({{ DB::table('customers')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/customer-ecommerce'))
                <a href="{{ route('ViewAllCustomerEcommerce') }}"
                    data-active-paths="{{ route('ViewAllCustomerEcommerce') }}, {{ url('/add/new/customer-ecommerce') }}, {{ url('/edit/customer-ecommerce/*') }}">
                    <i class="feather-box"></i> E-Customer
                    <span style="color:lightgreen" title="Total Contact Histories">
                        ({{ DB::table('users')->where('user_type', 3)->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/customer-contact-history'))
                <a href="{{ route('ViewAllCustomerContactHistories') }}"
                    data-active-paths="{{ route('ViewAllCustomerContactHistories') }}, {{ url('/add/new/customer-contact-history') }}, {{ url('/edit/customer-contact-history/*') }}">
                    <i class="feather-box"></i> Contacts History
                    <span style="color:lightgreen" title="Total Contact Histories">
                        ({{ DB::table('customer_contact_histories')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/customer-next-contact-date'))
                <a href="{{ url('/view/all/customer-next-contact-date') }}"
                    data-active-paths="{{ url('/view/all/customer-next-contact-date') }}, {{ url('/add/new/customer-next-contact-date') }}, {{ url('/edit/customer-next-contact-date/*') }}">
                    <i class="feather-box"></i> Next Date Contacts
                    <span style="color:lightgreen" title="Total Contact Histories">
                        ({{ DB::table('customer_next_contact_dates')->count() }})
                    </span>
                </a>
            @endif
        </li>

        <li>
            @php
                $supportTicketRoutes = [
                    'pending/support/tickets',
                    'solved/support/tickets',
                    'on/hold/support/tickets',
                    'rejected/support/tickets',
                ];
                $isSupportTicketActive = false;
                foreach ($supportTicketRoutes as $route) {
                    if (checkAuth($route)) {
                        $isSupportTicketActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isSupportTicketActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="fas fa-headset"></i><span>Support
                        Ticket</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('pending/support/tickets'))
                    <li>
                        <a style="color: skyblue !important;" href="{{ url('/pending/support/tickets') }}"
                            data-active-paths="{{ url('/pending/support/tickets') }}, {{ url('view/support/messages/*') }}">
                            Pending Supports
                            (@php
                                echo DB::table('support_tickets')->where('status', 0)->orWhere('status', 1)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('solved/support/tickets'))
                    <li>
                        <a style="color: #0c0 !important;" href="{{ url('/solved/support/tickets') }}"
                            data-active-paths="{{ url('/solved/support/tickets') }},{{ url('view/support/messages/*') }}">
                            Solved Supports
                            (@php
                                echo DB::table('support_tickets')->where('status', 2)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('on/hold/support/tickets'))
                    <li>
                        <a style="color: goldenrod !important;" href="{{ url('/on/hold/support/tickets') }}"
                            data-active-paths="{{ url('/on/hold/support/tickets') }},{{ url('view/support/messages/*') }}">
                            On Hold Supports
                            (@php
                                echo DB::table('support_tickets')->where('status', 4)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
                @if (checkAuth('rejected/support/tickets'))
                    <li>
                        <a style="color: red !important;" href="{{ url('/rejected/support/tickets') }}"
                            data-active-paths="{{ url('/rejected/support/tickets') }},{{ url('view/support/messages/*') }}">
                            Rejected Supports
                            (@php
                                echo DB::table('support_tickets')->where('status', 3)->count();
                            @endphp)
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        @if (checkAuth('view/all/contact/requests'))
            <li>
                <a href="{{ url('/view/all/contact/requests') }}"
                    data-active-paths="{{ url('/view/all/contact/requests') }}">
                    <i class="feather-phone-forwarded"></i>
                    <span>Contact Request</span>
                </a>
            </li>
        @endif
        @if (checkAuth('view/all/subscribed/users'))
            <li>
                <a href="{{ url('/view/all/subscribed/users') }}"
                    data-active-paths="{{ url('/view/all/subscribed/users') }}">
                    <i class="feather-user-check"></i>
                    <span>Subscribed Users</span>
                </a>
            </li>
        @endif
    @endif

    {{-- role permission module --}}
    @if ($rolePermissionModule->count())
        <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 5px;">
        <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">User Role Permission</li>
        @if (checkAuth('view/system/users'))
            <li>
                <a href="{{ url('/view/system/users') }}"
                    data-active-paths="{{ url('/view/system/users') }}, {{ url('add/new/system/user') }}, {{ url('edit/system/user/*') }}">
                    <i class="fas fa-user-shield"></i>
                    <span>System Users</span>
                </a>
            </li>
        @endif
        @if (checkAuth('view/user/roles'))
            <li>
                <a href="{{ url('/view/user/roles') }}"
                    data-active-paths="{{ url('/view/user/roles') }}, {{ url('/new/user/role') }}, {{ url('/edit/user/role/*') }}">
                    <i class="feather-user-plus"></i>
                    <span>User Roles</span>
                </a>
            </li>
        @endif
        @if (checkAuth('view/user/role/permission'))
            <li>
                <a href="{{ url('/view/user/role/permission') }}"
                    data-active-paths="{{ url('/view/user/role/permission') }}, {{ url('/assign/role/permission/*') }}">
                    <i class="mdi mdi-security"></i>
                    <span>Assign Role Permission</span>
                </a>
            </li>
        @endif
        @if (checkAuth('view/permission/routes'))
            <li>
                <a href="{{ url('/view/permission/routes') }}"
                    data-active-paths="{{ url('/view/permission/routes') }}">
                    <i class="feather-git-merge"></i>
                    <span>Permission Routes</span>
                </a>
            </li>
        @endif
    @endif

    {{-- website config module --}}
    @if ($websiteConfigModule->count())
        <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 5px;">
        <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Website Config</li>

        @if (checkAuth('general/info'))
            <li>
                <a href="{{ url('/general/info') }}" data-active-paths="{{ url('/general/info') }}">
                    <i class="feather-grid"></i>
                    <span>General Info</span>
                </a>
            </li>
        @endif
        @if (checkAuth('website/theme/page'))
            <li>
                <a href="{{ url('/website/theme/page') }}" data-active-paths="{{ url('/website/theme/page') }}">
                    <i class="mdi mdi-format-color-fill" style="font-size: 18px"></i>
                    <span>Website Theme Color</span>
                </a>
            </li>
        @endif
        @if (checkAuth('social/media/page'))
            <li>
                <a href="{{ url('/social/media/page') }}" data-active-paths="{{ url('/social/media/page') }}">
                    <i class="mdi mdi-link-variant" style="font-size: 17px"></i>
                    <span>Social Media Links</span>
                </a>
            </li>
        @endif
        @if (checkAuth('seo/homepage'))
            <li>
                <a href="{{ url('/seo/homepage') }}" data-active-paths="{{ url('/seo/homepage') }}">
                    <i class="dripicons-search"></i>
                    <span>Home Page SEO</span>
                </a>
            </li>
        @endif
        @if (checkAuth('custom/css/js'))
            <li>
                <a href="{{ url('/custom/css/js') }}" data-active-paths="{{ url('/custom/css/js') }}">
                    <i class="feather-code"></i>
                    <span>Custom CSS & JS</span>
                </a>
            </li>
        @endif
        @if (checkAuth('social/chat/script/page'))
            <li>
                <a href="{{ url('/social/chat/script/page') }}"
                    data-active-paths="{{ url('/social/chat/script/page') }}">
                    <i class="mdi mdi-code-brackets"></i>
                    <span>Social & Chat Scripts</span>
                </a>
            </li>
        @endif
    @endif


    @if ($cmsModule->count())
        <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
        <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Content Management</li>

        <li>
            @php
                $sliderBannerRoutes = [
                    'view/all/sliders',
                    'view/all/banners',
                    'view/promotional/banner',
                    'view/all/side-banner',
                ];
                $isSliderBannerActive = false;
                foreach ($sliderBannerRoutes as $route) {
                    if (checkAuth($route)) {
                        $isSliderBannerActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isSliderBannerActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-image"></i><span>Sliders &
                        Banners</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('view/all/sliders'))
                    <li>
                        <a href="{{ url('/view/all/sliders') }}"
                            data-active-paths="{{ url('/view/all/sliders') }}, {{ url('/add/new/slider') }}, 
                                                                {{ url('/edit/slider/*') }}, {{ url('/rearrange/slider') }}">
                            View All Sliders
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/all/banners'))
                    <li>
                        <a href="{{ url('/view/all/banners') }}"
                            data-active-paths="{{ url('/view/all/banners') }}, {{ url('/add/new/banner') }}, 
                                                 {{ url('/edit/banner/*') }}, {{ url('/rearrange/banners') }}">
                            View All Banners
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/promotional/banner'))
                    <li>
                        <a href="{{ url('/view/promotional/banner') }}"
                            data-active-paths="{{ url('/view/promotional/banner') }}">
                            Promotional Banner
                        </a>
                    </li>
                @endif
                @if (checkAuth('view/all/side-banner'))
                    <li>
                        <a href="{{ url('/view/all/side-banner') }}"
                            data-active-paths="{{ url('/view/all/side-banner') }}, {{ url('/add/new/side-banner') }}, {{ url('edit/side-banner/*') }}">
                            Side Banner
                        </a>
                    </li>
                @endif
            </ul>
        </li>
        <li>
            @if (checkAuth('view/testimonials'))
                <a href="{{ url('/view/testimonials') }}"
                    data-active-paths="{{ url('/view/testimonials') }}, 
                        {{ url('/add/testimonial') }}, {{ url('/edit/testimonial/*') }}">
                    <i class="feather-message-square"></i>
                    <span>Testimonials</span>
                </a>
            @endif
        </li>
        <li>
            @php
                $blogRoutes = ['blog/categories', 'add/new/blog', 'view/all/blogs'];
                $isBlogActive = false;
                foreach ($blogRoutes as $route) {
                    if (checkAuth($route)) {
                        $isBlogActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isBlogActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-file-text"></i><span>Manage
                        Blogs</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('blog/categories'))
                    <li><a href="{{ url('/blog/categories') }}"
                            data-active-paths="{{ url('/blog/categories') }}, {{ url('/rearrange/blog/category') }}">Blog
                            Categories</a></li>
                @endif
                @if (checkAuth('add/new/blog'))
                    <li><a href="{{ url('/add/new/blog') }}" data-active-paths="{{ url('/add/new/blog') }}">Write
                            a Blog</a>
                    </li>
                @endif
                @if (checkAuth('view/all/blogs'))
                    <li><a href="{{ url('/view/all/blogs') }}"
                            data-active-paths="{{ url('/view/all/blogs') }}, {{ url('/edit/blog/*') }}">View All
                            Blogs</a></li>
                @endif
            </ul>
        </li>
        <li>
            @php
                $termsRoutes = [
                    'terms/and/condition',
                    'view/privacy/policy',
                    'view/shipping/policy',
                    'view/return/policy',
                ];
                $isTermsActive = false;
                foreach ($termsRoutes as $route) {
                    if (checkAuth($route)) {
                        $isTermsActive = true;
                        break;
                    }
                }
            @endphp
            @if ($isTermsActive)
                <a href="javascript: void(0);" class="has-arrow"><i class="feather-alert-triangle"></i><span>Terms &
                        Policies</span></a>
            @endif
            <ul class="sub-menu" aria-expanded="false">
                @if (checkAuth('terms/and/condition'))
                    <li><a href="{{ url('/terms/and/condition') }}"
                            data-active-paths="{{ url('/terms/and/condition') }}">Terms
                            & Condition</a></li>
                @endif
                @if (checkAuth('view/privacy/policy'))
                    <li><a href="{{ url('/view/privacy/policy') }}"
                            data-active-paths="{{ url('/view/privacy/policy') }}">Privacy Policy</a></li>
                @endif
                @if (checkAuth('view/shipping/policy'))
                    <li><a href="{{ url('/view/shipping/policy') }}"
                            data-active-paths="{{ url('/view/shipping/policy') }}">Shipping Policy</a></li>
                @endif
                @if (checkAuth('view/return/policy'))
                    <li><a href="{{ url('/view/return/policy') }}"
                            data-active-paths="{{ url('/view/return/policy') }}">Return
                            Policy</a></li>
                @endif
            </ul>
        </li>
        <li>
            @if (checkAuth('view/all/pages'))
                <a href="{{ url('/view/all/pages') }}"
                    data-active-paths="{{ url('/view/all/pages') }}, {{ url('/create/new/page') }}, {{ url('edit/custom/page/*') }}">
                    <i class="feather-file-plus"></i>
                    <span>Custom Pages</span>
                    <span style="color:lightgreen" title="Total Outlets">
                        ({{ DB::table('custom_pages')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/outlet'))
                <a href="{{ url('/view/all/outlet') }}"
                    data-active-paths="{{ url('/view/all/outlet') }}, {{ url('/add/new/outlet') }}, {{ url('/edit/outlet/*') }}">
                    <i class="feather-box"></i> View All Outlets
                    <span style="color:lightgreen" title="Total Outlets">
                        ({{ DB::table('outlets')->count() }})
                    </span>
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('view/all/video-gallery'))
                <a href="{{ url('/view/all/video-gallery') }}"
                    data-active-paths="{{ url('/view/all/video-gallery') }}, {{ url('/add/new/video-gallery') }}, 
                    {{ url('/edit/video-gallery/*') }}">
                    <i class="feather-box"></i> View All Videos
                    <span style="color:lightgreen" title="Total Videos">
                        ({{ DB::table('video_galleries')->count() }})
                    </span>
                </a>
            @endif
        </li>

        @if (checkAuth('about/us/page'))
            <li>
                <a href="{{ url('/about/us/page') }}" data-active-paths="{{ url('/about/us/page') }}">
                    <i class="feather-globe"></i>
                    <span>About Us</span>
                </a>
            </li>
        @endif
        @if (checkAuth('view/all/faqs'))
            <li>
                <a href="{{ url('/view/all/faqs') }}"
                    data-active-paths="{{ url('/view/all/faqs') }}, {{ url('/add/new/faq') }}, {{ url('/edit/faq/*') }}">
                    <i class="far fa-question-circle"></i>
                    <span>FAQ's</span>
                </a>
            </li>
        @endif
    @endif


    {{-- download & backup module --}}
    @if ($backupModule->count())
        <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
        <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Download & Backup</li>


        <li>
            @if (checkAuth('download/database/backup'))
                <a href="{{ url('/download/database/backup') }}"
                    onclick="return confirm('Are you sure you want to download the database backup?');">
                    <i class="feather-database"></i>
                    Database Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/product/files/backup'))
                <a href="{{ url('/download/product/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the product images backup?');">
                    <i class="feather-image"></i>Product Images Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/user/files/backup'))
                <a href="{{ url('/download/user/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the user images backup?');">
                    <i class="feather-user"></i>User Images Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/banner/files/backup'))
                <a href="{{ url('/download/banner/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the banner images backup?');">
                    <i class="feather-layers"></i>Banner Images Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/category/files/backup'))
                <a href="{{ url('/download/category/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the category icon backup?');">
                    <i class="feather-grid"></i>Category Icon Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/subcategory/files/backup'))
                <a href="{{ url('/download/subcategory/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the subcategory backup?');">
                    <i class="feather-list"></i>Subcategory Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/flag/files/backup'))
                <a href="{{ url('/download/flag/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the flag icon backup?');">
                    <i class="feather-flag"></i>Flag Icon Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/ticket/files/backup'))
                <a href="{{ url('/download/ticket/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the ticket files backup?');">
                    <i class="feather-file"></i>Ticket Files Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/blog/files/backup'))
                about:blank#blocked
                <a href="{{ url('/download/blog/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the blog files backup?');">
                    <i class="feather-file-text"></i>Blog Files Backup
                </a>
            @endif
        </li>
        <li>
            @if (checkAuth('download/other/files/backup'))
                <a href="{{ url('/download/other/files/backup') }}"
                    onclick="return confirm('Are you sure you want to download the other images backup?');">
                    <i class="feather-folder"></i>Other Images Backup
                </a>
            @endif
        </li>
    @endif


    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li>
        @php
            $demoProductRoutes = ['generate/demo/products', 'remove/demo/products/page'];
            $isDemoProductActive = false;
            foreach ($demoProductRoutes as $route) {
                if (checkAuth($route)) {
                    $isDemoProductActive = true;
                    break;
                }
            }
        @endphp
        @if ($isDemoProductActive)
            <a href="javascript: void(0);" class="has-arrow"><i class="feather-box"></i><span>Demo
                    Products</span></a>
        @endif
        <ul class="sub-menu" aria-expanded="false">
            @if (checkAuth('generate/demo/products'))
                <li>
                    <a href="{{ url('/generate/demo/products') }}"
                        data-active-paths="{{ url('/generate/demo/products') }}">
                        Generate Products
                    </a>
                </li>
            @endif
            @if (checkAuth('remove/demo/products/page'))
                <li>
                    <a href="{{ url('/remove/demo/products/page') }}"
                        data-active-paths="{{ url('/remove/demo/products/page') }}">
                        Products
                    </a>
                </li>
            @endif
        </ul>
    </li>

    @if (checkAuth('clear/cache'))
        <li><a href="{{ url('/clear/cache') }}"><i class="feather-rotate-cw"></i><span>Clear Cache</span></a></li>
    @endif

    <li>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="feather-log-out"></i><span>Logout</span>
        </a>
    </li>

</ul>
