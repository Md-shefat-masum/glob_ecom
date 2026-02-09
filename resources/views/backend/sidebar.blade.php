<!-- Left Menu Start -->
<ul class="metismenu list-unstyled" id="side-menu">
    
    {{-- DASHBOARDS --}}
    <li class="menu-title" style="color: green;">
        <i class="feather-pie-chart"></i> DASHBOARDS
    </li>
    <li>
        <a href="{{ url('/home') }}" data-active-paths="{{ url('/home') }}">
            <i class="feather-shopping-bag"></i>
            <span>E-commerce</span>
        </a>
    </li>
    <li>
        <a href="{{ route('analytics.dashboard') }}" data-active-paths="{{ route('analytics.dashboard') }}">
            <i class="feather-trending-up"></i>
            <span>Analytics</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/crm-home') }}" data-active-paths="{{ url('/crm-home') }}">
            <i class="feather-users"></i>
            <span>CRM</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/accounts-home') }}" data-active-paths="{{ url('/accounts-home') }}">
            <i class="feather-pie-chart"></i>
            <span>Accounts</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/inventory-home') }}" data-active-paths="{{ url('/inventory-home') }}">
            <i class="feather-package"></i>
            <span>Inventory</span>
        </a>
    </li>

    {{-- PRODUCT MANAGEMENT --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        📦 PRODUCT MANAGEMENT
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-box"></i>
            <span>Products</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('product-management.index') }}" data-active-paths="{{ route('product-management.index') }}">
                    <i class="feather-list"></i> All Products
                </a>
            </li>
            <li>
                <a href="{{ route('product-management.create') }}" data-active-paths="{{ route('product-management.create') }}">
                    <i class="feather-plus-circle"></i> Create New Product
                </a>
            </li>
            <li>
                <a href="{{ url('/package-products') }}"
                    data-active-paths="{{ url('/package-products') }}, {{ url('/package-products/create') }}, {{ url('/package-products/*/edit') }}, {{ url('/package-products/*/manage-items') }}">
                    <i class="feather-package"></i> Package Products 
                    <span class="menu_badge_count">({{ DB::table('products')->where('is_package', true)->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/barcode_gen') }}" data-active-paths="{{ url('/barcode_gen') }}">
                    <i class="feather-maximize"></i> Barcode Generator
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-grid"></i>
            <span>Categories</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/category') }}"
                    data-active-paths="{{ url('/view/all/category') }},{{ url('/add/new/category') }},{{ url('/edit/category/*') }},{{ url('/rearrange/category') }}">
                    <i class="feather-sliders"></i> Categories
                    <span class="menu_badge_count">({{ DB::table('categories')->where('status', 1)->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/subcategory') }}"
                    data-active-paths="{{ url('/view/all/subcategory') }},{{ url('/add/new/subcategory') }},{{ url('/edit/subcategory/*') }},{{ url('/rearrange/subcategory') }}">
                    <i class="feather-command"></i> Subcategories
                    <span class="menu_badge_count">({{ DB::table('subcategories')->where('status', 1)->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/childcategory') }}"
                    data-active-paths="{{ url('/view/all/childcategory') }},{{ url('/add/new/childcategory') }},{{ url('/edit/childcategory/*') }},{{ url('/rearrange/childcategory') }}">
                    <i class="feather-git-pull-request"></i> Child Categories
                    <span class="menu_badge_count">({{ DB::table('child_categories')->where('status', 1)->count() }})</span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-settings"></i>
            <span>Attributes</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/units') }}" data-active-paths="{{ url('/view/all/units') }}">
                    <i class="feather-activity"></i> Measurement Units
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/brands') }}"
                    data-active-paths="{{ url('/view/all/brands') }},{{ url('/add/new/brand') }},{{ url('/rearrange/brands') }},{{ url('edit/brand/*') }}">
                    <i class="feather-award"></i> Brands
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/models') }}"
                    data-active-paths="{{ url('/view/all/models') }}, {{ url('add/new/model') }},{{ url('edit/model/*') }}">
                    <i class="feather-layers"></i> Models
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/flags') }}" data-active-paths="{{ url('/view/all/flags') }}">
                    <i class="feather-flag"></i> Flags
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/warrenties') }}" data-active-paths="{{ url('/view/all/warrenties') }}">
                    <i class="feather-shield"></i> Warranties
                </a>
            </li>
            {{-- Product Variants --}}
            <li>
                <a href="{{ route('variant-management.index') }}" data-active-paths="{{ route('variant-management.index') }}">
                    <i class="feather-layers"></i> Variant Management
                    <span class="menu_badge_count" title="Total Variant Groups">
                        ({{ DB::table('product_stock_variant_groups')->count() }})
                    </span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-message-circle"></i>
            <span>Reviews & Q/A</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/product/reviews') }}" data-active-paths="{{ url('/view/product/reviews') }}">
                    <i class="feather-star"></i> Product Reviews
                    <span style="color:goldenrod">
                        (@php echo DB::table('product_reviews')->where('status', 0)->count(); @endphp)
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/product/question/answer') }}"
                    data-active-paths="{{ url('/view/product/question/answer') }}">
                    <i class="feather-help-circle"></i> Questions/Answers
                    <span style="color:goldenrod">
                        (@php
                            echo DB::table('product_question_answers')
                                ->whereNull('answer')
                                ->orWhere('answer', '=', '')
                                ->count();
                        @endphp)
                    </span>
                </a>
            </li>
        </ul>
    </li>

    {{-- SALES & ORDERS --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        🛒 SALES & ORDERS
    </li>

    <li>
        <a href="{{ url('/view/all/product-order/manage') }}"
            data-active-paths="{{ url('/view/all/product-order/manage') }}, {{ url('/add/new/product-order/manage') }}">
            <i class="feather-shopping-cart"></i>
            <span>All Orders</span>
            <span class="menu_badge_count">(@php echo DB::table('product_orders')->where('order_status', 'pending')->count(); @endphp)</span>
        </a>
    </li>
    
    <li>
        <a href="{{ url('/add/new/product-order/manage') }}"
            data-active-paths="{{ url('/add/new/product-order/manage') }}, {{ url('/add/new/product-order/manage') }}">
            <i class="feather-shopping-cart"></i>
            <span>Create Order</span>
        </a>
    </li>

    <li>
        <a href="{{ url('/pos/desktop') }}"
            data-active-paths="{{ url('/pos/desktop') }}, {{ url('/pos/desktop/create') }}">
            <i class="feather-shopping-cart"></i>
            <span>POS</span>
        </a>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-rotate-ccw"></i>
            <span>Returns</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('ViewAllProductOrderReturns') }}">
                    <i class="feather-corner-up-left"></i> Order Returns
                </a>
            </li>
            <li>
                <a href="{{ route('CreateManualProductReturn') }}">
                    <i class="feather-plus-circle"></i> Create Manual Return
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllManualProductReturns') }}">
                    <i class="feather-list"></i> All Manual Returns
                </a>
            </li>
        </ul>
    </li>
    
    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-dollar-sign"></i>
            <span>Customer Payments</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('CreateCustomerPayment') }}">
                    <i class="feather-plus-circle"></i> Add Payment/Advance
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllCustomerPayments') }}">
                    <i class="feather-list"></i> All Payments
                </a>
            </li>
            <li>
                <a href="{{ route('CreateCustomerPaymentReturn') }}">
                    <i class="feather-corner-up-left"></i> Payment Refund
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="{{ url('/view/all/promo/codes') }}"
            data-active-paths="{{ url('/view/all/promo/codes') }},{{ url('/add/new/code') }},{{ url('/edit/promo/code/*') }}">
            <i class="feather-gift"></i>
            <span>Promo Codes</span>
            <span class="menu_badge_count">({{ DB::table('promo_codes')->where('status', 1)->count() }})</span>
        </a>
    </li>

    <li>
        <a href="{{ url('/view/customers/wishlist') }}" data-active-paths="{{ url('/view/customers/wishlist') }}">
            <i class="feather-heart"></i>
            <span>Customer Wishlist</span>
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
            <i class="feather-map-pin"></i>
            <span>Upazila & Thana</span>
        </a>
    </li>


    {{-- INVENTORY & STOCK --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        📦 INVENTORY & STOCK
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-home"></i>
            <span>Warehouse</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/product-warehouse') }}"
                    data-active-paths="{{ url('/view/all/product-warehouse') }}, {{ url('/add/new/product-warehouse') }}, {{ url('/edit/product-warehouse/*') }}">
                    <i class="feather-archive"></i> Warehouses
                    <span class="menu_badge_count">({{ DB::table('product_warehouses')->where('status', 'active')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/product-warehouse-room') }}"
                    data-active-paths="{{ url('/view/all/product-warehouse-room') }}, {{ url('/add/new/product-warehouse-room') }}, {{ url('/edit/product-warehouse-room/*') }}">
                    <i class="feather-grid"></i> Warehouse Rooms
                    <span class="menu_badge_count">({{ DB::table('product_warehouse_rooms')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/product-warehouse-room-cartoon') }}"
                    data-active-paths="{{ url('/view/all/product-warehouse-room-cartoon') }}, {{ url('/add/new/product-warehouse-room-cartoon') }}, {{ url('/edit/product-warehouse-room-cartoon/*') }}">
                    <i class="feather-box"></i> Room Cartons
                    <span class="menu_badge_count">({{ DB::table('product_warehouse_room_cartoons')->count() }})</span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-trending-up"></i>
            <span>Stock Adjustment</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('stock-adjustment.index') }}" data-active-paths="{{ route('stock-adjustment.index') }}">
                    <i class="feather-list"></i> Adjustment Logs
                </a>
            </li>
            <li>
                <a href="{{ route('stock-adjustment.create') }}" data-active-paths="{{ route('stock-adjustment.create') }}">
                    <i class="feather-plus-circle"></i> New Adjustment
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-users"></i>
            <span>Suppliers</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/supplier-source') }}"
                    data-active-paths="{{ url('/view/all/supplier-source') }}, {{ url('/add/new/supplier-source') }}, {{ url('/edit/supplier-source/*') }}">
                    <i class="feather-tag"></i> Supplier Types
                    <span class="menu_badge_count">({{ DB::table('supplier_source_types')->where('status', 'active')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/product-supplier') }}"
                    data-active-paths="{{ url('/view/all/product-supplier') }}, {{ url('/add/new/product-supplier') }}, {{ url('/edit/product-supplier/*') }}">
                    <i class="feather-users"></i> All Suppliers
                    <span class="menu_badge_count">({{ DB::table('product_suppliers')->where('status', 'active')->count() }})</span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-shopping-bag"></i>
            <span>Purchase</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/purchase-product/charge') }}"
                    data-active-paths="{{ url('/view/all/purchase-product/charge') }}, {{ url('/add/new/purchase-product/charge') }}, {{ url('/edit/purchase-product/charge/*') }}">
                    <i class="feather-tag"></i> Charge Types
                </a>
            </li>
            {{-- <li>
                <a href="{{ url('/view/all/purchase-product/quotation') }}"
                    data-active-paths="{{ url('/view/all/purchase-product/quotation') }}, {{ url('/add/new/purchase-product/quotation') }}, {{ url('/edit/purchase-product/quotation/*') }}, {{ url('edit/purchase-product/sales/quotation/*') }}">
                    <i class="feather-file-text"></i> Quotations
                    <span class="menu_badge_count">({{ DB::table('product_purchase_quotations')->count() }})</span>
                </a>
            </li> --}}
            <li>
                <a href="{{ url('/view/all/purchase-product/order') }}"
                    data-active-paths="{{ url('/view/all/purchase-product/order') }}, {{ url('/add/new/purchase-product/order') }}, {{ url('/edit/purchase-product/order/*') }}, {{ url('edit/purchase-product/sales/order/*') }}">
                    <i class="feather-shopping-cart"></i> Purchase Orders
                    <span class="menu_badge_count">({{ DB::table('product_purchase_orders')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/purchase-return/order') }}"
                    data-active-paths="{{ url('/view/all/purchase-return/order') }}, {{ url('/add/new/purchase-return/order') }}, {{ url('/edit/purchase-return/order/*') }}, {{ url('edit/purchase-return/sales/order/*') }}">
                    <i class="feather-corner-up-left"></i> Purchase Returns
                    <span class="menu_badge_count">({{ DB::table('product_purchase_returns')->count() }})</span>
                </a>
            </li>
        </ul>
    </li>


    {{-- ACCOUNTS & FINANCE --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        💰 ACCOUNTS & FINANCE
    </li>

    <li>
        <a href="{{ url('/view/all/ac-account') }}"
            data-active-paths="{{ url('/view/all/ac-account') }}, {{ url('/add/new/ac-account') }}, {{ url('/edit/ac-account/*') }}">
            <i class="feather-credit-card"></i>
            <span>Accounts</span>
            <span class="menu_badge_count">({{ DB::table('ac_accounts')->where('status', 'active')->count() }})</span>
        </a>
    </li>

    {{-- Investor Management --}}
    <li>
        <a href="{{ route('ViewAllInvestor') }}"
            data-active-paths="{{ route('ViewAllInvestor') }}, {{ route('CreateInvestor') }}">
            <i class="feather-users"></i>
            <span>Investor Management</span>
        </a>
    </li>

    <li>
        <a href="{{ url('/view/all/payment-type') }}"
            data-active-paths="{{ url('/view/all/payment-type') }}, {{ url('/add/new/payment-type') }}, {{ url('/edit/payment-type/*') }}">
            <i class="feather-credit-card"></i>
            <span>Payment Methods</span>
            <span class="menu_badge_count">({{ DB::table('db_paymenttypes')->count() }})</span>
        </a>
    </li>

    {{-- supplier payments --}}
    <li>
        <a href="{{ route('ViewAllSupplierPayments') }}"
            data-active-paths="{{ route('ViewAllSupplierPayments') }}, {{ route('CreateSupplierPaymentDue') }}, {{ route('CreateSupplierPaymentAdvance') }}, {{ route('ViewSupplierPayments') }}">
            <i class="feather-credit-card"></i>
            <span>Supplier Payments</span>
        </a>
    </li>

    {{-- income management --}}

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-trending-up"></i>
            <span>Incomes</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('ViewAllIncomeCategory') }}" data-active-paths="{{ route('ViewAllIncomeCategory') }},{{ route('AddNewIncomeCategory') }},{{ route('EditIncomeCategory', ['slug' => 'dummy']) }}">
                    <i class="fas fa-list"></i> Income Categories
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllIncome') }}" data-active-paths="{{ route('ViewAllIncome') }},{{ route('AddNewIncome') }},{{ route('ViewIncomeDetails', ['id' => 'dummy']) }}">
                    <i class="fas fa-money-bill-wave"></i> All Incomes
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-trending-down"></i>
            <span>Expenses</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/expense-category') }}"
                    data-active-paths="{{ url('/view/all/expense-category') }}, {{ url('/add/new/expense-category') }}, {{ url('/edit/expense-category/*') }}">
                    <i class="feather-tag"></i> Expense Categories
                    <span class="menu_badge_count">({{ DB::table('db_expense_categories')->where('status', 'active')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllExpense') }}"
                    data-active-paths="{{ route('ViewAllExpense') }}, {{ url('/add/new/expense') }}, {{ url('/edit/expense/*') }}">
                    <i class="feather-list"></i> All Expenses
                    <span class="menu_badge_count">({{ DB::table('db_expenses')->where('status', 'active')->count() }})</span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="{{ route('ViewAllDeposit') }}"
            data-active-paths="{{ route('ViewAllDeposit') }}, {{ url('/add/new/deposit') }}, {{ url('/edit/deposit/*') }}">
            <i class="feather-trending-up"></i>
            <span>Deposits</span>
        </a>
    </li>

    <li>
        <a href="{{ route('ViewAllWithdraw') }}"
            data-active-paths="{{ route('ViewAllWithdraw') }}, {{ route('AddNewWithdraw') }}">
            <i class="feather-trending-down"></i>
            <span>Withdraws</span>
        </a>
    </li>

    {{-- Adjustment Balance --}}
    <li>
        <a href="{{ route('ViewAllAdjustment') }}"
            data-active-paths="{{ route('ViewAllAdjustment') }}, {{ route('CreateAdjustment') }}">
            <i class="feather-refresh-cw"></i>
            <span>Account Adjustment</span>
        </a>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-file-text"></i>
            <span>Financial Reports</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('journal.index') }}" data-active-paths="{{ route('journal.index') }}">
                    <i class="feather-book-open"></i> Journal
                </a>
            </li>
            <li>
                <a href="{{ route('ledger.index') }}" data-active-paths="{{ route('ledger.index') }}">
                    <i class="feather-book"></i> Ledger
                </a>
            </li>
            <li>
                <a href="{{ route('ledger.balance_sheet') }}"
                    data-active-paths="{{ route('ledger.balance_sheet') }}">
                    <i class="feather-pie-chart"></i> Balance Sheet
                </a>
            </li>
            <li>
                <a href="{{ route('ledger.income_statement') }}"
                    data-active-paths="{{ route('ledger.income_statement') }}">
                    <i class="feather-bar-chart"></i> Income Statement
                </a>
            </li>
            <li>
                <a href="{{ url('/product/purchase/report') }}"
                    data-active-paths="{{ url('/product/purchase/report') }}">
                    <i class="feather-shopping-bag"></i> Purchase Report
                </a>
            </li>
        </ul>
    </li>

    {{-- CRM & CUSTOMERS --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        👥 CRM & CUSTOMERS
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-users"></i>
            <span>Customers</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/customer') }}"
                    data-active-paths="{{ url('/view/all/customer') }}, {{ url('/add/new/customers') }}, {{ url('/edit/customers/*') }}">
                    <i class="feather-user"></i> All Customers
                    <span class="menu_badge_count">({{ DB::table('customers')->where('status', 'active')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllCustomerEcommerce') }}"
                    data-active-paths="{{ route('ViewAllCustomerEcommerce') }}, {{ url('/add/new/customer-ecommerce') }}, {{ url('/edit/customer-ecommerce/*') }}">
                    <i class="feather-shopping-bag"></i> E-commerce Customers
                    <span class="menu_badge_count">({{ DB::table('users')->where('user_type', 3)->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/customer-category') }}"
                    data-active-paths="{{ url('/view/all/customer-category') }}, {{ url('/add/new/customer-category') }}, {{ url('/edit/customer-category/*') }}">
                    <i class="feather-grid"></i> Customer Categories
                    <span class="menu_badge_count">({{ DB::table('customer_categories')->where('status', 'active')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/customer-source') }}"
                    data-active-paths="{{ url('/view/all/customer-source') }}, {{ url('/add/new/customer-source') }}, {{ url('/edit/customer-source/*') }}">
                    <i class="feather-compass"></i> Customer Source Types
                    <span class="menu_badge_count">({{ DB::table('customer_source_types')->where('status', 'active')->count() }})</span>
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-phone"></i>
            <span>Contact Management</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('ViewAllCustomerContactHistories') }}"
                    data-active-paths="{{ route('ViewAllCustomerContactHistories') }}, {{ url('/add/new/customer-contact-history') }}, {{ url('/edit/customer-contact-history/*') }}">
                    <i class="feather-clock"></i> Contact History
                    <span class="menu_badge_count">({{ DB::table('customer_contact_histories')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/customer-next-contact-date') }}"
                    data-active-paths="{{ url('/view/all/customer-next-contact-date') }}, {{ url('/add/new/customer-next-contact-date') }}, {{ url('/edit/customer-next-contact-date/*') }}">
                    <i class="feather-calendar"></i> Scheduled Contacts
                    <span class="menu_badge_count">({{ DB::table('customer_next_contact_dates')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/contact/requests') }}"
                    data-active-paths="{{ url('/view/all/contact/requests') }}">
                    <i class="feather-phone-incoming"></i> Contact Requests
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="{{ url('/view/all/subscribed/users') }}"
            data-active-paths="{{ url('/view/all/subscribed/users') }}">
            <i class="feather-mail"></i>
            <span>Newsletter Subscribers</span>
        </a>
    </li>

    <li>
        <a href="{{ route('bulk-sms-bd.index') }}"
            data-active-paths="{{ route('bulk-sms-bd.index') }}">
            <i class="feather-message-square"></i>
            <span>SMS</span>
        </a>
    </li>

    {{-- SR MANAGEMENT --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        📊 SR MANAGEMENT
    </li>
    <li>
        <a href="{{ route('sales_targets.index') }}"
            data-active-paths="{{ route('sales_targets.index') }}, {{ route('sales_targets.create') }}, {{ url('/sales-targets') }}/*">
            <i class="feather-target"></i>
            <span>Sales Targets</span>
        </a>
    </li>

    {{-- USER MANAGEMENT --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        🔐 USER MANAGEMENT
    </li>

    <li>
        <a href="{{ url('/view/system/users') }}"
            data-active-paths="{{ url('/view/system/users') }}, {{ url('add/new/system/user') }}, {{ url('edit/system/user/*') }}">
            <i class="feather-user-check"></i>
            <span>System Users</span>
        </a>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-shield"></i>
            <span>Roles & Permissions</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/user/roles') }}"
                    data-active-paths="{{ url('/view/user/roles') }}, {{ url('/new/user/role') }}, {{ url('/edit/user/role/*') }}">
                    <i class="feather-users"></i> User Roles
                </a>
            </li>
            <li>
                <a href="{{ url('/view/user/role/permission') }}"
                    data-active-paths="{{ url('/view/user/role/permission') }}, {{ url('/assign/role/permission/*') }}">
                    <i class="feather-key"></i> Assign Permissions
                </a>
            </li>
            <li>
                <a href="{{ url('/view/permission/routes') }}" data-active-paths="{{ url('/view/permission/routes') }}">
                    <i class="feather-git-merge"></i> Permission Routes
                </a>
            </li>
        </ul>
    </li>

    {{-- SR MANAGEMENT --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        🔐 SR MANAGEMENT
    </li>
    <li>
        <a href="{{ url('/sales-targets') }}"
            data-active-paths="{{ url('/sales-targets') }}, {{ url('sales-targets/*') }}">
            <i class="feather-target"></i>
            <span>Sales Targets</span>
        </a>
    </li>

    {{-- WEBSITE & CONTENT --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        🌐 WEBSITE & CONTENT
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-edit"></i>
            <span>Blog Management</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/add/new/blog') }}" data-active-paths="{{ url('/add/new/blog') }}">
                    <i class="feather-plus-circle"></i> Write New Blog
                </a>
            </li>
            <li>
                <a href="{{ url('/view/all/blogs') }}"
                    data-active-paths="{{ url('/view/all/blogs') }}, {{ url('/edit/blog/*') }}">
                    <i class="feather-list"></i> All Blogs
                </a>
            </li>
            <li>
                <a href="{{ url('/blog/categories') }}"
                    data-active-paths="{{ url('/blog/categories') }}, {{ url('/rearrange/blog/category') }}">
                    <i class="feather-grid"></i> Blog Categories
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-file-text"></i>
            <span>Pages & Policies</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ url('/view/all/pages') }}"
                    data-active-paths="{{ url('/view/all/pages') }}, {{ url('/create/new/page') }}, {{ url('edit/custom/page/*') }}">
                    <i class="feather-file-plus"></i> Custom Pages
                    <span class="menu_badge_count">({{ DB::table('custom_pages')->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ route('AboutUsPage') }}" data-active-paths="{{ route('AboutUsPage') }}">
                    <i class="feather-info"></i> About Us
                </a>
            </li>
            <li>
                <a href="{{ url('/terms/and/condition') }}" data-active-paths="{{ url('/terms/and/condition') }}">
                    <i class="feather-file"></i> Terms & Conditions
                </a>
            </li>
            <li>
                <a href="{{ url('/view/privacy/policy') }}" data-active-paths="{{ url('/view/privacy/policy') }}">
                    <i class="feather-lock"></i> Privacy Policy
                </a>
            </li>
            <li>
                <a href="{{ url('/view/shipping/policy') }}"
                    data-active-paths="{{ url('/view/shipping/policy') }}">
                    <i class="feather-truck"></i> Shipping Policy
                </a>
            </li>
            <li>
                <a href="{{ url('/view/return/policy') }}" data-active-paths="{{ url('/view/return/policy') }}">
                    <i class="feather-rotate-ccw"></i> Return Policy
                </a>
            </li>
        </ul>
    </li>

    <li>
        <a href="javascript: void(0);" class="has-arrow">
            <i class="feather-image"></i>
            <span>Banner Management</span>
        </a>
        <ul class="sub-menu" aria-expanded="false">
            <li>
                <a href="{{ route('ViewAllSliders') }}"
                    data-active-paths="{{ route('ViewAllSliders') }}, {{ route('AddNewSlider') }}, {{ url('/edit/slider/*') }}, {{ route('RearrangeSlider') }}">
                    <i class="feather-sliders"></i> Sliders
                    <span class="menu_badge_count">({{ DB::table('banners')->where('type', 1)->where('status', 1)->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllBanners') }}"
                    data-active-paths="{{ route('ViewAllBanners') }}, {{ route('AddNewBanner') }}, {{ url('/edit/banner/*') }}, {{ route('RearrangeBanners') }}">
                    <i class="feather-image"></i> Banners
                    <span class="menu_badge_count">({{ DB::table('banners')->where('type', 2)->where('status', 1)->count() }})</span>
                </a>
            </li>
            <li>
                <a href="{{ route('ViewAllPromotionalBanners') }}" 
                    data-active-paths="{{ url('/view/all/promotional/banners') }}, {{ url('/add/new/promotional/banner') }}, {{ url('/edit/promotional/banner/*') }}, {{ url('/rearrange/promotional/banners') }}">
                    <i class="feather-award"></i> Promotional Banners
                    <span class="menu_badge_count">({{ DB::table('promotional_banners')->where('status', 1)->count() }})</span>
                </a>
            </li>
            {{-- <li>
                <a href="{{ route('ViewAllSideBanner') }}"
                    data-active-paths="{{ route('ViewAllSideBanner') }}, {{ route('AddNewSideBanner') }}, {{ url('/edit/side-banner/*') }}">
                    <i class="feather-sidebar"></i> Side Banners
                    <span class="menu_badge_count">({{ DB::table('side_banners')->where('status', 1)->count() }})</span>
                </a>
            </li> --}}
        </ul>
    </li>

    {{-- SETTINGS --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">
    <li class="menu-title" style="color: green;">
        ⚙️ SETTINGS
    </li>

    <li>
        <a href="{{ url('/general/info') }}" data-active-paths="{{ url('/general/info') }}">
            <i class="feather-info"></i>
            <span>General Information</span>
        </a>
    </li>

    <li>
        <a href="{{ url('/social/media/page') }}" data-active-paths="{{ url('/social/media/page') }}">
            <i class="feather-share-2"></i>
            <span>Social Media Links</span>
        </a>
    </li>

    <li>
        <a href="{{ url('/seo/homepage') }}" data-active-paths="{{ url('/seo/homepage') }}">
            <i class="feather-search"></i>
            <span>SEO Settings</span>
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
            <i class="feather-message-square"></i>
            <span>Chat & Social Scripts</span>
        </a>
    </li>

    {{-- LOGOUT --}}
    <hr style="border-color: #c8c8c836; margin-top: 12px; margin-bottom: 12px;">

    <li>
        <a href="{{ route('logout') }}"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="feather-log-out"></i>
            <span>Logout</span>
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
