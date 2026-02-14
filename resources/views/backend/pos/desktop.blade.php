@extends('backend.master')

@section('header_css')
    <link rel="stylesheet" href="/assets/plugins/select2/select2.min.css">
    <style>
        .pos-page {
            font-family: Arial, Helvetica, sans-serif;
            padding: 12px;
            box-sizing: border-box;
        }

        .pos-top {
            display: flex;
            gap: 12px;
        }

        .pos-left {
            flex: 1;
            background: #fff;
            padding: 12px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(15, 34, 58, 0.08);
            position: relative;
        }

        .pos-right {
            width: 420px;
            background: #fff;
            padding: 12px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(15, 34, 58, 0.08);
        }

        .pos-search-row {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .pos-search-row input {
            flex: 1;
            padding: 8px 10px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .pos-search-row button {
            padding: 8px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            background: #ff9f43;
            color: #fff;
        }

        .pos-warehouse-row {
            align-items: center;
            gap: 8px;
        }

        .pos-warehouse-row label {
            margin: 0;
            font-weight: 600;
            font-size: 13px;
        }

        .pos-warehouse-row select {
            min-width: 220px;
            padding: 6px 8px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .pos-filters {
            display: flex;
            gap: 8px;
            margin-bottom: 10px;
        }

        .pos-filters select {
            flex: 1;
            padding: 6px 8px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .pos-products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 133px));
            gap: 10px;
        }

        .pos-product-card {
            border-radius: 8px;
            border: 1px solid #f1f3f5;
            background: #fff;
            padding: 8px;
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: box-shadow 0.2s ease, transform 0.1s ease;
        }

        .pos-product-card:hover {
            box-shadow: 0 6px 16px rgba(15, 34, 58, 0.12);
            transform: translateY(-1px);
        }

        .pos-product-thumb {
            width: 100%;
            height: 120px;
            object-fit: cover;
            object-position: top center;
            border-radius: 6px;
            margin-bottom: 6px;
        }

        .pos-product-name {
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 4px;
            font-size: 12px;
        }

        .pos-product-price {
            font-weight: 700;
            color: #28a745;
        }

        .pos-product-add {
            margin-left: auto;
            padding: 4px 8px;
            border-radius: 4px;
            border: none;
            font-size: 11px;
            background: #4a90e2;
            color: #fff;
            cursor: pointer;
        }

        .pos-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }

        .pos-search-dropdown {
            position: absolute;
            top: 70px;
            left: 12px;
            right: 12px;
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            box-shadow: 0 6px 20px rgba(15, 34, 58, 0.18);
            
            z-index: 90;
        }

        .pos-search-dropdown .items{
            max-height: 360px;
            overflow-y: auto;
        }

        .pos-search-dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 10px;
            border-bottom: 1px solid #f1f3f5;
            font-size: 12px;
        }

        .pos-search-dropdown-item:last-child {
            border-bottom: none;
        }

        .pos-dd-thumb {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 4px;
        }

        .pos-dd-title {
            font-weight: 600;
        }

        .pos-dd-sub {
            color: #6c757d;
        }

        .pos-dd-actions button {
            padding: 4px 8px;
            border-radius: 4px;
            border: none;
            font-size: 11px;
            background: #4a90e2;
            color: #fff;
            cursor: pointer;
        }

        .pos-cart-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }

        .pos-cart-title h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 700;
        }

        .pos-cart-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-bottom: 10px;
            table-layout: fixed;
        }

        .pos-cart-table th,
        .pos-cart-table td {
            padding: 6px;
            border-bottom: 1px solid #f1f3f5;
            text-align: left;
        }

        .pos-cart-thumb {
            width: 32px;
            height: 32px;
            object-fit: cover;
            border-radius: 4px;
            margin-right: 4px;
        }

        .pos-cart-item-cell {
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .pos-cart-item-title {
            font-size: 12px;
            font-weight: 600;
        }

        .pos-cart-item-title-ellipsis {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .pos-cart-input {
            width: 60px;
            padding: 4px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
            text-align: right;
        }

        .pos-totals {
            padding-top: 6px;
            border-top: 1px dashed #dee2e6;
            font-size: 12px;
        }

        .pos-totals-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 4px;
        }

        .pos-totals-grand {
            margin-top: 6px;
            padding-top: 6px;
            border-top: 1px solid #ced4da;
            font-weight: 700;
            font-size: 14px;
        }

        .pos-down {
            margin-top: 12px;
            display: flex;
            justify-content: space-between;
            gap: 12px;
        }

        .pos-customer-area {
            flex: 1;
            background: #fff;
            padding: 12px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(15, 34, 58, 0.08);
        }

        .pos-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pos-actions-row button {
            width: 100%;
            padding: 8px 12px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
        }

        .pos-actions-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .pos-save-wrap {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .pos-draft-hint {
            font-size: 11px;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .pos-draft-hint a {
            color: #0d6efd;
            text-decoration: underline;
        }

        .pos-note-input textarea {
            resize: vertical;
        }

        .pos-btn-save {
            background: #17a2b8;
            color: #fff;
        }

        .pos-btn-hold {
            background: #ffc107;
            color: #212529;
        }

        .pos-btn-cancel {
            background: #e0e0e0;
            color: #212529;
        }

        .pos-btn-create {
            background: #28a745;
            color: #fff;
        }

        .pos-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 999;
        }

        .pos-modal {
            width: 760px;
            max-width: 100%;
            background: #fff;
            border-radius: 8px;
            padding: 16px;
            box-shadow: 0 8px 30px rgba(15, 34, 58, 0.35);
        }

        .pos-modal-header h3 {
            margin: 0;
            font-size: 18px;
        }

        .pos-modal-body {
            margin-top: 10px;
            font-size: 13px;
        }

        .pos-payment-methods {
            display: grid;
            /* grid-template-columns: repeat(2, minmax(0, 1fr)); */
            gap: 15px;
        }

        .pos-payment-row {
            display: flex;
            align-items: center;
            gap: 6px;

            label{
                margin: 0;
                width: 100px;
            }
        }

        .pos-payment-row input[type="number"] {
            flex: 1;
            padding: 4px 6px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .pos-modal-footer {
            margin-top: 12px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pos-modal-footer-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 8px;
        }

        .pos-modal-footer-row button {
            padding: 6px 10px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-size: 12px;
            font-weight: 600;
        }

        .pos-modal-footer-row .btn-secondary {
            background: #6c757d;
            color: #fff;
        }

        .pos-modal-footer-row .btn-primary {
            background: #4a90e2;
            color: #fff;
        }

        .pos-modal-footer-row .btn-success {
            background: #28a745;
            color: #fff;
        }

        .product_list_wrapper {
            height: calc(100% - 90px);
            overflow-y: auto;
        }

        .cart_list_wrapper {
            max-height: 400px;
            overflow-y: auto;
        }

        /* Loading Overlay Styles */
        .pos-loading-overlay {
            position: absolute;
            inset: 0;
            background: rgba(255, 255, 255, 0.85);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 100;
            border-radius: 6px;
        }

        .pos-loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f3f3;
            border-top: 4px solid #4a90e2;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .pos-loading-text {
            margin-top: 10px;
            font-size: 13px;
            color: #495057;
            text-align: center;
        }

        .pos-loading-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Button loading state */
        .pos-btn-loading {
            opacity: 0.6;
            cursor: not-allowed;
            position: relative;
        }

        .pos-btn-loading::after {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            top: 50%;
            left: 50%;
            margin-left: -8px;
            margin-top: -8px;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* Search loading */
        .pos-search-loading {
            position: relative;
        }

        .pos-search-loading::after {
            content: '';
            position: absolute;
            right: 50px;
            top: 50%;
            transform: translateY(-50%);
            width: 16px;
            height: 16px;
            border: 2px solid #6c757d;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }

        /* Product grid loading skeleton */
        .pos-product-skeleton {
            border-radius: 8px;
            border: 1px solid #f1f3f5;
            background: #fff;
            padding: 8px;
            height: 180px;
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
        }

        .pos-cart-item-variant {
            font-size: 12px;
            color: #6c757d;
            margin-top: 4px;
            font-weight: 400;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Inline loading indicator */
        .pos-inline-loading {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 2px solid #6c757d;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
            margin-left: 6px;
            vertical-align: middle;
        }

        .pos_header{
            background: #fff;
            padding: 5px;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(15, 34, 58, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 10px;
        }
        div:where(.swal2-container){
            z-index: 9999 !important;
        }
        #page-topbar{
            display: none;
        }
        #page-topbar.pos_header{
            display: block;
            .navbar-header{
                border: none;
            }
        }
    </style>
@endsection

@section('header_js')
    <script src="{{ versioned_asset('assets/js/vue.min.js') }}"></script>
    <script>
        window.POS_DESKTOP_CONFIG = {
            routes: {
                search: "{{ route('pos.desktop.search') }}",
                products: "{{ route('pos.desktop.products') }}",
                categories: "{{ route('pos.desktop.categories') }}",
                nestedCategories: "{{ route('pos.categories') }}",
                barcode: "{{ route('pos.desktop.barcode') }}",
                addToCart: "{{ route('pos.desktop.add-to-cart') }}",
                hold: "{{ route('pos.desktop.hold') }}",
                getHold: "{{ route('pos.desktop.get-hold', ['id' => '__ID__']) }}",
                customerSearch: "{{ route('pos.desktop.customer.search') }}",
                customerCreate: "{{ route('pos.desktop.customer.create') }}",
                applyCoupon: "{{ route('pos.desktop.apply-coupon') }}",
                calculateTotals: "{{ route('pos.desktop.calculate-totals') }}",
                createOrder: "{{ route('pos.desktop.create-order') }}",
                preview: "{{ route('pos.desktop.preview') }}",
                print: "{{ route('pos.desktop.print', ['slug' => '__SLUG__']) }}",
                holds: "{{ route('pos.desktop.holds') }}",
                paymentMethods: "{{ route('pos.get-payment-methods') }}",
                invoiceUrlBase: "{{ route('order.invoice', ['slug' => '__SLUG__']) }}",
                targetStats: "{{ route('pos.desktop.target-stats') }}",
                productsByBarcode: "{{ route('pos.desktop.products-by-barcode') }}",
                customerSource: "{{ route('pos.desktop.customer-source') }}",
                deliveryMethods: "{{ route('pos.desktop.delivery-methods') }}",
                outlets: "{{ route('pos.desktop.outlets') }}",
                courierMethods: "{{ route('pos.desktop.courier-methods') }}",
            },
            warehouses: @json($warehouses ?? []),
            image_url: "{{env('IMAGE_URL')}}",
        };
        document.addEventListener('DOMContentLoaded', function() {
            document.body.classList.add('lg_hide_menu');
        });
    </script>
    <script src="/assets/plugins/select2/select2.min.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ versioned_asset('assets/js/pos_order_vue.js') }}?v={{ time().'_'. env('APP_VERSION', time()) }}" defer></script>
    <script src="{{ versioned_asset('assets/js/pos/pos_left_categories.js') }}?v={{time().'_'. env('APP_VERSION', time()) }}" defer></script>
    <script src="{{ versioned_asset('assets/js/pos/pos_product_item.js') }}?v={{time().'_'. env('APP_VERSION', time()) }}" defer></script>
    <script src="{{ versioned_asset('assets/js/pos/pos_customer_manage.js') }}?v={{time().'_'. env('APP_VERSION', time()) }}" defer></script>
    <script src="{{ versioned_asset('assets/js/pos/pos_clock.js') }}?v={{time().'_'. env('APP_VERSION', time()) }}" defer></script>
@endsection

@section('page_title')
    POS Order
@endsection

@section('page_heading')
    POS Order
@endsection

@section('content')
    <div id="pos-desktop-app" class="pos_page">
        <header id="page-topbar" class="pos_header">
            <div class="navbar-header">
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-sm mr-2 d-lg-none header-item" id="vertical-menu-btn">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>

                    <button type="button" class="btn btn-sm mr-2 d-none d-lg-block header-item" onclick="$('body').toggleClass('lg_hide_menu')" id="lg_menu_toggler">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                    <div class="pos-target-stats d-flex flex-wrap align-items-center ml-2" style="gap: 8px;">
                        <template v-if="targetStats">
                            <div class="pos-target-stat-card border rounded px-2 py-1" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; min-width: 90px;">
                                <small class="d-block" style="opacity: .9;">Target</small>
                                <strong>@{{ formatMoney(targetStats.total_targets) }}</strong>
                            </div>
                            <div class="pos-target-stat-card border rounded px-2 py-1" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); color: #fff; min-width: 90px;">
                                <small class="d-block" style="opacity: .9;">Sales</small>
                                <strong>@{{ formatMoney(targetStats.sales) }}</strong>
                            </div>
                            <div class="pos-target-stat-card border rounded px-2 py-1" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: #fff; min-width: 90px;">
                                <small class="d-block" style="opacity: .9;">Remains</small>
                                <strong>@{{ formatMoney(targetStats.remains) }}</strong>
                            </div>
                            <div class="pos-target-stat-card border rounded px-2 py-1" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); color: #1a1a1a; min-width: 90px;">
                                <small class="d-block" style="opacity: .85;">Achieve %</small>
                                <strong>@{{ targetStats.achieve_percent }}%</strong>
                            </div>
                        </template>
                    </div>
                    <div class="pl-2">
                        <pos-clock></pos-clock>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    

                </div>
            </div>
        </header>
        <div class="pos-page" v-cloak>
            <!-- Warehouse selection -->
            <div class="pos_header">
                <div class="pos-warehouse-row">
                    <div>
                        <label for="pos-warehouse">Warehouse</label>
                    </div>
                    <div>
                        <select id="pos-warehouse" v-model="selectedWarehouseId" @change="onWarehouseChange">
                            <option :value="null">All Warehouses</option>
                            <option v-for="w in warehouses" :key="w.id" :value="w.id">
                                @{{ w.title }}
                            </option>
                        </select>
                    </div>
                </div>
                
            </div>

            <div class="pos-top">
                <pos-left-categories 
                    :selected_category_type="selected_category_type" 
                    :selected_category_id="selected_category_id" 
                    :setSelectedCategory="setSelectedCategory"
                >
                </pos-left-categories>

                <!-- LEFT: products & search -->
                <div class="pos-left">
                    <div class="pos-search-row">
                        <div style="flex: 1;">
                            <input type="text" v-model="searchQuery" @input="onSearchInput"
                                :class="{ 'pos-search-loading': loading.search }"
                                class="w-100"
                                placeholder="Search product by name" ref="searchInput">
                        </div>
                        <div style="width: 200px;">
                            <input type="text" class="w-100" placeholder="Search product by barcode" @focus="clear_value_for_barcode($event)" v-model="barcodeQuery" @input="onBarcodeInput">
                        </div>
                    </div>

                    <div class="product_list_wrapper" style="position: relative;">
                        <div class="pos-loading-overlay" v-if="loading.products">
                            <div class="pos-loading-container">
                                <div class="pos-loading-spinner"></div>
                                <div class="pos-loading-text">Loading products...</div>
                            </div>
                        </div>
                        <div class="pos-products-grid">
                            
                            <pos-product-item 
                                v-for="p in products" :key="p.id" :p="p" 
                                :formatMoney="formatMoney" 
                                :product-item-select="selectProduct"
                            ></pos-product-item>
                            
                            <template v-if="loading.products && products.length === 0">
                                <div class="pos-product-skeleton" v-for="n in 12" :key="'skeleton-' + n"></div>
                            </template>
                        </div>
                    </div>

                    <div class="pos-pagination">
                        <button type="button" class="btn btn-sm btn-light" @click="prevPage" :disabled="page === 1">
                            Prev
                        </button>
                        <span>Page @{{ page }}</span>
                        <button type="button" class="btn btn-sm btn-light" @click="nextPage" :disabled="!hasMore">
                            Next
                        </button>
                    </div>

                    <!-- Search dropdown -->
                    <div class="pos-search-dropdown" v-if="showSearchDropdown">
                        <div class="items">
                            <div v-if="loading.search" class="p-3 text-center">
                                <div class="pos-loading-spinner" style="margin: 0 auto;"></div>
                                <div class="pos-loading-text">Searching...</div>
                            </div>
                            <div v-else class="pos-search-dropdown-item" v-for="r in searchResults.items" :key="r.id">
                                <img :src="r.image_url" class="pos-dd-thumb" alt="">
                                <div>
                                    <div class="pos-dd-title">@{{ r.title || r.name }}</div>
                                    <div class="pos-dd-sub">
                                        @{{ r.barcode ? 'Barcode: ' + r.barcode : '' }}
                                        <span v-if="r.stock !== undefined"> · Stock: @{{ r.stock }}</span>
                                    </div>
                                </div>
                                <div class="pos-dd-actions ml-auto">
                                    <button type="button" @click="onAddFromSearch(r)">Add</button>
                                </div>
                            </div>
                            <div v-if="!loading.search && searchResults.length === 0" class="p-3 text-center text-muted">
                                No results found
                            </div>
                        </div>
                        <div class="p-2 text-right">
                            <button type="button" class="btn btn-sm btn-light" @click="closeSearch">Close</button>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: cart -->
                <div class="pos-right" style="display: flex; flex-direction: column; gap: 10px; justify-content: space-between;">
                    <div>
                        <div>
                            <pos-customer-manage
                                :set-selected-customer="setSelectedCustomer"
                                :customer_sources="customerSources"
                            ></pos-customer-manage>
                        </div>
                        
                        <div class="pos-cart-title">
                            <h4>Order List</h4>
                        </div>
    
                        <div class="cart_list_wrapper">
                            <table class="pos-cart-table">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th>Qty</th>
                                        <th>Unit</th>
                                        <th>Disc (%)</th>
                                        <th>Disc (Tk)</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template v-for="(it, index) in cart" :key="it.temp_id">
                                        <!-- First row: image + name -->
                                        <tr>
                                            <td colspan="6">
                                                <div class="pos-cart-item-cell">
                                                    <img :src="it.image_url" class="pos-cart-thumb" alt="">
                                                    <div class="pos-cart-item-title pos-cart-item-title-ellipsis" style="flex: 1;"
                                                        :title="it.title">
                                                        <div style="display: flex; justify-content: space-between;">
                                                            <div>
                                                                @{{ it.title }}
                                                            </div>
                                                            <div>
                                                                <i class="feather-trash-2" style="cursor: pointer; color: red; font-size: 12px;" @click="removeItem(it, index)"></i>
                                                            </div>
                                                        </div>
                                                        <div class="pos-cart-item-variant" v-if="it.variant_combination_key">
                                                            <span>
                                                                @{{ it.variant_combination_key }} 
                                                            </span>
                                                            <span>
                                                                (avl: @{{ it.max_qty }})
                                                            </span>
                                                            <span v-if="it.warehouse_name" style="background: #f0f0f0; padding: 2px 4px; border-radius: 4px; white-space: normal;line-height: 25px;">
                                                                <span>
                                                                    @{{ it.warehouse_name }}
                                                                </span>
                                                                <span v-if="it.room_name">, </span>
                                                                <span>
                                                                    @{{ it.room_name }}
                                                                </span>
                                                                <span v-if="it.cartoon_name">, </span>
                                                                <span>
                                                                    @{{ it.cartoon_name }}
                                                                </span>
                                                            </span> &nbsp;
                                                            <span v-if="it.unit_code" :style="'border: 1px solid #' + randomHex(it.unit_code || it.temp_id) + '; color: black; padding: 2px 4px; border-radius: 4px;'">
                                                                @{{ it.unit_code }}
                                                            </span>
                                                        </div>
                                                        <div class="pos-cart-item-variant" v-if="!it.variant_combination_key">
                                                            <span>
                                                                Avl: @{{ it.max_qty }}
                                                            </span> &nbsp;
                                                            <span v-if="it.warehouse_name" style="background: #f0f0f0; padding: 2px 4px; border-radius: 4px;white-space: normal;line-height: 25px;">
                                                                <span>
                                                                    @{{ it.warehouse_name }}
                                                                </span>
                                                                <span v-if="it.room_name">, </span>
                                                                <span>
                                                                    @{{ it.room_name }}
                                                                </span>
                                                                <span v-if="it.cartoon_name">, </span>
                                                                <span>
                                                                    @{{ it.cartoon_name }}
                                                                </span>
                                                            </span> &nbsp;
                                                            <span v-if="it.unit_code" :style="'border: 1px solid #' + randomHex(it.unit_code || it.temp_id) + '; color: black; padding: 2px 4px; border-radius: 4px;'">
                                                                @{{ it.unit_code }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <!-- Second row: quantity, prices, discounts, total -->
                                        <tr>
                                            <td></td>
                                            <td>
                                                <input 
                                                    :disabled="it.unit_code" 
                                                    :readonly="it.unit_code"
                                                    :title="it.unit_code ? 'can not change quantity for barcode product' : ''"
                                                    type="text" class="pos-cart-input" :value="it.qty"
                                                    @focus="$event.target.select()"
                                                    @keyup.up.prevent="incrementValue($event, 'qty', it)"
                                                    @keyup.down.prevent="decrementValue($event, 'qty', it)"
                                                    @input="updateCartValue($event, 'qty', it)"
                                                    @blur="recalcItem(it)">
                                            </td>
                                            <td>
                                                <input type="text" class="pos-cart-input"
                                                    :value="it.unit_price"
                                                    @focus="$event.target.select()"
                                                    @keyup.up.prevent="incrementValue($event, 'unit_price', it)"
                                                    @keyup.down.prevent="decrementValue($event, 'unit_price', it)"
                                                    @input="updateCartValue($event, 'unit_price', it)"
                                                    @blur="recalcItem(it)">
                                            </td>
                                            <td>
                                                <input type="text" class="pos-cart-input"
                                                    :value="it.discount.percent"
                                                    @focus="$event.target.select()"
                                                    @keyup.up.prevent="incrementValue($event, 'discount.percent', it)"
                                                    @keyup.down.prevent="decrementValue($event, 'discount.percent', it)"
                                                    @input="updateDiscountValue($event, 'percent', it)"
                                                    @blur="onItemDiscountChange(it, 'percent')">
                                            </td>
                                            <td>
                                                <input type="text" class="pos-cart-input"
                                                    :value="it.discount.fixed"
                                                    @focus="$event.target.select()"
                                                    @keyup.up.prevent="incrementValue($event, 'discount.fixed', it)"
                                                    @keyup.down.prevent="decrementValue($event, 'discount.fixed', it)"
                                                    @input="updateDiscountValue($event, 'fixed', it)"
                                                    @blur="onItemDiscountChange(it, 'fixed')">
                                            </td>
                                            <td>@{{ formatMoney(it.final_price) }}</td>
                                        </tr>
                                    </template>
                                    <tr v-if="cart.length === 0">
                                        <td colspan="6" class="text-center text-muted">Cart is empty</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
    
                        <div class="pos-totals">
                            <div class="pos-totals-row">
                                <span>Subtotal</span>
                                <span>@{{ formatMoney(totals.subtotal) }}</span>
                            </div>
                            <div class="pos-totals-row">
                                <div>
                                    <span>Discount</span>
                                    <label class="mr-2" for="discount_type_fixed">
                                        <input type="radio" name="discount_type" value="fixed" id="discount_type_fixed" v-model="totals.discount.type" @change="recalcTotals">
                                        <span>Fixed</span>
                                    </label>
                                    <label for="discount_type_percent" class="mr-2">
                                        <input type="radio" name="discount_type" value="percent" id="discount_type_percent" v-model="totals.discount.type" @change="recalcTotals">
                                        <span>Percent</span>
                                    </label>
                                </div>
                                <span>
                                    <input type="text" class="pos-cart-input"
                                        :value="totals.discount.value"
                                        :placeholder="totals.discount.type === 'percent' ? '%' : '0'"
                                        @focus="$event.target.select()"
                                        @keyup.up.prevent="incrementValue($event, 'totals.discount.value', null)"
                                        @keyup.down.prevent="decrementValue($event, 'totals.discount.value', null)"
                                        @input="updateValue($event, 'totals.discount.value')">
                                    <span class="text-muted ml-1">= @{{ formatMoney(totals.discount.amount || 0) }}</span>
                                </span>
                            </div>
                            <div class="pos-totals-row">
                                <span>
                                    Coupon
                                    <span v-if="totals.coupon.amount > 0 && totals.coupon.type">(@{{ totals.coupon.type === 'percent' ? 'per: ' + totals.coupon.percent + '%' : 'fix: ' + formatMoney(totals.coupon.value) }})</span>
                                    <input type="text" v-model="coupon.code" style="width: 90px; margin-left: 4px;"
                                        placeholder="Code" :disabled="loading.coupon">
                                    <button type="button" class="btn btn-sm btn-light" @click="applyCoupon" 
                                        :disabled="loading.coupon" :class="{ 'pos-btn-loading': loading.coupon }">
                                        <span v-if="!loading.coupon">Apply</span>
                                        <span v-else>Applying...</span>
                                    </button>
                                </span>
                                <span>@{{ formatMoney(totals.coupon.amount || 0) }}</span>
                            </div>
                            <div class="pos-totals-row">
                                <span>Extra Charge</span>
                                <span>
                                    <input type="text" class="pos-cart-input"
                                        :value="extra_charge"
                                        @focus="$event.target.select()"
                                        @keyup.up.prevent="incrementValue($event, 'extra_charge', null)"
                                        @keyup.down.prevent="decrementValue($event, 'extra_charge', null)"
                                        @input="updateValue($event, 'extra_charge')"
                                        @blur="recalcTotals">
                                </span>
                            </div>
                            <div class="pos-totals-row">
                                <span>
                                    Delivery Charge
                                    <label for="delivery_charge_type_inside_city">
                                        <input type="radio" name="delivery_charge_type" @change="setDeliveryChargeByType()" value="inside_city" id="delivery_charge_type_inside_city" v-model="delivery_info.delivery_charge_type">
                                        <span>Inside</span>
                                    </label>
                                    <label for="delivery_charge_type_outside_city">
                                        <input type="radio" name="delivery_charge_type" @change="setDeliveryChargeByType()" value="outside_city" id="delivery_charge_type_outside_city" v-model="delivery_info.delivery_charge_type">
                                        <span>Outside</span>
                                    </label>
                                </span>
                                <span>
                                    <input type="text" class="pos-cart-input"
                                        :value="delivery_charge"
                                        @focus="$event.target.select();"
                                        @keyup.up.prevent="incrementValue($event, 'delivery_charge', null)"
                                        @keyup.down.prevent="decrementValue($event, 'delivery_charge', null)"
                                        @input="updateValue($event, 'delivery_charge')"
                                        @blur="recalcTotals">
                                </span>
                            </div>
                            <div class="pos-totals-row">
                                <span>Round Off</span>
                                <span>
                                    <input type="text" class="pos-cart-input"
                                        :value="round_off"
                                        @focus="$event.target.select()"
                                        @keyup.up.prevent="incrementValue($event, 'round_off', null)"
                                        @keyup.down.prevent="decrementValue($event, 'round_off', null)"
                                        @input="updateValue($event, 'round_off')"
                                        @blur="recalcTotals">
                                </span>
                            </div>
                            <div class="pos-totals-row pos-totals-grand">
                                <span>Grand Total</span>
                                <span>@{{ formatMoney(totals.grand_total) }}</span>
                            </div>
                            
                            <hr/>
                            <div v-if="selectedCustomer && selectedCustomer.id != 1">
                                <div class="pos-totals-row">
                                    <span>
                                        <label style="margin: 0; cursor: pointer; display: flex; align-items: center; gap: 6px;">
                                            <input type="checkbox" v-model="useAdvance" @change="onAdvanceCheckboxChange">
                                            <span>Advance</span>
                                            <span v-if="selectedCustomer.advance" style="color: #6c757d; font-size: 11px;">
                                                (Available: @{{ formatMoney(selectedCustomer.advance) }})
                                            </span>
                                        </label>
                                    </span>
                                    <span v-if="useAdvance">
                                        <input type="text" class="pos-cart-input"
                                            :value="advanceAmount"
                                            @focus="onAdvanceFocus($event)"
                                            @keyup.up.prevent="advanceAmount = Math.min((parseFloat(advanceAmount) || 0) + 1, selectedCustomer.advance || 0); updateAdvanceAmount({target: {value: advanceAmount}}); $event.target.value = advanceAmount"
                                            @keyup.down.prevent="advanceAmount = Math.max(0, (parseFloat(advanceAmount) || 0) - 1); updateAdvanceAmount({target: {value: advanceAmount}}); $event.target.value = advanceAmount"
                                            @input="updateAdvanceAmount($event)">
                                    </span>
                                </div>
                            </div>
                            <div v-for="payment_mode in paymentMethods" :key="payment_mode.id" class="pos-totals-row">
                                <span>
                                    <b>@{{ payment_mode.title }}</b>
                                </span>
                                <span>
                                    <input class="pos-cart-input"
                                        type="text"
                                        :value="payment_mode.amount"
                                        :max="getPaymentMaxAmount(payment_mode)"
                                        @focus="onPaymentFocus($event, payment_mode)"
                                        @keyup.up.prevent="incrementPaymentValue($event, payment_mode)"
                                        @keyup.down.prevent="decrementPaymentValue($event, payment_mode)"
                                        @input="updatePaymentValue($event, payment_mode)">
                                </span>
                            </div>
                            <div class="pos-totals-row pos-totals-grand">
                                <span><b>Paid Amount</b></span>
                                <span>@{{ formatMoney(paymentTotal) }}</span>
                            </div>
                            <div class="pos-totals-row pos-totals-grand">
                                <span><b>Due Amount</b></span>
                                <span>@{{ formatMoney(totals.grand_total - paymentTotal) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="pos-actions">
                        {{-- <div class="pos-actions-row">
                            <div class="pos-save-wrap">
                                <button type="button" class="pos-btn-save" @click="saveOrder">Save</button>
                                <div class="pos-draft-hint" v-if="hasSavedDraft">
                                    <small>Saved draft available.</small>
                                    <a href="#" @click.prevent="restoreSavedOrder">Restore</a>
                                </div>
                            </div>
                            <button type="button" class="pos-btn-hold" @click="holdOrder" 
                                :disabled="loading.hold" :class="{ 'pos-btn-loading': loading.hold }">
                                <span v-if="!loading.hold">Hold</span>
                                <span v-else>Holding...</span>
                            </button>
                        </div>
                        <div class="pos-actions-row">
                            <button type="button" class="pos-btn-cancel" @click="cancelOrder">Cancel</button>
                            <button type="button" class="pos-btn-create" @click="openHoldList" 
                                :disabled="loading.holdList">Hold List</button>
                        </div> --}}
                        <div class="delivery_info_container">
                            <div class="d-flex align-items-center" style="gap: 5px;">
                                <label class="font-weight-bold pr-2">Order Status:</label>
                                <label for="pending" class="d-flex align-items-center" style="gap: 5px;"><input type="radio" id="pending" name="order_status" value="pending" v-model="order_status"> Quotation</label>
                                <label for="invoiced" class="d-flex align-items-center" style="gap: 5px;"><input type="radio" id="invoiced" name="order_status" value="invoiced" v-model="order_status"> Invoiced</label>
                                <label for="delivered" class="d-flex align-items-center" style="gap: 5px;"><input type="radio" id="delivered" name="order_status" value="delivered" v-model="order_status"> Delivered</label>
                            </div>
                            
                            <div>
                                <label for="delivery_info_checkbox">
                                    <input type="checkbox" id="delivery_info_checkbox" >
                                    Delivery Info
                                </label>
                            </div>
                            <div class="delivery_info_wrapper">
                                
                                <div class="mb-2">
                                    <label for="delivery_method">Delivery Method</label>
                                    <select id="delivery_method" v-model="delivery_info.delivery_method" class="form-control">
                                        <option value="">Select Delivery Method</option>
                                        <option v-for="method in deliveryMethods" :key="method.id" :value="method.title">@{{ method.title }}</option>
                                    </select>
                                </div>
                                <div class="mb-2" v-if="['store_pickup', 'store pickup'].includes((delivery_info.delivery_method || '').toString().toLowerCase())">
                                    <label for="outlet_id">Outlet</label>
                                    <select id="outlet_id" v-model="delivery_info.outlet_id" class="form-control">
                                        <option value="">Select Outlet</option>
                                        <option v-for="outlet in outlets" :key="outlet.id" :value="outlet.id">@{{ outlet.title }}</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label for="expected_delivery_date">Expected Delivery Date</label>
                                    <input type="date" id="expected_delivery_date" class="form-control" v-model="delivery_info.expected_delivery_date">
                                </div>
                                <div class="mb-2">
                                    <label for="order_source">Order Source</label>
                                    <select id="order_source" v-model="delivery_info.order_source" class="form-control">
                                        <option value="">Select Order Source</option>
                                        <option v-for="source in customerSources" :key="source.id" :value="source.id">@{{ source.title }}</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label for="order_note">Note/Instruction</label>
                                    <textarea id="order_note" class="form-control" rows="2" v-model="delivery_info.order_note"></textarea>
                                </div>
                                <div class="d-flex align-items-center mb-2" style="gap: 5px;">
                                    <label class="font-weight-bold pr-2">Courier Method:</label>
                                    <label for="courier_method_none" class="d-flex align-items-center" style="gap: 5px;">
                                        <input type="radio" id="courier_method_none" name="courier_method" value=""> None
                                    </label>
                                    <label :for="'courier_method_' + courier_method.id" v-for="courier_method in courierMethods" :key="courier_method.id" class="d-flex align-items-center" style="gap: 5px;">
                                        <input type="radio" :id="'courier_method_' + courier_method.id" 
                                            :name="'courier_method'" 
                                            :value="courier_method.id" 
                                            v-model="delivery_info.courier_method"
                                            @change="setCourierMethod(courier_method)"> 
                                        @{{ courier_method.title }}
                                    </label>
                                </div>
                                <div class="mb-2" v-if="delivery_info.courier_method_title && delivery_info.courier_method_title.toLowerCase().includes('steadfast')">
                                    <label for="courier_info_address">Courier Address</label>
                                    <textarea id="courier_info_address" v-model="selectedCustomer.address" class="form-control" rows="2" ></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn btn-success w-100" @click="submitOrder"
                            :disabled="loading.order" :class="{ 'pos-btn-loading': loading.order }">
                            <span v-if="!loading.order">Submit Order</span>
                            <span v-else>Processing...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.innerWidth < 992) {
                window.location.href = "{{ route('pos.desktop.mobile') }}";
            }
        });
    </script>
@endpush
