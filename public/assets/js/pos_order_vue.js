function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(this, args);
        }, wait);
    };
}

document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('pos-desktop-app');
    if (!el || typeof Vue === 'undefined') {
        return;
    }

    const routes = (window.POS_DESKTOP_CONFIG && window.POS_DESKTOP_CONFIG.routes) || {};
    const LOCAL_SAVE_KEY = 'pos_desktop_saved_order';

    new Vue({
        el: '#pos-desktop-app',
        data: function () {
            return {
                // warehouses
                warehouses: (window.POS_DESKTOP_CONFIG && window.POS_DESKTOP_CONFIG.warehouses) || [],
                selectedWarehouseId: null,
                // products & filters
                products: [],
                page: 1,
                perPage: 24,
                hasMore: false,

                selected_category_type: 'category_id', // category_id, subcategory_id, childcategory_id
                selected_category_id: null,

                categories: [],
                subcategories: [],
                childcategories: [],
                customerSources: [],
                deliveryMethods: [],
                courierMethods: [],
                outlets: [],
                filters: {
                    category: null,
                    subcategory: null,
                    childcategory: null,
                },

                // search
                searchQuery: '',
                barcodeQuery: '',
                showSearchDropdown: false,
                searchResults: [],

                // cart
                cart: [],
                totals: {
                    subtotal: 0,
                    discount: { type: 'percent', value: 0, amount: 0 },
                    coupon: { code: '', percent: 0, amount: 0 },
                    extra_charge: 0,
                    delivery_charge: 0,
                    round_off: 0,
                    grand_total: 0,
                },
                coupon: {
                    code: '',
                    percent: 0,
                    type: '',   // 'percent' | 'fixed' when applied
                    value: 0,  // percent number or fixed amount
                },
                extra_charge: 0,
                delivery_charge: 0,
                round_off: 0,

                // customer
                customerSearch: '',
                selectedCustomer: {
                    id: 1,
                    name: 'Walking Customer',
                    phone: '',
                    email: '',
                    address: '',
                    image: null,
                },
                showCustomerModal: false,
                newCustomer: {
                    name: '',
                    mobile: '',
                    email: '',
                    address: '',
                },

                // payment
                showPaymentModal: false,
                paymentMethods: [
                    { id: 'cash', title: 'Cash', selected: true, amount: 0 },
                    { id: 'bkash', title: 'Bkash', selected: true, amount: 0 },
                    { id: 'nogod', title: 'Nogod', selected: true, amount: 0 },
                    { id: 'rocket', title: 'Rocket', selected: true, amount: 0 },
                    { id: 'bank', title: 'Bank', selected: true, amount: 0 },
                ],
                delivery_info: {
                    delivery_method: '',
                    expected_delivery_date: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    order_source: 'pos',
                    order_note: '',
                    outlet_id: '',
                    courier_method: null,
                    courier_method_title: '',
                    delivery_charge_type: 'inside_city',
                },
                useAdvance: false,
                advanceAmount: 0,

                // holds
                showHoldListModal: false,
                holdList: [],
                hasSavedDraft: false,
                activeTab: 'customer',

                // loading states
                loading: {
                    products: false,
                    search: false,
                    categories: false,
                    customer: false,
                    coupon: false,
                    hold: false,
                    holdList: false,
                    order: false,
                    barcode: false,
                },
                // target stats (auth user sales target analytics)
                targetStats: null,
                order_status: 'invoiced',
            };
        },
        computed: {
            paymentTotal() {
                const base = this.paymentMethods.reduce((sum, m) => {
                    return sum + (m.selected ? Number(m.amount || 0) : 0);
                }, 0);

                let advance = 0;
                if (this.useAdvance) {
                    advance = Number(this.advanceAmount || 0);
                }

                return base + advance;
            },
            selectedWarehouseName() {
                if (!this.selectedWarehouseId) {
                    return 'All Warehouses';
                }
                const found = this.warehouses.find(w => Number(w.id) === Number(this.selectedWarehouseId));
                return found ? found.title : 'Warehouse';
            },
            totalPurchasePrice() {
                return this.cart.reduce((sum, item) => {
                    // Assumes each cart item has purchase_price and qty (quantity)
                    const price = Number(item.purchase_price || 0);
                    const qty = Number(item.qty || 0);
                    return sum + (price * qty);
                }, 0);
            }
        },
        watch: {
            selectedCustomer(newVal, oldVal) {
                if (!newVal) {
                    this.activeTab = 'customer';
                }
                // Reset advance when customer changes
                if (newVal && (!oldVal || newVal.id !== oldVal.id)) {
                    this.useAdvance = false;
                    this.advanceAmount = 0;
                }
            },
        },
        mounted() {
            if (!this.selectedWarehouseId && this.warehouses && this.warehouses.length > 0) {
                this.selectedWarehouseId = this.warehouses[0].id;
            }
            if (this.$refs && this.$refs.searchInput) {
                this.$nextTick(() => this.$refs.searchInput.focus());
            }
            this.loadCategories();
            this.loadPaymentMethods();
            this.fetchProducts();
            this.checkForSavedOrder(true);
            this.loadTargetStats();
            this.loadCustomerSources();
            this.loadDeliveryMethods();
            this.loadOutlets();
            this.loadCourierMethods();
        },
        methods: {
            loadCustomerSources() {
                if (!routes.customerSource) return;
                this.get(routes.customerSource)
                    .then((r) => {
                        this.customerSources = r.data.data;
                    })
                    .catch(() => { });
            },
            loadDeliveryMethods() {
                if (!routes.deliveryMethods) return;
                this.get(routes.deliveryMethods)
                    .then((r) => {
                        this.deliveryMethods = r.data.data;
                    })
                    .catch(() => { });
            },
            loadOutlets() {
                if (!routes.outlets) return;
                this.get(routes.outlets)
                    .then((r) => {
                        this.outlets = r.data.data;
                    })
                    .catch(() => { });
            },
            loadCourierMethods() {
                if (!routes.courierMethods) return;
                this.get(routes.courierMethods)
                    .then((r) => {
                        this.courierMethods = r.data.data;
                    })
                    .catch(() => { });
            },
            s_alert(title, icon = 'info', text = null) {
                const opts = { title, icon, allowOutsideClick: false, allowEscapeKey: false };
                if (text) opts.text = text;
                if (typeof Swal !== 'undefined') {
                    Swal.fire(opts);
                } else {
                    alert(title);
                }
            },
            s_confirm(title, text = null, icon = 'question') {
                if (typeof Swal !== 'undefined') {
                    return Swal.fire({
                        title,
                        text: text || undefined,
                        icon,
                        showCancelButton: true,
                        confirmButtonText: 'Yes',
                        cancelButtonText: 'Cancel',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                    }).then((result) => !!result.isConfirmed);
                }
                return Promise.resolve(!!window.confirm(title));
            },
            setSelectedCategory(type, id) {
                console.log('setSelectedCategory', type, id);
                this.selected_category_type = type;
                this.selected_category_id = id;
                this.page = 1;
                this.fetchProducts();
            },
            setSelectedCustomer(customer) {
                this.selectedCustomer = customer;
            },
            formatMoney(v) {
                return (Number(v) || 0).toFixed(2);
            },
            randomHex(seed) {
                const s = String(seed || Math.random());
                let h = 0;
                for (let i = 0; i < s.length; i++) h = ((h << 5) - h) + s.charCodeAt(i) | 0;
                const c = (h & 0xFFFFFF).toString(16).padStart(6, '0').slice(0, 6);
                return c;
            },
            setActiveTab(tab) {
                if (tab !== 'customer' && !this.selectedCustomer) {
                    this.s_alert('Select or create a customer first.', 'warning');
                    this.activeTab = 'customer';
                    return;
                }
                this.activeTab = tab;
            },

            // API helpers
            get(url, params = {}) {
                const p = Object.assign({}, params);
                if (this.selectedWarehouseId) {
                    p.warehouse_id = this.selectedWarehouseId;
                }
                return axios.get(url, { params: p });
            },
            post(url, body = {}) {
                const payload = Object.assign({}, body);
                if (this.selectedWarehouseId && !payload.warehouse_id) {
                    payload.warehouse_id = this.selectedWarehouseId;
                }
                return axios.post(url, payload);
            },

            // LOADERS
            loadTargetStats() {
                if (!routes.targetStats) return;
                this.get(routes.targetStats)
                    .then((r) => {
                        if (r.data && r.data.success && r.data.data) {
                            this.targetStats = r.data.data;
                        }
                    })
                    .catch(() => { });
            },
            loadPaymentMethods() {
                if (!routes.paymentMethods) return;
                this.get(routes.paymentMethods)
                    .then((r) => {
                        if (r.data && r.data.success && r.data.data) {
                            // Transform payment methods to match expected format
                            const mapped = r.data.data.map((method) => ({
                                id: method.id,
                                payment_type_id: method.payment_type_id,
                                title: method.title,
                                account_id: method.account_id,
                                account_name: method.account_name,
                                // selected: ['cash', 'Cash'].includes(method.title),
                                selected: true,
                                amount: 0
                            }));
                            // Cash first, then others alphabetically by title
                            const methods = mapped.sort((a, b) => {
                                const aIsCash = /^cash$/i.test((a.title || '').trim());
                                const bIsCash = /^cash$/i.test((b.title || '').trim());
                                if (aIsCash && !bIsCash) return -1;
                                if (!aIsCash && bIsCash) return 1;
                                if (aIsCash && bIsCash) return 0;
                                return (a.title || '').localeCompare(b.title || '', undefined, { sensitivity: 'base' });
                            });

                            // If we have methods, replace the default ones
                            if (methods.length > 0) {
                                this.paymentMethods = methods;
                            }
                        }
                    })
                    .catch(() => {
                        // Keep default payment methods on error
                    });
            },
            loadCategories() {
                if (!routes.categories) return;
                this.loading.categories = true;
                this.get(routes.categories)
                    .then((r) => {
                        const data = r.data && r.data.data ? r.data.data : {};
                        this.categories = data.categories || [];
                        this.subcategories = data.subcategories || [];
                        this.childcategories = data.childcategories || [];
                    })
                    .catch(() => { })
                    .finally(() => {
                        this.loading.categories = false;
                    });
            },
            fetchProducts() {
                if (!routes.products) return;
                this.loading.products = true;
                this.get(routes.products, {
                    page: this.page,
                    per_page: this.perPage,
                    [this.selected_category_type]: this.selected_category_id,
                })
                    .then((r) => {
                        const data = (r.data && r.data.data) || {};
                        this.products = data.items || [];
                        this.hasMore = !!data.has_more;
                    })
                    .catch(() => { })
                    .finally(() => {
                        this.loading.products = false;
                    });
            },
            clear_value_for_barcode(event) {
                this.barcodeQuery = '';
            },
            onBarcodeInput: debounce(function () {
                if (!routes.productsByBarcode) return;
                this.loading.barcode = true;
                this.post(routes.productsByBarcode, { code: this.barcodeQuery })
                    .then((r) => {
                        this.selectProduct(r.data.data);
                    })
                    .catch((e) => {
                        this.s_alert((e.response.data.message ?? 'Barcode lookup failed'), 'error');
                    })
                    .finally(() => {
                        this.loading.barcode = false;
                    });
            }, 300),
            onWarehouseChange() {
                // Reload products and clear cart when warehouse changes
                this.cart = [];
                this.recalcTotals();
                this.fetchProducts();
            },
            onFilterChange() {
                this.page = 1;
                this.fetchProducts();
            },
            prevPage() {
                if (this.page > 1) {
                    this.page--;
                    this.fetchProducts();
                }
            },
            nextPage() {
                if (this.hasMore) {
                    this.page++;
                    this.fetchProducts();
                }
            },

            // SEARCH
            onSearchInput: debounce(function () {
                const q = (this.searchQuery || '').trim();
                if (!q) {
                    this.showSearchDropdown = false;
                    this.loading.search = false;
                    return;
                }
                if (!routes.search) return;
                this.loading.search = true;
                this.get(routes.search, { q: q })
                    .then((r) => {
                        // this.searchResults = r.data && r.data.data ? r.data.data : [];
                        // this.showSearchDropdown = true;
                        this.products = r.data && r.data.data ? r.data.data.items : [];
                    })
                    .catch(() => { })
                    .finally(() => {
                        this.loading.search = false;
                    });
            }, 1000),
            closeSearch() {
                this.showSearchDropdown = false;
            },

            // CAMERA / BARCODE
            openCamera() {
                const code = window.prompt('Enter or scan barcode:');
                if (code) {
                    this.barcodeLookup(code);
                }
            },
            barcodeLookup(code) {
                if (!routes.barcode) return;
                this.loading.barcode = true;
                this.post(routes.barcode, { code: code })
                    .then((r) => {
                        const data = r.data && r.data.data ? r.data.data : {};
                        if (data.single) {
                            this.onAddFromSearch(data.single);
                        } else if (Array.isArray(data.items) && data.items.length) {
                            this.searchResults = data.items;
                            this.showSearchDropdown = true;
                        } else {
                            this.s_alert('No product found for barcode', 'warning');
                        }
                    })
                    .catch(() => {
                        this.s_alert('Barcode lookup failed', 'error');
                    })
                    .finally(() => {
                        this.loading.barcode = false;
                    });
            },

            removeItem(it, index) {
                this.cart.splice(index, 1);
                this.recalcTotals();
            },

            // CART
            selectProduct: function (p, variantPayload) {
                let unit_data = {};
                if (p.unit) {
                    unit_data.unit_code = p.unit.code;

                    unit_data.warehouse_name = p.unit.warehouse_name;
                    unit_data.cartoon_name = p.unit.cartoon_name;
                    unit_data.room_name = p.unit.room_name;

                    unit_data.warehouse_id = p.unit.warehouse_id;
                    unit_data.room_id = p.unit.room_id;
                    unit_data.cartoon_id = p.unit.cartoon_id;
                    unit_data.purchase_price = p.unit.purchase_price;
                }

                // Variant chosen from product card (e.g. variant_combination_key)
                if (p.has_variants && variantPayload && variantPayload.variant_combination_key) {
                    let data = {
                        product_id: p.product_id || p.id,
                        variant_id: variantPayload.variant_id || null,
                        variant_combination_key: variantPayload.variant_combination_key,
                        qty: 1,
                        max_qty: variantPayload.max_qty || 0,
                        title: p.name,
                        image_url: p.image_url,
                        unit_price: p.unit_price || 0,
                        ...unit_data,
                    };

                    this.addCartItem(data);
                    return;
                }
                // if (p.has_variants && routes.search) {
                //     this.loading.search = true;
                //     this.get(routes.search, { product_id: p.id })
                //         .then((r) => {
                //             this.searchResults = r.data && r.data.data ? r.data.data : [];
                //             this.showSearchDropdown = true;
                //         })
                //         .catch(() => {})
                //         .finally(() => {
                //             this.loading.search = false;
                //         });
                // }
                this.addCartItem({
                    product_id: p.product_id || p.id,
                    variant_id: null,
                    qty: 1,
                    max_qty: p.stock || 0,
                    title: p.name,
                    image_url: p.image_url,
                    unit_price: p.unit_price || 0,
                    ...unit_data,
                });

            },
            onAddFromSearch(r) {
                this.addCartItem({
                    product_id: r.product_id || r.id,
                    variant_id: r.variant_id || r.id || null,
                    qty: 1,
                    title: r.title || r.name,
                    image_url: r.image_url,
                    unit_price: r.unit_price || 0,
                });
                this.closeSearch();
            },
            addCartItem(item) {
                const hasUnitCode = item.unit_code != null && String(item.unit_code).trim() !== '';

                if (hasUnitCode) {
                    const existing = this.cart.find((ci) => ci.unit_code != null && ci.unit_code === item.unit_code);
                    if (existing) {
                        return;
                    }
                    const ci = Object.assign(
                        {
                            temp_id: 'ci-' + Date.now() + Math.random(),
                            discount: { type: 'percent', percent: 0, fixed: 0, value: 0 },
                        },
                        item
                    );
                    ci.final_price = ci.qty * ci.unit_price;
                    this.cart.push(ci);
                } else {
                    const existing = this.cart.find((ci) => {
                        if (ci.product_id !== item.product_id) return false;
                        const keyA = ci.variant_combination_key || '';
                        const keyB = item.variant_combination_key || '';
                        if (keyA || keyB) return keyA === keyB;
                        return ci.variant_id === item.variant_id;
                    });
                    if (existing) {
                        existing.qty += item.qty;
                        if (existing.qty > existing.max_qty) {
                            existing.qty = existing.max_qty;
                        }
                        this.recalcItem(existing);
                    } else {
                        const ci = Object.assign(
                            {
                                temp_id: 'ci-' + Date.now() + Math.random(),
                                discount: { type: 'percent', percent: 0, fixed: 0, value: 0 },
                            },
                            item
                        );
                        ci.final_price = ci.qty * ci.unit_price;
                        this.cart.push(ci);
                    }
                }
                this.recalcTotals();
            },
            recalcItem(it) {
                const qty = Number(it.qty || 0);
                const unitPrice = Number(it.unit_price || 0);
                const gross = qty * unitPrice;
                let finalPrice = gross;

                if (it.discount) {
                    const type = it.discount.type || 'percent';

                    if (type === 'percent') {
                        const percent = Number(it.discount.percent || 0);
                        finalPrice = gross * (1 - percent / 100);
                        const fixed = gross - finalPrice;
                        it.discount.fixed = fixed;
                        it.discount.value = percent;
                    } else if (type === 'fixed') {
                        const fixed = Number(it.discount.fixed || 0);
                        finalPrice = gross - fixed;
                        const percent = gross > 0 ? (fixed / gross) * 100 : 0;
                        it.discount.percent = percent;
                        it.discount.value = percent;
                    }
                }

                it.final_price = finalPrice;
                this.recalcTotals();
            },

            onItemDiscountChange(it, mode) {
                const qty = Number(it.qty || 0);
                const unitPrice = Number(it.unit_price || 0);
                const gross = qty * unitPrice;

                if (!it.discount) {
                    it.discount = { type: 'percent', percent: 0, fixed: 0, value: 0 };
                }

                if (mode === 'percent') {
                    it.discount.type = 'percent';
                    const percent = Number(it.discount.percent || 0);
                    const fixed = gross * (percent / 100);
                    it.discount.fixed = fixed;
                } else if (mode === 'fixed') {
                    it.discount.type = 'fixed';
                    const fixed = Number(it.discount.fixed || 0);
                    const percent = gross > 0 ? (fixed / gross) * 100 : 0;
                    it.discount.percent = percent;
                }

                this.recalcItem(it);
            },
            recalcTotals() {
                const subtotal = this.cart.reduce((sum, it) => sum + Number(it.final_price || 0), 0);
                let discountAmount = 0;
                if (this.totals.discount.type === 'percent') {
                    discountAmount = subtotal * (Number(this.totals.discount.value || 0) / 100);
                } else if (this.totals.discount.type === 'fixed') {
                    discountAmount = Number(this.totals.discount.value || 0);
                }
                discountAmount = Math.floor(discountAmount);
                discountAmount = Math.max(0, Math.min(discountAmount, subtotal));

                let couponAmount = 0;
                const afterDiscountBase = subtotal - discountAmount;
                if (this.coupon.type === 'fixed' && (this.coupon.value || 0) > 0) {
                    couponAmount = Math.min(Number(this.coupon.value) || 0, afterDiscountBase);
                } else if (this.coupon.type === 'percent' && (this.coupon.percent || 0) > 0) {
                    couponAmount = afterDiscountBase * (Number(this.coupon.percent) / 100);
                }
                couponAmount = Math.floor(couponAmount);
                couponAmount = Math.max(0, couponAmount);

                const afterDiscount = subtotal - discountAmount - couponAmount;
                const grand =
                    afterDiscount +
                    Number(this.extra_charge || 0) +
                    Number(this.delivery_charge || 0) -
                    Number(this.round_off || 0);

                this.totals = {
                    subtotal: subtotal,
                    discount: {
                        type: this.totals.discount.type,
                        value: this.totals.discount.value,
                        amount: discountAmount,
                    },
                    coupon: {
                        code: this.coupon.code,
                        percent: this.coupon.percent,
                        type: this.coupon.type,
                        value: this.coupon.value,
                        amount: couponAmount,
                    },
                    extra_charge: Number(this.extra_charge || 0),
                    delivery_charge: Number(this.delivery_charge || 0),
                    round_off: Number(this.round_off || 0),
                    grand_total: Number(grand || 0),
                };

                // Adjust advance amount if it exceeds available balance or due amount
                if (this.useAdvance && this.selectedCustomer && this.selectedCustomer.advance) {
                    const basePayments = this.paymentMethods.reduce((sum, m) => {
                        return sum + (m.selected ? Number(m.amount || 0) : 0);
                    }, 0);
                    const dueAmount = this.totals.grand_total - basePayments;
                    const availableAdvance = Number(this.selectedCustomer.advance) || 0;

                    // Ensure advance doesn't exceed available balance or due amount
                    if (this.advanceAmount > availableAdvance) {
                        this.advanceAmount = availableAdvance;
                    }
                    if (this.advanceAmount > dueAmount) {
                        this.advanceAmount = Math.max(0, dueAmount);
                    }
                }

                this.setDeliveryChargeByType();
            },

            // COUPON
            applyCoupon() {
                if (!routes.applyCoupon) return;
                this.loading.coupon = true;
                this.post(routes.applyCoupon, {
                    code: this.coupon.code,
                    subtotal: this.totals.subtotal,
                })
                    .then((r) => {
                        if (r.data && r.data.success) {
                            const data = r.data.data || {};
                            this.coupon.type = data.type === 'percent' ? 'percent' : 'fixed';
                            this.coupon.value = Number(data.value) || 0;
                            if (data.type === 'percent') {
                                this.coupon.percent = this.coupon.value;
                            } else {
                                this.coupon.percent = 0;
                            }
                            this.recalcTotals();
                        } else {
                            this.s_alert((r.data && r.data.message) || 'Invalid coupon', 'error');
                        }
                    })
                    .catch(() => {
                        this.s_alert('Coupon apply failed', 'error');
                    })
                    .finally(() => {
                        this.loading.coupon = false;
                    });
            },

            // SAVE / HOLD / CANCEL
            holdOrder() {
                if (!routes.hold) return;
                if (!this.cart.length) {
                    this.s_alert('Cart is empty', 'warning');
                    return;
                }
                this.loading.hold = true;
                this.post(routes.hold, {
                    cart: this.cart,
                    totals: this.totals,
                    customer: this.selectedCustomer,
                    order_note: this.orderNote,
                })
                    .then((r) => {
                        if (r.data && r.data.success) {
                            this.s_alert('Order held. ID: ' + (r.data.data && r.data.data.id), 'success');
                            this.clearCart();
                            this.clearSavedOrder();
                        } else {
                            this.s_alert((r.data && r.data.message) || 'Hold failed', 'error');
                        }
                    })
                    .catch(() => {
                        this.s_alert('Hold failed', 'error');
                    })
                    .finally(() => {
                        this.loading.hold = false;
                    });
            },
            saveOrder() {
                if (!this.cart.length) {
                    this.s_alert('Cart is empty', 'warning');
                    return;
                }
                try {
                    const payload = this.serializeOrderState();
                    window.localStorage.setItem(LOCAL_SAVE_KEY, JSON.stringify(payload));
                    this.hasSavedDraft = true;
                    this.s_alert('Order saved locally. You can restore it later on this device.', 'success');
                } catch (e) {
                    console.error('Unable to save order locally', e);
                    this.s_alert('Failed to save order locally.', 'error');
                }
            },
            cancelOrder() {
                this.s_confirm('Cancel order and clear cart?').then((ok) => {
                    if (ok) {
                        this.clearCart();
                        this.clearSavedOrder();
                    }
                });
            },
            clearCart() {
                this.cart = [];
                this.orderNote = '';
                // Reset all totals-related data (discount, coupon, charges, round off)
                this.totals.discount.value = 0;
                this.coupon = { code: '', percent: 0, type: '', value: 0 };
                this.extra_charge = 0;
                this.delivery_charge = 0;
                this.round_off = 0;
                // Reset advance and payment methods
                this.useAdvance = false;
                this.advanceAmount = 0;
                this.paymentMethods.forEach((m) => { m.amount = 0; });
                this.recalcTotals();
                this.setSelectedCustomer({ id: 1, name: 'Walking Customer', phone: '', email: '', address: '', image: null });

                this.fetchProducts();
                this.delivery_info = {
                    delivery_method: '',
                    expected_delivery_date: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    order_source: 'pos',
                    order_note: '',
                    outlet_id: '',
                    courier_method: null,
                    courier_method_title: '',
                };
            },
            setCourierMethod(method) {
                this.delivery_info.courier_method = method ? method.id : null;
                this.delivery_info.courier_method_title = method ? method.title : '';
            },

            // HOLD LIST
            openHoldList() {
                if (!routes.holds) return;
                this.showHoldListModal = true;
                this.loadHoldList();
            },
            closeHoldList() {
                this.showHoldListModal = false;
            },
            loadHoldList() {
                this.loading.holdList = true;
                this.get(routes.holds)
                    .then((r) => {
                        if (r.data && r.data.success) {
                            this.holdList = r.data.data || [];
                        }
                    })
                    .catch(() => { })
                    .finally(() => {
                        this.loading.holdList = false;
                    });
            },
            loadHold(id) {
                if (!routes.getHold) return;
                this.loading.hold = true;
                const url = routes.getHold.replace('__ID__', id);
                this.get(url)
                    .then((r) => {
                        if (r.data && r.data.success) {
                            const data = r.data.data || {};
                            this.cart = data.cart || [];
                            this.totals = data.totals || this.totals;
                            this.selectedCustomer = data.customer || null;
                            this.orderNote = data.note || (data.hold && data.hold.meta ? data.hold.meta.note || '' : '') || '';
                            this.showHoldListModal = false;
                        }
                    })
                    .catch(() => { })
                    .finally(() => {
                        this.loading.hold = false;
                    });
            },

            // PAYMENT MODAL
            openPaymentModal() {
                if (!this.cart.length) {
                    this.s_alert('Cart is empty', 'warning');
                    return;
                }
                if (!this.selectedCustomer) {
                    this.s_alert('Select or create a customer before creating an order.', 'warning');
                    return;
                }
                this.showPaymentModal = true;
            },
            closePaymentModal() {
                this.showPaymentModal = false;
            },
            recalcPayment() {
                // paymentTotal is computed
            },

            // Input value update handlers
            updateCartValue(event, property, item) {
                const value = parseFloat(event.target.value) || 0;
                if (property === 'qty') {
                    item.qty = Math.max(0, Math.floor(value));
                    if (item.qty > item.max_qty) {
                        item.qty = item.max_qty;
                        event.target.value = item.qty;
                    }
                } else {
                    item[property] = Math.max(0, value);
                }
            },
            updateDiscountValue(event, mode, item) {
                const value = parseFloat(event.target.value) || 0;
                if (mode === 'percent') {
                    item.discount.percent = Math.max(0, value);
                } else {
                    item.discount.fixed = Math.max(0, value);
                }
            },
            updateValue(event, property) {
                const raw = event.target.value;
                if (property === 'totals.discount.value') {
                    const value = parseFloat(raw) || 0;
                    this.totals.discount.value = Math.max(0, value);
                    this.recalcTotals();
                    return;
                }
                if (property === 'round_off') {
                    const value = raw === '' || raw === '-' ? null : parseFloat(raw);
                    this.round_off = value === null || isNaN(value) ? 0 : value;
                    this.recalcTotals();
                    return;
                }
                const value = parseFloat(raw) || 0;
                this[property] = Math.max(0, value);
            },
            incrementValue(event, property, item, step = 1) {
                if (item) {
                    // For cart items
                    if (property.includes('.')) {
                        const parts = property.split('.');
                        const obj = item[parts[0]];
                        const key = parts[1];
                        const current = parseFloat(obj[key]) || 0;
                        obj[key] = current + step;
                        if (property === 'discount.percent' || property === 'discount.fixed') {
                            this.onItemDiscountChange(item, key);
                        } else {
                            this.recalcItem(item);
                        }
                    } else {
                        const current = property === 'qty'
                            ? (parseInt(item[property]) || 0)
                            : (parseFloat(item[property]) || 0);
                        item[property] = current + step;
                        if (property === 'qty' || property === 'unit_price') {
                            this.recalcItem(item);
                        }
                    }
                } else {
                    // For data properties (including nested totals.discount.value)
                    if (property === 'totals.discount.value') {
                        const current = parseFloat(this.totals.discount.value) || 0;
                        this.totals.discount.value = Math.max(0, current + step);
                        this.recalcTotals();
                        event.target.value = this.totals.discount.value;
                        return;
                    }
                    if (property === 'round_off') {
                        const current = parseFloat(this.round_off) || 0;
                        const stepRound = 0.01;
                        this.round_off = current + stepRound;
                        this.recalcTotals();
                        event.target.value = this.round_off;
                        return;
                    }
                    const current = parseFloat(this[property]) || 0;
                    this[property] = current + step;
                }
                // Update the input value
                event.target.value = item
                    ? (property.includes('.')
                        ? item[property.split('.')[0]][property.split('.')[1]]
                        : item[property])
                    : (property === 'totals.discount.value' ? this.totals.discount.value : property === 'round_off' ? this.round_off : this[property]);

                if (item && property === 'qty' && item.qty > item.max_qty) {
                    item.qty = item.max_qty;
                    event.target.value = item.qty;
                }
            },
            decrementValue(event, property, item, step = 1) {
                if (item) {
                    // For cart items
                    if (property.includes('.')) {
                        const parts = property.split('.');
                        const obj = item[parts[0]];
                        const key = parts[1];
                        const current = parseFloat(obj[key]) || 0;
                        const newValue = Math.max(0, current - step);
                        obj[key] = newValue;
                        if (property === 'discount.percent' || property === 'discount.fixed') {
                            this.onItemDiscountChange(item, key);
                        } else {
                            this.recalcItem(item);
                        }
                        event.target.value = newValue;
                    } else {
                        const current = property === 'qty'
                            ? (parseInt(item[property]) || 0)
                            : (parseFloat(item[property]) || 0);
                        const newValue = Math.max(0, current - step);
                        item[property] = newValue;
                        if (property === 'qty' || property === 'unit_price') {
                            this.recalcItem(item);
                        }
                        event.target.value = newValue;
                    }
                } else {
                    // For data properties (including nested totals.discount.value)
                    if (property === 'totals.discount.value') {
                        const current = parseFloat(this.totals.discount.value) || 0;
                        const newValue = Math.max(0, current - step);
                        this.totals.discount.value = newValue;
                        this.recalcTotals();
                        event.target.value = newValue;
                        return;
                    }
                    if (property === 'round_off') {
                        const current = parseFloat(this.round_off) || 0;
                        const stepRound = 0.01;
                        const newValue = current - stepRound;
                        this.round_off = newValue;
                        this.recalcTotals();
                        event.target.value = this.round_off;
                        return;
                    }
                    const current = parseFloat(this[property]) || 0;
                    const newValue = Math.max(0, current - step);
                    this[property] = newValue;
                    event.target.value = newValue;
                }

                if (item && property === 'qty' && item.qty > item.max_qty) {
                    item.qty = item.max_qty;
                    event.target.value = item.qty;
                }
            },

            // Payment input handlers
            getPaymentMaxAmount(method) {
                // Calculate remaining due amount
                const otherPayments = this.paymentMethods.reduce((sum, m) => {
                    if (m.id !== method.id && m.selected) {
                        return sum + (parseFloat(m.amount) || 0);
                    }
                    return sum;
                }, 0);

                const advance = this.useAdvance ? (Number(this.advanceAmount) || 0) : 0;

                const remaining = this.totals.grand_total - otherPayments - advance;
                return Math.max(0, remaining);
            },
            updatePaymentValue(event, method) {
                let value = parseFloat(event.target.value) || 0;
                const maxAmount = this.getPaymentMaxAmount(method);

                // Limit to remaining due amount
                if (value > maxAmount) {
                    value = maxAmount;
                    event.target.value = value;
                }

                // Ensure non-negative
                value = Math.max(0, value);
                method.amount = value;

                // Recalculate to ensure total doesn't exceed grand total
                // Get total of all other payment methods
                const otherPayments = this.paymentMethods.reduce((sum, m) => {
                    if (m.id !== method.id && m.selected) {
                        return sum + (parseFloat(m.amount) || 0);
                    }
                    return sum;
                }, 0);

                const advance = this.useAdvance ? (Number(this.advanceAmount) || 0) : 0;

                const totalPaid = otherPayments + method.amount + advance;

                // If total exceeds grand total, adjust this method
                if (totalPaid > this.totals.grand_total) {
                    const excess = totalPaid - this.totals.grand_total;
                    method.amount = Math.max(0, method.amount - excess);
                    event.target.value = method.amount;
                }
            },
            incrementPaymentValue(event, method, step = 1) {
                const current = parseFloat(method.amount) || 0;
                const maxAmount = this.getPaymentMaxAmount(method);
                const newValue = Math.min(maxAmount, current + step);
                method.amount = newValue;
                event.target.value = newValue;
            },
            decrementPaymentValue(event, method, step = 1) {
                const current = parseFloat(method.amount) || 0;
                const newValue = Math.max(0, current - step);
                method.amount = newValue;
                event.target.value = newValue;
            },
            onPaymentFocus(event, method) {
                const dueAmount = this.getPaymentMaxAmount(method);
                method.amount = dueAmount;
                event.target.value = dueAmount;
                this.$nextTick(() => {
                    event.target.select();
                });
            },
            onAdvanceCheckboxChange() {
                if (this.useAdvance) {
                    // Calculate default advance amount
                    const basePayments = this.paymentMethods.reduce((sum, m) => {
                        return sum + (m.selected ? Number(m.amount || 0) : 0);
                    }, 0);
                    const dueAmount = this.totals.grand_total - basePayments;
                    const availableAdvance = this.selectedCustomer && this.selectedCustomer.advance
                        ? Number(this.selectedCustomer.advance)
                        : 0;

                    // Default: due amount if customer has enough advance, otherwise available advance
                    if (dueAmount > 0 && availableAdvance > 0) {
                        this.advanceAmount = Math.min(dueAmount, availableAdvance);
                    } else {
                        this.advanceAmount = 0;
                    }
                } else {
                    this.advanceAmount = 0;
                }
                this.recalcTotals();
            },
            updateAdvanceAmount(event) {
                let value = parseFloat(event.target.value) || 0;
                const availableAdvance = this.selectedCustomer && this.selectedCustomer.advance
                    ? Number(this.selectedCustomer.advance)
                    : 0;

                // Limit to available advance
                if (value > availableAdvance) {
                    value = availableAdvance;
                    event.target.value = value;
                }

                // Ensure non-negative
                value = Math.max(0, value);
                this.advanceAmount = value;

                // Recalculate totals
                this.recalcTotals();
            },
            onAdvanceFocus(event) {
                const basePayments = this.paymentMethods.reduce((sum, m) => {
                    return sum + (m.selected ? Number(m.amount || 0) : 0);
                }, 0);
                const dueAmount = this.totals.grand_total - basePayments;
                const availableAdvance = this.selectedCustomer && this.selectedCustomer.advance
                    ? Number(this.selectedCustomer.advance)
                    : 0;

                // Set to due amount if customer has enough, otherwise available advance
                if (dueAmount > 0 && availableAdvance > 0) {
                    this.advanceAmount = Math.min(dueAmount, availableAdvance);
                } else if (availableAdvance > 0) {
                    this.advanceAmount = availableAdvance;
                } else {
                    this.advanceAmount = 0;
                }

                event.target.value = this.advanceAmount;
                this.$nextTick(() => {
                    event.target.select();
                });
            },
            serializeOrderState() {
                return {
                    timestamp: new Date().toISOString(),
                    cart: this.cart,
                    totals: this.totals,
                    customer: this.selectedCustomer,
                    coupon: this.coupon,
                    extra_charge: this.extra_charge,
                    delivery_charge: this.delivery_charge,
                    round_off: this.round_off,
                    selectedWarehouseId: this.selectedWarehouseId,
                    order_note: this.orderNote,
                };
            },
            checkForSavedOrder(autoPrompt = false) {
                if (!window.localStorage) return;
                try {
                    const raw = window.localStorage.getItem(LOCAL_SAVE_KEY);
                    if (!raw) {
                        this.hasSavedDraft = false;
                        return;
                    }
                    const data = JSON.parse(raw);
                    if (!data || !Array.isArray(data.cart) || !data.cart.length) {
                        this.hasSavedDraft = false;
                        return;
                    }
                    this.hasSavedDraft = true;
                    if (autoPrompt && !this.cart.length) {
                        this.s_confirm('A locally saved POS order was found. Restore it now?').then((shouldRestore) => {
                            if (shouldRestore) {
                                this.applySavedOrder(data);
                            }
                        });
                    }
                } catch (e) {
                    console.warn('Unable to restore saved POS order', e);
                }
            },
            restoreSavedOrder() {
                if (!window.localStorage) return;
                try {
                    const raw = window.localStorage.getItem(LOCAL_SAVE_KEY);
                    if (!raw) {
                        this.s_alert('No saved order found.', 'info');
                        this.hasSavedDraft = false;
                        return;
                    }
                    const data = JSON.parse(raw);
                    if (!data || !Array.isArray(data.cart) || !data.cart.length) {
                        this.s_alert('Saved order is empty or invalid.', 'warning');
                        this.hasSavedDraft = false;
                        return;
                    }
                    this.applySavedOrder(data);
                } catch (e) {
                    console.warn('Unable to restore saved POS order', e);
                    this.s_alert('Failed to restore saved order.', 'error');
                }
            },
            applySavedOrder(data) {
                this.cart = data.cart || [];
                this.totals = data.totals || this.totals;
                this.selectedCustomer = data.customer || null;
                this.coupon = data.coupon || this.coupon;
                this.extra_charge = data.extra_charge || 0;
                this.delivery_charge = data.delivery_charge || 0;
                this.round_off = data.round_off || 0;
                if (data.selectedWarehouseId) {
                    this.selectedWarehouseId = data.selectedWarehouseId;
                }
                this.orderNote = data.order_note || '';
                this.recalcTotals();
                this.hasSavedDraft = false;
            },
            clearSavedOrder() {
                if (!window.localStorage) return;
                try {
                    window.localStorage.removeItem(LOCAL_SAVE_KEY);
                    this.hasSavedDraft = false;
                } catch (e) {
                    console.warn('Unable to clear saved POS order', e);
                }
            },

            // PREVIEW & PRINT
            previewOrder() {
                if (!routes.preview) return;
                const slug = this.currentOrderSlug || null;
                let payload;
                if (slug) {
                    payload = { order_slug: slug };
                } else {
                    if (!this.cart.length) {
                        this.s_alert('Cart is empty', 'warning');
                        return;
                    }
                    payload = {
                        cart: this.cart,
                        totals: this.totals,
                        customer: this.selectedCustomer,
                        order_note: this.orderNote,
                    };
                }
                this.post(routes.preview, payload)
                    .then((r) => {
                        const html = r.data && r.data.data ? r.data.data.html : null;
                        if (!html) return;
                        const w = window.open('', '_blank', 'width=900,height=700');
                        w.document.write(html);
                    })
                    .catch(() => { });
            },
            printPosPreview() {
                if (!routes.print) return;
                const slug = this.currentOrderSlug || null;
                if (!slug) {
                    this.s_alert('Order not created yet. Submit first to print.', 'warning');
                    return;
                }
                const url = routes.print.replace('__SLUG__', slug);
                this.get(url)
                    .then((r) => {
                        const html = r.data && r.data.data ? r.data.data.html : null;
                        if (!html) return;
                        const w = window.open('', '_blank', 'width=900,height=700');
                        w.document.write(html);
                        w.print();
                    })
                    .catch(() => { });
            },
            printA4Preview() {
                const slug = this.currentOrderSlug || null;
                if (!slug || !routes.invoiceUrlBase) {
                    this.s_alert('Order not created yet. Submit first to print.', 'warning');
                    return;
                }
                const url = routes.invoiceUrlBase.replace('__SLUG__', slug);
                window.open(url, '_blank');
            },

            // SUBMIT ORDER
            submitOrder() {
                if (!routes.createOrder) return;
                if (!this.cart.length) {
                    this.s_alert('Cart is empty', 'warning');
                    return;
                }
                if (!this.selectedCustomer) {
                    this.s_alert('Select or create a customer before submitting.', 'warning');
                    return;
                }

                this.loading.order = true;
                const payload = {
                    cart: this.cart,
                    totals: {
                        ...this.totals,
                        paid: this.paymentTotal,
                        due: this.totals.grand_total - this.paymentTotal,
                        total_purchase_price: this.totalPurchasePrice,
                    },
                    customer: this.selectedCustomer,
                    payments: this.paymentMethods
                        .filter((m) => m.selected && m.amount > 0)
                        .map((m) => ({
                            method: m.id,
                            amount: m.amount,
                            payment_type_id: m.payment_type_id || null
                        })),
                    use_advance: this.useAdvance,
                    advance_amount: this.useAdvance ? this.advanceAmount : 0,
                    order_note: this.orderNote,
                    order_source: 'pos',
                    delivery_info: this.delivery_info,
                    order_status: this.order_status,
                };

                this.post(routes.createOrder, payload)
                    .then((r) => {
                        if (r.data && r.data.success) {
                            const data = r.data.data || {};
                            this.currentOrderSlug = data.order_slug;
                            this.clearCart();
                            if (routes.print && data.order_slug) {
                                const url = routes.print.replace('__SLUG__', data.order_slug);
                                this.get(url)
                                    .then((resp) => {
                                        const html = resp.data && resp.data.data ? resp.data.data.html : null;
                                        if (!html) return;
                                        const w = window.open('', '_blank', 'width=900,height=700');
                                        w.document.write(html);
                                        w.print();
                                    })
                                    .catch(() => { });
                            }
                        } else {
                            this.s_alert((r.data && r.data.message) || 'Order creation failed', 'error');
                        }
                    })
                    .catch((e) => {
                        const msg = (e.response && e.response.data && e.response.data.message)
                            ? e.response.data.message
                            : (e.message || 'Unknown error');
                        this.s_alert('Order creation failed', 'error', msg);
                    })
                    .finally(() => {
                        this.loading.order = false;
                    });
            },

            /**
             * Calculate Steadfast Courier delivery charge
             * @param {boolean} inside_city - true = Inside Dhaka, false = Outside Dhaka
             * @param {number} outside_city - this will not be used if inside_city is true (for clarity)
             * @param {number[]} weights - weight array (kg), e.g. [0.5, 1.2, 3]
             * @returns {number} total delivery charge (only courier charge, COD 1% extra to add)
             */
            getSteadfastDeliveryCharge(inside_city, weights) {
                // base charge (1kg / 1kg)
                const baseInside = 70;
                const baseOutside = 130;

                // extra charge per kg (approx 20-25 taka)
                const extraPerKg = 20;

                const minCharge = 0; // 50 or 80 

                let total = 0;

                weights.forEach(weight => {
                    let charge = 0;

                    if (weight <= 0) return; // invalid skip

                    const base = inside_city ? baseInside : baseOutside;

                    if (weight <= 1) {
                        charge = base;
                    } else {
                        const extraKg = weight - 1;
                        charge = base + (Math.ceil(extraKg) * extraPerKg);
                    }

                    total += Math.max(charge, minCharge);
                });

                return total;
            },
            setDeliveryChargeByType() {
                const type = this.delivery_info.delivery_charge_type;
                const weights = this.cart.map(item => (item.weight || .5) * item.qty);
                this.delivery_charge = this.getSteadfastDeliveryCharge(type === 'inside_city', weights);
            }
        },
    });
});

