@extends('backend.master')

@section('header_css')
    <style>
        [v-cloak] {
            display: none !important;
        }

        .pos-mobile-shell {
            position: relative;
        }

        .pos-mobile-page {
            padding: 12px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            min-height: calc(100vh - 160px);
            background: #f4f6f8;
            padding-bottom: 160px;
        }

        .pos-mobile-card {
            background: #fff;
            border-radius: 12px;
            padding: 14px;
            box-shadow: 0 8px 24px rgba(15, 34, 58, 0.08);
        }

        .pos-mobile-top {
            position: sticky;
            top: 0;
            z-index: 30;
            background: #f4f6f8;
            padding-bottom: 10px;
        }

        .pos-mobile-warehouse {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .pos-mobile-warehouse select {
            border-radius: 10px;
            border: 1px solid #e4e7ec;
            padding: 10px;
            font-size: 14px;
            background: #fff;
        }

        .pos-mobile-tabs {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 8px;
            position: sticky;
            top: 64px;
            z-index: 25;
            background: #f4f6f8;
            padding-top: 6px;
            padding-bottom: 6px;
        }

        .pos-mobile-tab {
            border: none;
            border-radius: 40px;
            padding: 10px 12px;
            font-weight: 600;
            font-size: 13px;
            background: #fff;
            color: #5c6c7f;
            box-shadow: 0 2px 6px rgba(15, 34, 58, 0.08);
        }

        .pos-mobile-tab.active {
            background: linear-gradient(135deg, #4a90e2, #4776e6);
            color: #fff;
            box-shadow: 0 6px 16px rgba(71, 118, 230, 0.4);
        }

        .pos-mobile-panel {
            display: none;
        }

        .pos-mobile-panel.active {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .pos-mobile-actions button {
            width: 100%;
            border: none;
            border-radius: 10px;
            padding: 12px;
            background: #f1f5f9;
            font-weight: 600;
            color: #334155;
        }

        .pos-mobile-search-row {
            display: flex;
            gap: 8px;
        }

        .pos-mobile-search-row input {
            flex: 1;
            border-radius: 10px;
            border: 1px solid #e4e7ec;
            padding: 10px 12px;
        }

        .pos-mobile-search-row button {
            width: 48px;
            border-radius: 10px;
            border: none;
            background: #4a90e2;
            color: #fff;
        }

        .pos-mobile-filters {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .pos-mobile-filters select {
            flex: 1 1 45%;
            border-radius: 10px;
            border: 1px solid #e4e7ec;
            padding: 8px;
            font-size: 13px;
        }

        .pos-mobile-products {
            margin-top: 12px;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }

        .pos-product-card {
            background: #fdfdfd;
            border-radius: 12px;
            padding: 10px;
            border: 1px solid #edf1f5;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .pos-product-name {
            font-size: 13px;
            font-weight: 600;
            color: #0f172a;
        }

        .pos-product-thumb {
            width: 100%;
            height: 110px;
            object-fit: cover;
            border-radius: 10px;
        }

        .pos-product-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
        }

        .pos-product-add {
            border: none;
            background: #4a90e2;
            color: #fff;
            border-radius: 20px;
            padding: 4px 10px;
            font-size: 12px;
        }

        .pos-mobile-cart-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .pos-mobile-cart-item {
            background: #fff;
            border-radius: 12px;
            padding: 12px;
            box-shadow: 0 6px 18px rgba(15, 34, 58, 0.08);
        }

        .pos-mobile-cart-head {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .pos-cart-thumb {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            object-fit: cover;
        }

        .pos-mobile-cart-title {
            font-weight: 600;
            font-size: 14px;
        }

        .pos-mobile-cart-inputs {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
            margin-top: 12px;
        }

        .pos-mobile-cart-inputs label {
            font-size: 11px;
            color: #64748b;
            margin-bottom: 4px;
            display: block;
        }

        .pos-cart-input {
            width: 100%;
            border-radius: 10px;
            border: 1px solid #e4e7ec;
            padding: 8px;
            font-size: 13px;
        }

        .pos-mobile-cart-total {
            margin-top: 10px;
            font-weight: 600;
            font-size: 14px;
            text-align: right;
        }

        .pos-mobile-cart-actions {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 8px;
        }

        .pos-mobile-cart-actions button {
            padding: 10px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
        }

        .pos-btn-save {
            background: #f1f5f9;
            color: #0f172a;
        }

        .pos-btn-hold {
            background: #ffe9c7;
            color: #ad6700;
        }

        .pos-btn-hold-list {
            background: #e0f2fe;
            color: #0369a1;
        }

        .pos-btn-cancel {
            background: #fee2e2;
            color: #b91c1c;
        }

        .pos-payment-field {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
        }

        .pos-payment-field input {
            width: 140px;
            border-radius: 10px;
            border: 1px solid #e4e7ec;
            padding: 8px;
            text-align: right;
        }

        .pos-mobile-bottom {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
            padding: 14px 16px;
            background: #ffffff;
            border-radius: 16px 16px 0 0;
            box-shadow: 0 -10px 25px rgba(15, 34, 58, 0.12);
            z-index: 40;
        }
        .pos-mobile-search-wrapper {
            position: sticky;
            top: 118px;
            z-index: 20;
            background: #fff;
            padding-bottom: 6px;
            border-radius: 12px;
        }


        .pos-mobile-bottom button {
            flex: 1;
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 15px;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg, #ff7a18, #af002d);
        }

        .pos-mobile-bottom-total span {
            display: block;
            font-size: 12px;
            color: #94a3b8;
        }

        .pos-mobile-bottom-total strong {
            font-size: 20px;
            color: #0f172a;
        }

        .pos-payment-methods {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .pos-payment-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .pos-payment-row input[type="text"] {
            width: 120px;
            border-radius: 8px;
            border: 1px solid #e4e7ec;
            padding: 8px;
        }

        .pos-search-dropdown {
            position: relative;
            margin-top: 8px;
            border: 1px solid #e4e7ec;
            border-radius: 10px;
            background: #fff;
            max-height: 220px;
            overflow-y: auto;
        }

        .pos-search-dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border-bottom: 1px solid #f1f5f9;
        }

        .pos-search-dropdown-item:last-child {
            border-bottom: none;
        }

        .pos-dd-thumb {
            width: 38px;
            height: 38px;
            border-radius: 8px;
            object-fit: cover;
        }

        .pos-mobile-note textarea {
            border-radius: 10px;
            border: 1px solid #e4e7ec;
            width: 100%;
            padding: 10px;
        }

        .pos-totals {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .pos-totals-row {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            color: #475569;
        }

        .pos-totals-row span:last-child {
            font-weight: 600;
        }

        .pos-totals-grand {
            font-size: 15px;
            font-weight: 700;
            color: #111827;
        }

        .pos-pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }

        .pos-mobile-payment-actions {
            display: flex;
            gap: 8px;
        }

        .pos-mobile-payment-actions button {
            flex: 1;
            border-radius: 10px;
            border: none;
            padding: 10px;
            font-weight: 600;
        }

        .pos-btn-preview {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .pos-btn-holdlist {
            background: #fef3c7;
            color: #92400e;
        }

        .pos-btn-muted {
            background: #f1f5f9;
            color: #0f172a;
        }

        .pos-modal-backdrop {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
            z-index: 980;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .pos-modal {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 520px;
            max-height: 90vh;
            overflow-y: auto;
            padding: 20px;
        }

        .pos-modal-header {
            margin-bottom: 12px;
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
                invoiceUrlBase: "{{ route('order.invoice', ['slug' => '__SLUG__']) }}",
            },
            warehouses: @json($warehouses ?? [])
        };
    </script>
    <script src="{{ versioned_asset('assets/js/pos_order_vue.js') }}" defer></script>
@endsection

@section('page_title', 'POS Mobile Order')

@section('page_heading', 'POS Mobile Order')

@section('content')
    <div id="pos-desktop-app" class="pos-mobile-shell">
        <div class="pos-mobile-page" v-cloak>
            <div class="pos-mobile-top">
                <div class="pos-mobile-warehouse">
                    <label>Warehouse</label>
                    <select v-model="selectedWarehouseId" @change="onWarehouseChange">
                        <option :value="null">All Warehouses</option>
                        <option v-for="w in warehouses" :key="w.id" :value="w.id">
                            @{{ w.title }}
                        </option>
                    </select>
                </div>

                <div class="pos-mobile-tabs">
                    <button type="button" class="pos-mobile-tab" :class="{ 'active': activeTab === 'customer' }"
                        @click="setActiveTab('customer')">Customer</button>
                    <button type="button" class="pos-mobile-tab" :class="{ 'active': activeTab === 'products' }"
                        @click="setActiveTab('products')">Products</button>
                    <button type="button" class="pos-mobile-tab" :class="{ 'active': activeTab === 'cart' }"
                        @click="setActiveTab('cart')">Cart</button>
                    <button type="button" class="pos-mobile-tab" :class="{ 'active': activeTab === 'payment' }"
                        @click="setActiveTab('payment')">Payment</button>
                </div>
            </div>

            <div class="pos-mobile-panel" :class="{ 'active': activeTab === 'customer' }">
                <div class="pos-mobile-card">
                    <label>Search customer</label>
                    <div class="pos-mobile-search-row mt-2">
                        <input type="text" v-model="customerSearch" placeholder="Mobile, name, email or ID"
                            @keyup.enter="searchCustomer">
                        <button type="button" @click="searchCustomer"><i class="fas fa-search"></i></button>
                    </div>
                    <div class="pos-mobile-actions mt-3">
                        <button type="button" @click="openCreateCustomer">Create Customer</button>
                    </div>
                </div>
                <div class="pos-mobile-card" v-if="selectedCustomer">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">@{{ selectedCustomer.name }}</h5>
                            <p class="text-muted mb-0">@{{ selectedCustomer.mobile }}</p>
                        </div>
                        <small class="text-muted">ID: @{{ selectedCustomer.id }}</small>
                    </div>
                    <div class="mt-2 text-muted">
                        Due: <strong>@{{ formatMoney(selectedCustomer.due || 0) }}</strong> ·
                        Advance: <strong>@{{ formatMoney(selectedCustomer.advance || 0) }}</strong>
                    </div>
                </div>
                <div class="pos-mobile-card pos-mobile-note">
                    <label>Order Note</label>
                    <textarea rows="3" v-model="orderNote" placeholder="Add note for this order (optional)"></textarea>
                </div>
            </div>

            <div class="pos-mobile-panel" :class="{ 'active': activeTab === 'products' }">
                <div class="pos-mobile-card">
                    <div class="pos-mobile-search-wrapper">
                        <div class="pos-mobile-search-row">
                            <input type="text" v-model="searchQuery" @input="onSearchInput"
                                placeholder="Search products, SKU or barcode" ref="searchInput">
                            <button type="button" @click="openCamera"><i class="fas fa-camera"></i></button>
                        </div>
                    </div>
                    <div class="pos-mobile-filters">
                        <select v-model="filters.category" @change="onFilterChange">
                            <option :value="null">All Categories</option>
                            <option v-for="c in categories" :key="c.id" :value="c.id">@{{ c.name }}</option>
                        </select>
                        <select v-model="filters.subcategory" @change="onFilterChange">
                            <option :value="null">All Subcategories</option>
                            <option v-for="s in subcategories" :key="s.id" :value="s.id">@{{ s.name }}</option>
                        </select>
                        <select v-model="filters.childcategory" @change="onFilterChange">
                            <option :value="null">All Child</option>
                            <option v-for="ch in childcategories" :key="ch.id" :value="ch.id">@{{ ch.name }}</option>
                        </select>
                    </div>

                    <div v-if="showSearchDropdown" class="pos-search-dropdown mt-3">
                        <div class="pos-search-dropdown-item" v-for="r in searchResults" :key="r.id"
                            @click="onAddFromSearch(r)">
                            <img :src="r.image_url" class="pos-dd-thumb" alt="">
                            <div>
                                <div class="fw-bold">@{{ r.title || r.name }}</div>
                                <small class="text-muted">Stock: @{{ r.stock }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="pos-mobile-products">
                        <div class="pos-product-card" v-for="p in products" :key="p.id" @click="selectProduct(p)">
                            <img :src="p.image_url" class="pos-product-thumb" alt="">
                            <div class="pos-product-name">@{{ p.name }}</div>
                            <div class="pos-product-meta">
                                <span class="pos-product-price">@{{ formatMoney(p.unit_price) }}</span>
                                <span class="text-muted">Stock: @{{ p.stock }}</span>
                                <button class="pos-product-add" type="button" @click.stop="selectProduct(p)">Add</button>
                            </div>
                        </div>
                    </div>

                    <div class="pos-pagination mt-3">
                        <button class="btn btn-sm btn-light" @click="prevPage" :disabled="page === 1">Prev</button>
                        <span>Page @{{ page }}</span>
                        <button class="btn btn-sm btn-light" @click="nextPage" :disabled="!hasMore">Next</button>
                    </div>
                </div>
            </div>

            <div class="pos-mobile-panel" :class="{ 'active': activeTab === 'cart' }">
                <div class="pos-mobile-cart-list" v-if="cart.length">
                    <div class="pos-mobile-cart-item" v-for="(it, index) in cart" :key="it.temp_id">
                        <div class="pos-mobile-cart-head">
                            <img :src="it.image_url" class="pos-cart-thumb" alt="">
                            <div>
                                <div class="pos-mobile-cart-title">@{{ it.title }}</div>
                                <small class="text-muted">Unit: @{{ formatMoney(it.unit_price) }}</small>
                            </div>
                        </div>
                        <div class="pos-mobile-cart-inputs">
                            <div>
                                <label>Qty</label>
                                <input type="text" class="pos-cart-input" :value="it.qty"
                                    @focus="$event.target.select()"
                                    @keyup.up.prevent="incrementValue($event, 'qty', it)"
                                    @keyup.down.prevent="decrementValue($event, 'qty', it)"
                                    @input="updateCartValue($event, 'qty', it)" @blur="recalcItem(it)">
                            </div>
                            <div>
                                <label>Unit</label>
                                <input type="text" class="pos-cart-input" :value="it.unit_price"
                                    @focus="$event.target.select()"
                                    @keyup.up.prevent="incrementValue($event, 'unit_price', it)"
                                    @keyup.down.prevent="decrementValue($event, 'unit_price', it)"
                                    @input="updateCartValue($event, 'unit_price', it)" @blur="recalcItem(it)">
                            </div>
                            <div>
                                <label>Disc (%)</label>
                                <input type="text" class="pos-cart-input" :value="it.discount.percent"
                                    @focus="$event.target.select()"
                                    @keyup.up.prevent="incrementValue($event, 'discount.percent', it)"
                                    @keyup.down.prevent="decrementValue($event, 'discount.percent', it)"
                                    @input="updateDiscountValue($event, 'percent', it)"
                                    @blur="onItemDiscountChange(it, 'percent')">
                            </div>
                            <div>
                                <label>Disc (Tk)</label>
                                <input type="text" class="pos-cart-input" :value="it.discount.fixed"
                                    @focus="$event.target.select()"
                                    @keyup.up.prevent="incrementValue($event, 'discount.fixed', it)"
                                    @keyup.down.prevent="decrementValue($event, 'discount.fixed', it)"
                                    @input="updateDiscountValue($event, 'fixed', it)"
                                    @blur="onItemDiscountChange(it, 'fixed')">
                            </div>
                        </div>
                        <div class="pos-mobile-cart-total">
                            Line Total: @{{ formatMoney(it.final_price) }}
                        </div>
                    </div>
                </div>
                <div class="text-center text-muted" v-else>
                    Cart is empty. Add products to get started.
                </div>

                <div class="pos-mobile-card">
                    <div class="pos-mobile-cart-actions">
                        <button class="pos-btn-save" type="button" @click="saveOrder">Save</button>
                        <button class="pos-btn-hold" type="button" @click="holdOrder">Hold</button>
                        <button class="pos-btn-hold-list" type="button" @click="openHoldList">Hold List</button>
                        <button class="pos-btn-cancel" type="button" @click="cancelOrder">Cancel</button>
                    </div>
                </div>
            </div>

            <div class="pos-mobile-panel" :class="{ 'active': activeTab === 'payment' }">
                <div class="pos-mobile-card">
                    <div class="pos-payment-field">
                        <span>Coupon</span>
                        <div class="d-flex gap-2">
                            <input type="text" v-model="coupon.code" style="width: 120px; border-radius: 8px; border: 1px solid #e2e8f0;"
                                placeholder="Code">
                            <button class="pos-btn-muted" type="button" @click="applyCoupon">Apply</button>
                        </div>
                    </div>
                    <div class="pos-payment-field">
                        <span>Extra Charge</span>
                        <input type="text" :value="extra_charge" @focus="$event.target.select()"
                            @keyup.up.prevent="incrementValue($event, 'extra_charge', null)"
                            @keyup.down.prevent="decrementValue($event, 'extra_charge', null)"
                            @input="updateValue($event, 'extra_charge')" @blur="recalcTotals">
                    </div>
                    <div class="pos-payment-field">
                        <span>Delivery Charge</span>
                        <input type="text" :value="delivery_charge" @focus="$event.target.select()"
                            @keyup.up.prevent="incrementValue($event, 'delivery_charge', null)"
                            @keyup.down.prevent="decrementValue($event, 'delivery_charge', null)"
                            @input="updateValue($event, 'delivery_charge')" @blur="recalcTotals">
                    </div>
                    <div class="pos-payment-field">
                        <span>Round Off</span>
                        <input type="text" :value="round_off" @focus="$event.target.select()"
                            @keyup.up.prevent="incrementValue($event, 'round_off', null)"
                            @keyup.down.prevent="decrementValue($event, 'round_off', null)"
                            @input="updateValue($event, 'round_off')" @blur="recalcTotals">
                    </div>

                    <div class="pos-totals mt-3">
                        <div class="pos-totals-row">
                            <span>Subtotal</span>
                            <span>@{{ formatMoney(totals.subtotal) }}</span>
                        </div>
                        <div class="pos-totals-row">
                            <span>Discount</span>
                            <span>@{{ formatMoney(totals.discount.amount || 0) }}</span>
                        </div>
                        <div class="pos-totals-row">
                            <span>Coupon</span>
                            <span>@{{ formatMoney(totals.coupon.amount || 0) }}</span>
                        </div>
                        <div class="pos-totals-row pos-totals-grand">
                            <span>Grand Total</span>
                            <span>@{{ formatMoney(totals.grand_total) }}</span>
                        </div>
                    </div>

                    <div class="pos-mobile-payment-actions mt-3">
                        {{-- <button type="button" class="pos-btn-preview" @click="previewOrder">Preview</button> --}}
                        <button type="button" class="pos-btn-holdlist" @click="openHoldList">Hold List</button>
                    </div>
                </div>
            </div>

            <div class="pos-mobile-bottom">
                <button type="button" @click="openPaymentModal">
                    Create Order
                </button>
                <div class="pos-mobile-bottom-total">
                    <span>Grand Total</span>
                    <strong>@{{ formatMoney(totals.grand_total) }}</strong>
                </div>
            </div>

            <!-- Payment Modal -->
            <div class="pos-modal-backdrop" v-if="showPaymentModal">
                <div class="pos-modal">
                    <div class="pos-modal-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Payment</h4>
                        <button type="button" class="btn btn-sm btn-light" @click="closePaymentModal">&times;</button>
                    </div>
                    <div class="pos-modal-body">
                        <p><strong>Customer:</strong> @{{ selectedCustomer ? selectedCustomer.name : 'Walk-in customer' }}</p>
                        <p><strong>Grand Total:</strong> @{{ formatMoney(totals.grand_total) }}</p>
                        <div class="mb-2" v-if="selectedCustomer">
                            <label>
                                <input type="checkbox" v-model="useAdvance">
                                Use Advance (available: @{{ formatMoney(selectedCustomer.advance || 0) }})
                            </label>
                        </div>
                        <div class="pos-payment-methods">
                            <div class="pos-payment-row" v-for="method in paymentMethods" :key="method.id">
                                <label class="d-flex align-items-center gap-2">
                                    <input type="checkbox" v-model="method.selected">
                                    @{{ method.title }}
                                </label>
                                <input v-if="method.selected" type="text" :value="method.amount"
                                    :max="getPaymentMaxAmount(method)" @focus="onPaymentFocus($event, method)"
                                    @keyup.up.prevent="incrementPaymentValue($event, method)"
                                    @keyup.down.prevent="decrementPaymentValue($event, method)"
                                    @input="updatePaymentValue($event, method)">
                            </div>
                        </div>
                    </div>
                    <div class="pos-modal-footer">
                        <div class="pos-modal-footer-row">
                            <div>
                                <strong>Total Paid:</strong> @{{ formatMoney(paymentTotal) }}<br>
                                <strong>Remaining:</strong> @{{ formatMoney(totals.grand_total - paymentTotal) }}
                            </div>
                            <div class="d-flex gap-2">
                                {{-- <button type="button" class="btn btn-secondary" @click="previewOrder">Preview</button> --}}
                                <button type="button" class="btn btn-primary" @click="submitOrder">Submit</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hold List Modal -->
            <div class="pos-modal-backdrop" v-if="showHoldListModal">
                <div class="pos-modal">
                    <div class="pos-modal-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Hold Orders (@{{ selectedWarehouseName }})</h4>
                        <button type="button" class="btn btn-sm btn-light" @click="closeHoldList">&times;</button>
                    </div>
                    <div class="pos-modal-body">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Items</th>
                                    <th>Total</th>
                                    <th>Time</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="h in holdList" :key="h.id">
                                    <td>#@{{ h.id }}</td>
                                    <td>@{{ h.items_count }}</td>
                                    <td>@{{ formatMoney(h.grand_total) }}</td>
                                    <td>@{{ h.created_at }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary" type="button" @click="loadHold(h.id)">Load</button>
                                    </td>
                                </tr>
                                <tr v-if="holdList.length === 0">
                                    <td colspan="5" class="text-center text-muted">No holds found</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Customer Modal -->
            <div class="pos-modal-backdrop" v-if="showCustomerModal">
                <div class="pos-modal">
                    <div class="pos-modal-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Create Customer</h4>
                        <button type="button" class="btn btn-sm btn-light" @click="closeCustomerModal">&times;</button>
                    </div>
                    <div class="pos-modal-body">
                        <div class="form-group">
                            <label>Name *</label>
                            <input type="text" class="form-control" v-model="newCustomer.name">
                        </div>
                        <div class="form-group">
                            <label>Mobile *</label>
                            <input type="text" class="form-control" v-model="newCustomer.mobile">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" v-model="newCustomer.email">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <textarea class="form-control" rows="2" v-model="newCustomer.address"></textarea>
                        </div>
                    </div>
                    <div class="pos-modal-footer">
                        <div class="pos-modal-footer-row justify-content-end">
                            <button type="button" class="btn btn-secondary" @click="closeCustomerModal">Cancel</button>
                            <button type="button" class="btn btn-success" @click="submitNewCustomer">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (window.innerWidth >= 992) {
                window.location.href = "{{ route('pos.desktop.index') }}";
            }
        });
    </script>
@endpush


