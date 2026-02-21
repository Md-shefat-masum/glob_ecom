@extends('backend.master')

@section('header_css')
    <link rel="stylesheet" href="/assets/plugins/select2/select2.min.css">
    <style>
        
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
                <div class="pos-customer-manage-row">
                    <div>
                        <pos-customer-manage
                            :set-selected-customer="setSelectedCustomer"
                            :customer_sources="customerSources"
                            :selected-customer="selectedCustomer"
                        ></pos-customer-manage>
                    </div>
                </div>
                
            </div>

            <div class="pos-top">

                <!-- LEFT: products & search -->
                <div class="pos-left">
                    <div class="pos-search-row">
                        <div style="flex: 1;">
                            <input type="text" v-model="searchQuery" 
                                @focus="show_product_search_result = true"
                                @input="onSearchInput"
                                :class="{ 'pos-search-loading': loading.search }"
                                class="w-100"
                                placeholder="Search product by name" ref="searchInput">
                        </div>
                        <div style="width: 200px;">
                            <input type="text" class="w-100" placeholder="Search product by barcode" @focus="clear_value_for_barcode($event)" v-model="barcodeQuery" @input="onBarcodeInput">
                        </div>
                    </div>

                    <div class="cart_list_wrapper">
                        <table class="pos-cart-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th class="cart_col_width">Qty</th>
                                    <th class="cart_col_width">Unit Price</th>
                                    <th class="cart_col_width">Disc (%)</th>
                                    <th class="cart_col_width">Disc (Tk)</th>
                                    <th class="cart_col_width">Disc Price</th>
                                    <th class="cart_col_width">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-for="(it, index) in cart" :key="it.temp_id">
                                    <!-- First row: image + name -->
                                    
                                    <tr v-if="window_width < 1300">
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
                                        <td v-if="window_width >= 1300">
                                            <div class="pos-cart-item-cell">
                                                <img :src="it.image_url" class="pos-cart-thumb" alt="">
                                                <div class="pos-cart-item-title pos-cart-item-title-ellipsis" style="flex: 1;"
                                                    :title="it.title">
                                                    <div class="pos-cart-item-title-row">
                                                        <div class="pos-cart-item-title-text">
                                                            @{{ it.title }}
                                                        </div>
                                                        <div class="pos-cart-item-title-remove">
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
                                                        {{-- <span v-if="it.warehouse_name" style="background: #f0f0f0; padding: 2px 4px; border-radius: 4px; white-space: normal;line-height: 25px;">
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
                                                        </span> &nbsp; --}}
                                                        <span v-if="it.unit_code" :style="'border: 1px solid #' + randomHex(it.unit_code || it.temp_id) + '; color: black; padding: 2px 4px; border-radius: 4px;'">
                                                            @{{ it.unit_code }}
                                                        </span>
                                                    </div>
                                                    <div class="pos-cart-item-variant" v-if="!it.variant_combination_key">
                                                        <span>
                                                            Avl: @{{ it.max_qty }}
                                                        </span> &nbsp;
                                                        {{-- <span v-if="it.warehouse_name" style="background: #f0f0f0; padding: 2px 4px; border-radius: 4px;white-space: normal;line-height: 25px;">
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
                                                        </span> &nbsp; --}}
                                                        <span v-if="it.unit_code" :style="'border: 1px solid #' + randomHex(it.unit_code || it.temp_id) + '; color: black; padding: 2px 4px; border-radius: 4px;'">
                                                            @{{ it.unit_code }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="cart_col_width">
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
                                        <td class="cart_col_width">
                                            <input type="text" class="pos-cart-input"
                                                :value="it.unit_price"
                                                @focus="$event.target.select()"
                                                @keyup.up.prevent="incrementValue($event, 'unit_price', it)"
                                                @keyup.down.prevent="decrementValue($event, 'unit_price', it)"
                                                @input="updateCartValue($event, 'unit_price', it)"
                                                @blur="recalcItem(it)">
                                        </td>
                                        <td class="cart_col_width">
                                            <input type="text" class="pos-cart-input"
                                                :value="it.discount.percent"
                                                @focus="$event.target.select()"
                                                @keyup.up.prevent="incrementValue($event, 'discount.percent', it)"
                                                @keyup.down.prevent="decrementValue($event, 'discount.percent', it)"
                                                @input="updateDiscountValue($event, 'percent', it)"
                                                @blur="onItemDiscountChange(it, 'percent')">
                                        </td>
                                        <td class="cart_col_width">
                                            <input type="text" class="pos-cart-input"
                                                :value="it.discount.fixed"
                                                @focus="$event.target.select()"
                                                @keyup.up.prevent="incrementValue($event, 'discount.fixed', it)"
                                                @keyup.down.prevent="decrementValue($event, 'discount.fixed', it)"
                                                @input="updateDiscountValue($event, 'fixed', it)"
                                                @blur="onItemDiscountChange(it, 'fixed')">
                                        </td>
                                        <td class="cart_col_width">
                                            <input type="text" disabled class="pos-cart-input"
                                                :value="it.discount_price">
                                        </td>
                                        <td class="cart_col_width">@{{ formatMoney(it.final_price) }}</td>
                                    </tr>
                                </template>
                                <tr v-if="cart.length === 0">
                                    <td colspan="6" class="text-center text-muted">Cart is empty</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="product_list_wrapper product_list_wrapper_v2" v-if="show_product_search_result">
                        <div class="pos-loading-overlay" v-if="loading.search">
                            <div class="pos-loading-container">
                                <div class="pos-loading-spinner"></div>
                                <div class="pos-loading-text">Loading products...</div>
                            </div>
                        </div>
                        <div class="pos-products-grid pos-products-grid-v2">
                            <pos-product-item 
                                v-for="p in products" :key="p.id" :p="p" 
                                :formatMoney="formatMoney" 
                                :product-item-select="selectProduct"
                                :hide_product_search_result="hide_product_search_result"
                            ></pos-product-item>
                            <template v-if="loading.search && products.length === 0">
                                <div class="pos-product-skeleton" v-for="n in 5" :key="'skeleton-' + n"></div>
                            </template>
                        </div>
                        <div class="pos-product-search-result-footer text-center mt-2">
                            <button type="button" class="btn btn-sm btn-danger" @click="show_product_search_result = false">Close</button>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: cart -->
                <div class="pos-right pos_right_v2" style="display: flex; flex-direction: column; gap: 10px; justify-content: space-between;">
                    <div>
                        <div class="pos-totals">
                            <div class="pos-totals-row pos-totals-subtotal">
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
                                        <input type="radio" name="delivery_charge_type" @change="setDeliveryChargeByType(1, true)" value="inside_city" id="delivery_charge_type_inside_city" v-model="delivery_info.delivery_charge_type">
                                        <span>Inside</span>
                                    </label>
                                    <label for="delivery_charge_type_outside_city">
                                        <input type="radio" name="delivery_charge_type" @change="setDeliveryChargeByType(1, true)" value="outside_city" id="delivery_charge_type_outside_city" v-model="delivery_info.delivery_charge_type">
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
                            <div class="pos-totals-row pos-totals-grand pos_total_grand_total">
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
                            
                            <div v-if="order_status === 'delivered'">
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
                                    <div class="mb-2" v-if="delivery_info.courier_method_title">
                                        <label for="courier_info_address">Courier Address</label>
                                        <textarea id="courier_info_address" v-model="selectedCustomer.address" class="form-control" rows="2" ></textarea>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="exchange_amount_container" v-if="total_cash_payment > 0">
                            <div class="pos-totals-row">
                                <span>
                                    <b>Cash received</b>
                                </span>
                                <span>
                                    <input class="pos-cart-input"
                                        type="text"
                                        v-model="cash_received"
                                        @focus="$event.target.select()">
                                </span>
                            </div>
                            <div class="pos-totals-row">
                                <span><b>Exchange Amount</b></span>
                                <span>@{{ exchange_amount }}</span>
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
