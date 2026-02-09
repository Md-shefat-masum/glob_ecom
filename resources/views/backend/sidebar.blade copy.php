<!-- Left Menu Start -->
{{-- <div style="padding: 10px;">
    <input type="text" id="menuSearch" placeholder="Search menu..."
        style="width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 5px;">
</div> --}}

<ul class="metismenu list-unstyled" id="side-menu">
    <li>
        <a href="{{ url('/home') }}" data-active-paths="{{ url('/home') }}">
            <i class="feather-home"></i>
            <span> Ecommerce Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ route('analytics.dashboard') }}" data-active-paths="{{ route('analytics.dashboard') }}">
            <i class="feather-bar-chart-2"></i>
            <span> 🍃 Organic Analytics </span>
        </a>
    </li>
    <li>
        <a href="{{ url('/crm-home') }}" data-active-paths="{{ url('/crm-home') }}">
            <i class="feather-headphones"></i>
            <span> CRM Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/accounts-home') }}" data-active-paths="{{ url('/accounts-home') }}">
            <i class="feather-dollar-sign"></i>
            <span> Accounts Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/inventory-home') }}" data-active-paths="{{ url('/inventory-home') }}">
            <i class="feather-shield"></i>
            <span> Inventory Dashboard</span>
        </a>
    </li>

    {{-- Start E-commerce Module --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">
        Product &amp; Sales
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-settings"></i>
            <span>Product Attributes</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            {{-- Fshion Insdustry --}}
            @if (DB::table('config_setups')->where('code', 'product_size')->first())
                <li>
                    <a href="{{ url('/view/all/sizes') }}"
                        data-active-paths="{{ url('/view/all/sizes') }},{{ url('/rearrange/size') }}">
                        Product Sizes
                    </a>
                </li>
            @endif

            {{-- common --}}
            @if (DB::table('config_setups')->where('code', 'color')->first())
                <li>
                    <a href="{{ url('/view/all/colors') }}" data-active-paths="{{ url('/view/all/colors') }}">
                        Product Colors
                    </a>
                </li>
            @endif

            @if (DB::table('config_setups')->where('code', 'measurement_unit')->first())
                <li>
                    <a href="{{ url('/view/all/units') }}" data-active-paths="{{ url('/view/all/units') }}">
                        Measurement Units
                    </a>
                </li>
            @endif

            <li>
                <a href="{{ url('/view/all/brands') }}"
                    data-active-paths="{{ url('/view/all/brands') }},{{ url('/add/new/brand') }},{{ url('/rearrange/brands') }},{{ url('edit/brand/*') }}">
                    Product Brands
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/models') }}"
                    data-active-paths="{{ url('/view/all/models') }}, {{ url('add/new/model') }},{{ url('edit/model/*') }}">
                    Models of Brand
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/flags') }}" data-active-paths="{{ url('/view/all/flags') }}">
                    Product Flags
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/warrenties') }}" data-active-paths="{{ url('/view/all/warrenties') }}">
                    Product Warranties
                </a>
            </li>
        </ul>
    </li>

    {{-- new product management --}}
    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-box"></i>
            <span>Product Management</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('product-management.index') }}" data-active-paths="{{ route('product-management.index') }}">
                    <i class="fas fa-list"></i> All Products
                </a>
            </li>
            <li>
                <a href="{{ route('product-management.create') }}" data-active-paths="{{ route('product-management.create') }}">
                    <i class="fas fa-plus-circle"></i> Create New
                </a>
            </li>
        </ul>
    </li>

    {{-- stock adjustment modules --}}
    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-trending-up"></i>
            <span>Stock Adjustment</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('stock-adjustment.index') }}" data-active-paths="{{ route('stock-adjustment.index') }}">
                    <i class="fas fa-list"></i> Adjustment Logs
                </a>
            </li>
            <li>
                <a href="{{ route('stock-adjustment.create') }}" data-active-paths="{{ route('stock-adjustment.create') }}">
                    <i class="fas fa-plus-circle"></i> New Adjustment
                </a>
            </li>
        </ul>
    </li>

    {{-- product return modules --}}
    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-rotate-ccw"></i>
            <span>Product Returns</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('ViewAllProductOrderReturns') }}">
                    <i class="fas fa-list"></i> Order Returns
                </a>
            </li>
            <li>
                <a href="{{ route('CreateManualProductReturn') }}">
                    <i class="fas fa-plus-circle"></i> Manual Return
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllManualProductReturns') }}">
                    <i class="fas fa-list"></i> View Manual Returns
                </a>
            </li>
        </ul>
    </li>
    
    {{-- customer payment modules --}}
    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-dollar-sign"></i>
            <span>Customer Payments</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('CreateCustomerPayment') }}">
                    Add Payment/Advance
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllCustomerPayments') }}">
                    View All Payments
                </a>
            </li>
            <li>
                <a href="{{ route('CreateCustomerPaymentReturn') }}">
                    Payment Return/Refund
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="{{ url('/view/all/category') }}"
            data-active-paths="{{ url('/view/all/category') }},{{ url('/add/new/category') }},{{ url('/edit/category/*') }},{{ url('/rearrange/category') }}">
            <i class="feather-sliders"></i>
            <span>Category</span>
            <span style="color:lightgreen" title="Total Products">
                ({{ DB::table('categories')->count() }})
            </span>
        </a>
    </li>

    <li>
        <a href="{{ url('/view/all/subcategory') }}"
            data-active-paths="{{ url('/view/all/subcategory') }},{{ url('/add/new/subcategory') }},{{ url('/edit/subcategory/*') }},{{ url('/rearrange/subcategory') }}">
            <i class="feather-command"></i>
            <span>Subcategory</span>
            <span style="color:lightgreen" title="Total Products">
                ({{ DB::table('subcategories')->count() }})
            </span>
        </a>
        {{-- <ul class="sub-menu" aria-expanded="false">
            <li><a href="{{ url('/add/new/subcategory') }}">Add New Subcategory</a></li>
            <li><a href="{{ url('/view/all/subcategory') }}">View All Subcategories</a></li>
        </ul> --}}
    </li>
    <li>
        <a href="{{ url('/view/all/childcategory') }}"
            data-active-paths="{{ url('/view/all/childcategory') }},{{ url('/add/new/childcategory') }},{{ url('/edit/childcategory/*') }},{{ url('/rearrange/childcategory') }}">
            <i class="feather-git-pull-request"></i><span>Child Category</span>
            <span style="color:lightgreen" title="Total Products">
                ({{ DB::table('child_categories')->count() }})
            </span>

        </a>
    </li>
    
    <li>
        <a href="{{ url('/view/product/reviews') }}" data-active-paths="{{ url('/view/product/reviews') }}">
            Products's Review
            <span style="color:goldenrod" title="Indicate Pending Review">
                (@php
                    echo DB::table('product_reviews')->where('status', 0)->count();
                @endphp)
            </span>
        </a>
    </li>
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
    <li>
        <a href="{{ url('/barcode_gen') }}" data-active-paths="{{ url('/barcode_gen') }}">
            Product Barcode Gen
        </a>
    </li>

    <li>
        <a href="{{ url('/package-products') }}"
            data-active-paths="{{ url('/package-products') }}, {{ url('/package-products/create') }}, {{ url('/package-products/*/edit') }}, {{ url('/package-products/*/manage-items') }}">
            <i class="feather-package"></i> Package Products
            <span style="color:lightgreen" title="Total Package Products">
                ({{ DB::table('products')->where('is_package', true)->count() }})
            </span>
        </a>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-shopping-cart"></i>
            <span>Manage Orders</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a style="color: white !important;" href="{{ url('/view/all/product-order/manage') }}"
                    data-active-paths="{{ url('/view/all/product-order/manage') }}, {{ url('/add/new/product-order/manage') }}">
                    All Orders
                    (@php echo DB::table('orders')->count(); @endphp)
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="{{ url('/view/all/promo/codes') }}"
            data-active-paths="{{ url('/view/all/promo/codes') }},{{ url('/add/new/code') }},{{ url('/edit/promo/code/*') }}">
            <i class="feather-gift"></i>
            <span>Promo Codes</span>
            <span style="color:lightgreen" title="Total Products">
                ({{ DB::table('promo_codes')->count() }})
            </span>
        </a>
    </li>

    <li>
        <a href="{{ url('/view/customers/wishlist') }}" data-active-paths="{{ url('/view/customers/wishlist') }}">
            <i class="feather-heart"></i>
            <span>Customer's Wishlist</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/delivery/charges') }}" data-active-paths="{{ url('/view/delivery/charges') }}">
            <i class="feather-truck"></i>
            <span>Delivery Charges</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/upazila/thana') }}" data-active-paths="{{ url('/view/upazila/thana') }}">
            <i class="dripicons-location"></i>
            <span>Upazila & Thana</span>
        </a>
    </li>
    {{-- End E-commerce Module --}}


    {{-- Start Inventory Module --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Inventory Modules</li>
    <li>
        <a href="{{ url('/view/all/product-warehouse') }}"
            data-active-paths="{{ url('/view/all/product-warehouse') }}, {{ url('/add/new/product-warehouse') }}, {{ url('/edit/product-warehouse/*') }}">
            <i class="feather-box"></i>
            <span>Product Warehouse</span>
            <span style="color:lightgreen" title="Total Product Warehouses">
                ({{ DB::table('product_warehouses')->count() }})
            </span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/all/product-warehouse-room') }}"
            data-active-paths="{{ url('/view/all/product-warehouse-room') }}, {{ url('/add/new/product-warehouse-room') }}, {{ url('/edit/product-warehouse-room/*') }}">
            <i class="feather-box"></i>Warehouse Room
            <span style="color:lightgreen" title="Total Product Warehouse Rooms">
                ({{ DB::table('product_warehouse_rooms')->count() }})
            </span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/all/product-warehouse-room-cartoon') }}"
            data-active-paths="{{ url('/view/all/product-warehouse-room-cartoon') }}, {{ url('/add/new/product-warehouse-room-cartoon') }}, {{ url('/edit/product-warehouse-room-cartoon/*') }}">
            <i class="feather-box"></i> Room Cartoon
            <span style="color:lightgreen" title="Total Product Warehouse Room cartoons">
                ({{ DB::table('product_warehouse_room_cartoons')->count() }})
            </span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/all/supplier-source') }}"
            data-active-paths="{{ url('/view/all/supplier-source') }}, {{ url('/add/new/supplier-source') }}, {{ url('/edit/supplier-source/*') }}">
            <i class="feather-box"></i> Supplier Types
            <span style="color:lightgreen" title="Total CS Types">
                ({{ DB::table('supplier_source_types')->count() }})
            </span>
        </a>
    </li>

    <li>
        <a href="{{ url('/view/all/product-supplier') }}"
            data-active-paths="{{ url('/view/all/product-supplier') }}, {{ url('/add/new/product-supplier') }}, {{ url('/edit/product-supplier/*') }}">
            <i class="feather-box"></i> Product Suppliers
            <span style="color:lightgreen" title="Total Product Suppliers">
                ({{ DB::table('product_suppliers')->count() }})
            </span>
        </a>
    </li>


    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-box"></i>
            <span>Product Purchase</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/purchase-product/charge') }}"
                    data-active-paths="{{ url('/view/all/purchase-product/charge') }}, {{ url('/add/new/purchase-product/charge') }}, {{ url('/edit/purchase-product/charge/*') }}">
                    Other Charge Types
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/purchase-product/quotation') }}"
                    data-active-paths="{{ url('/view/all/purchase-product/quotation') }}, {{ url('/add/new/purchase-product/quotation') }}, {{ url('/edit/purchase-product/quotation/*') }}, {{ url('edit/purchase-product/sales/quotation/*') }}">
                    View All Quotations
                    <span style="color:lightgreen" title="Total Product Purchase Quotations">
                        ({{ DB::table('product_purchase_quotations')->count() }})
                    </span>
                </a>
            </li>
            {{-- <a href="javascript: void(0);" class="has-arrow"><i class="feather-box"></i><span>Order</span></a> --}}

            <li>
                <a href="{{ url('/view/all/purchase-product/order') }}"
                    data-active-paths="{{ url('/view/all/purchase-product/order') }}, {{ url('/add/new/purchase-product/order') }}, {{ url('/edit/purchase-product/order/*') }}, {{ url('edit/purchase-product/sales/order/*') }}">
                    View All Orders
                    <span style="color:lightgreen" title="Total Product Purchase Orders">
                        ({{ DB::table('product_purchase_orders')->count() }})
                    </span>
                </a>
            </li>

            <li>
                <a href="{{ url('/view/all/purchase-return/order') }}"
                    data-active-paths="{{ url('/view/all/purchase-return/order') }}, {{ url('/add/new/purchase-return/order') }}, {{ url('/edit/purchase-return/order/*') }}, {{ url('edit/purchase-return/sales/order/*') }}">
                    View All Returns
                    <span style="color:lightgreen" title="Total return Purchase Returns">
                        ({{ DB::table('product_purchase_returns')->count() }})
                    </span>
                </a>
            </li>

        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-printer"></i>
            <span>Generate Report</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/product/purchase/report') }}"
                    data-active-paths="{{ url('/product/purchase/report') }}">
                    Product Purchase Report
                </a>
            </li>
        </ul>
    </li>
    {{-- End Inventory Module --}}


    {{-- Start Accounts Module --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Accounts Modules</li>

    <li>
        <a href="{{ url('/view/all/payment-type') }}"
            data-active-paths="{{ url('/view/all/payment-type') }}, {{ url('/add/new/payment-type') }}, {{ url('/edit/payment-type/*') }}">
            <i class="feather-box"></i> Payment Types
            <span style="color:lightgreen" title="Total CS Types">
                ({{ DB::table('db_paymenttypes')->count() }})
            </span>
        </a>
    </li>
    <li>

        <a href="{{ url('/view/all/expense-category') }}"
            data-active-paths="{{ url('/view/all/expense-category') }}, {{ url('/add/new/expense-category') }}, {{ url('/edit/expense-category/*') }}">
            <i class="feather-box"></i> Expense Categories
            <span style="color:lightgreen" title="Total Categories">
                ({{ DB::table('db_expense_categories')->count() }})
            </span>
        </a>

    </li>
    <li>
        <a href="{{ url('/view/all/ac-account') }}"
            data-active-paths="{{ url('/view/all/ac-account') }}, {{ url('/add/new/ac-account') }}, {{ url('/edit/ac-account/*') }}">
            <i class="feather-box"></i> All Accounts
            <span style="color:lightgreen" title="Total Accounts">
                ({{ DB::table('ac_accounts')->count() }})
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('ViewAllExpense') }}"
            data-active-paths="{{ route('ViewAllExpense') }}, {{ url('/add/new/expense') }}, {{ url('/edit/expense/*') }}">
            <i class="feather-box"></i> All Expenses
            <span style="color:lightgreen" title="Total Expenses">
                ({{ DB::table('db_expenses')->count() }})
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('ViewAllDeposit') }}"
            data-active-paths="{{ route('ViewAllDeposit') }}, {{ url('/add/new/deposit') }}, {{ url('/edit/deposit/*') }}">
            <i class="feather-box"></i> All Deposits
            <span style="color:lightgreen" title="Total Deposits">
                ({{ DB::table('ac_transactions')->count() }})
            </span>
        </a>
    </li>


    <li>
        <a href="javascript: void(0);" class="has-arrow"><i class="feather-settings"></i><span>Reports</span></a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('journal.index') }}" data-active-paths="{{ route('journal.index') }}">
                    <i class="feather-box"></i>
                    <span>Journal</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ledger.index') }}" data-active-paths="{{ route('ledger.index') }}">
                    <i class="feather-box"></i>
                    <span>Ledger</span></a>
            </li>
            <li>
                <a href="{{ route('ledger.balance_sheet') }}"
                    data-active-paths="{{ route('ledger.balance_sheet') }}">
                    <i class="feather-box"></i>
                    <span>Balance Sheet</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ledger.income_statement') }}"
                    data-active-paths="{{ route('ledger.income_statement') }}">
                    <i class="feather-box"></i>
                    <span>Income Statement</span>
                </a>
            </li>
        </ul>
    </li>
    {{-- End Accounts Module --}}

    {{-- Start Crm Module --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">CRM Modules</li>
    <li>
        <a href="{{ url('/view/all/customer-source') }}"
            data-active-paths="{{ url('/view/all/customer-source') }}, {{ url('/add/new/customer-source') }}, {{ url('/edit/customer-source/*') }}">
            <i class="feather-box"></i> Customer Src Type
            <span style="color:lightgreen" title="Total CS Types">
                ({{ DB::table('customer_source_types')->count() }})
            </span>
        </a>
    </li>
    <li>

        <a href="{{ url('/view/all/customer-category') }}"
            data-active-paths="{{ url('/view/all/customer-category') }}, {{ url('/add/new/customer-category') }}, {{ url('/edit/customer-category/*') }}">
            <i class="feather-box"></i> Customer Category
            <span style="color:lightgreen" title="Total Categories">
                ({{ DB::table('customer_categories')->count() }})
            </span>
        </a>

    </li>
    <li>
        <a href="{{ url('/view/all/customer') }}"
            data-active-paths="{{ url('/view/all/customer') }}, {{ url('/add/new/customers') }}, {{ url('/edit/customers/*') }}">
            <i class="feather-box"></i> Customers
            <span style="color:lightgreen" title="Total Customers">
                ({{ DB::table('customers')->count() }})
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('ViewAllCustomerEcommerce') }}"
            data-active-paths="{{ route('ViewAllCustomerEcommerce') }}, {{ url('/add/new/customer-ecommerce') }}, {{ url('/edit/customer-ecommerce/*') }}">
            <i class="feather-box"></i> E-Customer
            <span style="color:lightgreen" title="Total Contact Histories">
                ({{ DB::table('users')->where('user_type', 3)->count() }})
            </span>
        </a>
    </li>
    <li>
        <a href="{{ route('ViewAllCustomerContactHistories') }}"
            data-active-paths="{{ route('ViewAllCustomerContactHistories') }}, {{ url('/add/new/customer-contact-history') }}, {{ url('/edit/customer-contact-history/*') }}">
            <i class="feather-box"></i> Contacts History
            <span style="color:lightgreen" title="Total Contact Histories">
                ({{ DB::table('customer_contact_histories')->count() }})
            </span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/all/customer-next-contact-date') }}"
            data-active-paths="{{ url('/view/all/customer-next-contact-date') }}, {{ url('/add/new/customer-next-contact-date') }}, {{ url('/edit/customer-next-contact-date/*') }}">
            <i class="feather-box"></i> Next Date Contacts
            <span style="color:lightgreen" title="Total Contact Histories">
                ({{ DB::table('customer_next_contact_dates')->count() }})
            </span>
        </a>
    </li>

    <li>
        <a href="{{ url('/view/all/contact/requests') }}"
            data-active-paths="{{ url('/view/all/contact/requests') }}">
            <i class="feather-phone-forwarded"></i>
            <span>Contact Request</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/all/subscribed/users') }}"
            data-active-paths="{{ url('/view/all/subscribed/users') }}">
            <i class="feather-user-check"></i>
            <span>Subscribed Users</span>
        </a>
    </li>
    {{-- End Crm Modules --}}

    {{-- Start User Role Permission Module --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 5px;">
    <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">User Role Permission</li>

    <li>
        <a href="{{ url('/view/system/users') }}"
            data-active-paths="{{ url('/view/system/users') }}, {{ url('add/new/system/user') }}, {{ url('edit/system/user/*') }}">
            <i class="fas fa-user-shield"></i>
            <span>System Users</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/user/roles') }}"
            data-active-paths="{{ url('/view/user/roles') }}, {{ url('/new/user/role') }}, {{ url('/edit/user/role/*') }}">
            <i class="feather-user-plus"></i>
            <span>User Roles</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/user/role/permission') }}"
            data-active-paths="{{ url('/view/user/role/permission') }}, {{ url('/assign/role/permission/*') }}">
            <i class="mdi mdi-security"></i>
            <span>Assign Role Permission</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/view/permission/routes') }}" data-active-paths="{{ url('/view/permission/routes') }}">
            <i class="feather-git-merge"></i>
            <span>Permission Routes</span>
        </a>
    </li>
    {{-- End User Role Permission Module --}}


    {{-- Start Website Config Module --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 5px;">
    <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Website Config</li>

    <li>
        <a href="{{ url('/general/info') }}" data-active-paths="{{ url('/general/info') }}">
            <i class="feather-grid"></i>
            <span>General Info</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/social/media/page') }}" data-active-paths="{{ url('/social/media/page') }}">
            <i class="mdi mdi-link-variant" style="font-size: 17px"></i>
            <span>Social Media Links</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/seo/homepage') }}" data-active-paths="{{ url('/seo/homepage') }}">
            <i class="dripicons-search"></i>
            <span>Home Page SEO</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/custom/css/js') }}" data-active-paths="{{ url('/custom/css/js') }}">
            <i class="feather-code"></i>
            <span>Custom CSS & JS</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/social/chat/script/page') }}" data-active-paths="{{ url('/social/chat/script/page') }}">
            <i class="mdi mdi-code-brackets"></i>
            <span>Social & Chat Scripts</span>
        </a>
    </li>
    {{-- End Website Config Module --}}

    {{-- Start Content Management Module --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: khaki; text-shadow: 1px 1px 2px black;">Content Management</li>
    <li>
        <a href="javascript: void(0);" class="has-arrow"><i class="feather-file-text"></i><span>Manage
                Blogs</span></a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/blog/categories') }}"
                    data-active-paths="{{ url('/blog/categories') }}, {{ url('/rearrange/blog/category') }}">
                    Blog Categories
                </a>
            </li>
            <li>
                <a href="{{ url('/add/new/blog') }}" data-active-paths="{{ url('/add/new/blog') }}">
                    Write a Blog
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/blogs') }}"
                    data-active-paths="{{ url('/view/all/blogs') }}, {{ url('/edit/blog/*') }}">
                    View All Blogs
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-alert-triangle"></i>
            <span>Terms & Policies</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/terms/and/condition') }}" data-active-paths="{{ url('/terms/and/condition') }}">
                    Terms & Condition
                </a>
            </li>
            <li>
                <a href="{{ url('/view/privacy/policy') }}" data-active-paths="{{ url('/view/privacy/policy') }}">
                    Privacy Policy
                </a>
            </li>
            <li>
                <a href="{{ url('/view/shipping/policy') }}"
                    data-active-paths="{{ url('/view/shipping/policy') }}">
                    Shipping Policy
                </a>
            </li>
            <li>
                <a href="{{ url('/view/return/policy') }}" data-active-paths="{{ url('/view/return/policy') }}">
                    Return Policy
                </a>
            </li>
        </ul>
    </li>
    <li>
        <a href="{{ url('/view/all/pages') }}"
            data-active-paths="{{ url('/view/all/pages') }}, {{ url('/create/new/page') }}, {{ url('edit/custom/page/*') }}">
            <i class="feather-file-plus"></i>
            <span>Custom Pages</span>
            <span style="color:lightgreen" title="Total Outlets">
                ({{ DB::table('custom_pages')->count() }})
            </span>
        </a>
    </li>

    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">

    <li>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="feather-log-out"></i><span>Logout</span>
        </a>
    </li>
</ul>

<script>
    function getMenuSection(item) {
        let prev = item.previousElementSibling;
        while (prev) {
            if (prev.classList?.contains('menu-title')) {
                return prev.textContent.trim();
            }
            prev = prev.previousElementSibling;
        }
        return '';
    }

    function getNextMenuTitle(children, index) {
        for (let j = index + 1; j < children.length; j++) {
            const next = children[j];
            if (next.classList?.contains('menu-title')) {
                return next;
            }
            if (next.tagName === 'LI') {
                const section = getMenuSection(next);
                if (section) return children.find(el => el.classList?.contains('menu-title') && el.textContent
                    .trim() === section);
            }
        }
        return null;
    }
</script>
