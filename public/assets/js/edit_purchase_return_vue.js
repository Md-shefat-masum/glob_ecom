


function debounce(func, wait) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            func.apply(this, args);
        }, wait);
    };
}

window.onload = function () {
    new Vue({
        el: "#formApp",
        data: function () {
            return {
                searchQuery: "",
                searchResults: [],
                loadingMore: false,
                allProductsLoaded: false,

                rooms: [],
                cartoonsByRoom: {},
                selectedWarehouse: "",
                selectedRoom: "",
                selectedCartoon: "",
                selectedSupplier: null,
                purchaseDate: '',
                purchaseCode: '',
                purchaseItems: [],
                dropdownVisible: false,

                other_charges_input_amount: '',
                other_charges_type: '',
                discount_on_all: 0,
                discount_on_all_type: 'in_percentage',
                discount_to_all_amt: 0,
                other_charges_amt: 0,

                note: '',
                reference: '',
                subtotal_amt: 0,
                total_round_off_amt: 0,
                round_off_input: '0',
                orderStatus: 'pending',

                initializing: true,
                editData: null,
            };
        },
        async created() {
            await this.fetchEditData();
            await this.setOtherCharges();
            this.initializing = false;
        },
        computed: {
            totalQuantity() {
                return this.purchaseItems.length > 0
                    ? this.purchaseItems.reduce((total, item) => total + Number(item.quantity || 0), 0)
                    : 0;
            },
            subtotal() {
                return this.purchaseItems.reduce((total, item) => {
                    let price = Number(item.price || 0);
                    let quantity = Number(item.quantity || 0);
                    let discount = Number(item.discount || 0) / 100;
                    let tax = Number(item.tax || 0) / 100;

                    let totalBeforeTax = price * quantity * (1 - discount);
                    let totalWithTax = totalBeforeTax * (1 + tax);

                    return total + totalWithTax;
                }, 0);
            },
            pre_grand_total_amt() {
                return +this.subtotal + this.other_charges_amt - this.discount_to_all_amt;
            },
            grand_total_amt() {
                const deduction = Number(this.total_round_off_amt || 0);
                const adjusted = this.pre_grand_total_amt - deduction;
                return adjusted > 0 ? adjusted : 0;
            }
        },
        methods: {
            showNumericError(label) {
                if (typeof toastr !== 'undefined' && toastr && typeof toastr.error === 'function') {
                    toastr.error(`${label} must be a number`);
                }
            },

            sanitizeNumberValue(value, label) {
                if (value === '' || value === null || value === undefined) {
                    return 0;
                }

                const numeric = Number(value);
                if (Number.isNaN(numeric)) {
                    this.showNumericError(label);
                    return 0;
                }
                return numeric;
            },

            handleItemNumericInput(item, field, label) {
                const raw = item[field];
                if (raw === '' || raw === null || raw === undefined) {
                    this.$set(item, field, '');
                    // Reset stock_codes when quantity is cleared
                    if (field === 'quantity') {
                        this.$set(item, 'stock_codes', []);
                    }
                    return;
                }
                
                // Initialize stock_codes array when quantity changes
                if (field === 'quantity') {
                    const quantity = Number(raw) || 0;
                    if (!item.stock_codes || !Array.isArray(item.stock_codes)) {
                        this.$set(item, 'stock_codes', []);
                    }
                    // Ensure stock_codes array has enough elements (0-based array for 1-based v-for)
                    // v-for="i in quantity" gives i=1,2,3... so we need indices 0,1,2...
                    while (item.stock_codes.length < quantity) {
                        item.stock_codes.push('');
                    }
                    // Remove excess elements if quantity decreased
                    if (item.stock_codes.length > quantity) {
                        item.stock_codes = item.stock_codes.slice(0, quantity);
                    }
                }

                const numeric = Number(raw);
                if (Number.isNaN(numeric)) {
                    this.showNumericError(label);
                    this.$set(item, field, 0);
                } else {
                    this.$set(item, field, numeric);
                }
            },

            handleDiscountOnAllInput() {
                const numeric = this.sanitizeNumberValue(this.discount_on_all, 'Discount on All');
                this.discount_on_all = numeric;
                this.updateDiscountTotals();
            },

            updateDiscountTotals() {
                const baseDiscount = this.sanitizeNumberValue(this.discount_on_all, 'Discount on All');
                this.discount_on_all = baseDiscount;

                let discount_all = 0;
                if (this.discount_on_all_type === 'in_percentage') {
                    discount_all = (baseDiscount / 100) * this.subtotal;
                } else if (this.discount_on_all_type === 'in_fixed') {
                    discount_all = baseDiscount;
                }

                this.discount_to_all_amt = Number(discount_all) || 0;
            },

            handleRoundOffInput() {
                if (this.round_off_input === '' || this.round_off_input === null || this.round_off_input === undefined) {
                    this.total_round_off_amt = 0;
                    return;
                }

                const numeric = Number(this.round_off_input);
                if (Number.isNaN(numeric)) {
                    this.showNumericError('Round Off');
                    this.round_off_input = '0';
                    this.total_round_off_amt = 0;
                    return;
                }

                this.total_round_off_amt = numeric;
                this.round_off_input = numeric.toString();
            },

            getData() {
                if (this.searchQuery.length > 1) {
                    this.fetchProducts();
                    this.dropdownVisible = true;
                } else {
                    this.searchResults = [];
                    this.dropdownVisible = false;
                }
            },

            async fetchEditData() {
                const path = window.location.pathname;
                const slug = path.split('/').pop();

                try {
                    const response = await axios.get(`${location.origin}/api/edit/purchase-return/order/${slug}`);
                    this.editData = response.data.data;

                    this.selectedWarehouse = this.editData.product_warehouse_id;
                    this.selectedSupplier = this.editData.product_supplier_id;
                    this.purchaseDate = this.editData.date;
                    this.purchaseCode = this.editData.purchase_code;
                    this.reference = this.editData.reference || '';
                    this.orderStatus = this.editData.order_status || 'pending';
                    this.other_charges_input_amount = this.editData.other_charge_percentage;
                    this.other_charges_type = this.editData.other_charge_type;
                    this.discount_on_all = Number(this.editData.discount_amount) || 0;
                    this.note = this.editData.note;
                    this.subtotal_amt = Number(this.editData.subtotal) || 0;
                    this.other_charges_amt = Number(this.editData.other_charge_amount) || 0;
                    this.discount_to_all_amt = Number(this.editData.calculated_discount_amount) || 0;
                    this.discount_on_all_type = this.editData.discount_type || 'in_percentage';
                    this.total_round_off_amt = Number(this.editData.round_off) || 0;
                    this.round_off_input = (Number(this.editData.round_off) || 0).toString();

                    await this.getRooms();

                    this.selectedRoom = this.editData.product_warehouse_room_id || '';
                    if (this.selectedRoom) {
                        await this.loadCartoonsForRoom(this.selectedRoom, true);
                        this.selectedCartoon = this.editData.product_warehouse_room_cartoon_id || '';
                    } else {
                        this.selectedCartoon = '';
                    }

                    this.purchaseItems = (this.editData.order_products || []).map(productItem => {
                        const variant = productItem.variant_combination;
                        const product = productItem.product;

                        const variantName = variant ? (variant.name || variant.combination_key || '') : '';
                        const displayName = variantName
                            ? `${productItem.product_name} (${variantName})`
                            : productItem.product_name;

                        const previousStock = productItem.previous_stock ?? (variant ? variant.stock : (product ? product.stock : 0));

                        // Ensure stock_codes is an array and matches quantity
                        let stockCodes = productItem.stock_codes || [];
                        if (!Array.isArray(stockCodes)) {
                            // Convert object to array if needed
                            stockCodes = Array.isArray(stockCodes) ? stockCodes : Object.values(stockCodes);
                        }
                        const quantity = Number(productItem.qty || 0);
                        // Ensure array length matches quantity
                        while (stockCodes.length < quantity) {
                            stockCodes.push('');
                        }
                        if (stockCodes.length > quantity) {
                            stockCodes = stockCodes.slice(0, quantity);
                        }

                        return {
                            rowKey: `${productItem.product_id}-${variant ? variant.id : 'product'}`,
                            isVisible: true,
                            product_id: productItem.product_id,
                            stock_codes: stockCodes,
                            id: productItem.product_id,
                            variant_combination_id: variant ? variant.id : null,
                            has_variant: product ? product.has_variant : (variant ? true : false),
                            name: productItem.product_name,
                            display_name: displayName,
                            price: Number(productItem.product_price || 0),
                            quantity: quantity,
                            discount: Number(productItem.discount_amount || 0),
                            tax: Number(productItem.tax || 0),
                            previous_stock: Number(previousStock || 0),
                            warehouse_room_id: productItem.product_warehouse_room_id || '',
                            warehouse_cartoon_id: productItem.product_warehouse_room_cartoon_id || '',
                            cartoonOptions: [],
                        };
                    });

                    await Promise.all(this.purchaseItems.map(item => {
                        if (item.warehouse_room_id) {
                            return this.loadCartoonsForRoom(item.warehouse_room_id, true).then(cartoons => {
                                item.cartoonOptions = cartoons;
                                if (!cartoons.find(cartoon => Number(cartoon.id) === Number(item.warehouse_cartoon_id))) {
                                    item.warehouse_cartoon_id = '';
                                }
                            });
                        }
                        return Promise.resolve();
                    }));

                    this.updateDiscountTotals();
                    this.handleRoundOffInput();

                    this.dropdownVisible = false;
                } catch (error) {
                    console.error("Error fetching edit data:", error);
                }
            },

            async setOtherCharges() {
                if (!this.editData || !this.editData.other_charge_type) {
                    return;
                }

                try {
                    const decoded = JSON.parse(this.editData.other_charge_type) || [];
                    const otherChargeTitles = Array.from(document.querySelectorAll('.other_charges_title'));

                    otherChargeTitles.forEach((element, index) => {
                        const match = decoded.find(item => item.title === element.value);
                        if (!match) {
                            return;
                        }
                        const typeSelect = document.querySelector('.other_charges_type' + index);
                        const amountInput = document.querySelector('.other_charges_amount' + index);
                        if (amountInput) {
                            amountInput.value = match.amount;
                        }
                        if (typeSelect) {
                            typeSelect.value = match.type;
                        }
                    });

                    this.calc_other_charges();
                } catch (error) {
                    console.error("Error parsing other charges JSON:", error);
                }
            },

            calc_other_charges() {
                const subtotal = this.subtotal;
                let percentTotal = 0;
                let fixedTotal = 0;

                const otherChargeInputs = Array.from(document.querySelectorAll('.other_charges_amount'));
                otherChargeInputs.forEach((element, index) => {
                    const typeSelect = document.querySelector('.other_charges_type' + index);
                    if (!typeSelect) {
                        return;
                    }

                    const rawValue = element.value;
                    if (rawValue === '' || rawValue === null || rawValue === undefined) {
                        return;
                    }

                    let value = Number(rawValue);
                    if (Number.isNaN(value)) {
                        this.showNumericError('Other Charge Amount');
                        element.value = 0;
                        value = 0;
                    }

                    if (typeSelect.value === "percent") {
                        percentTotal += (subtotal * value) / 100;
                    } else {
                        fixedTotal += value;
                    }
                });

                this.other_charges_amt = percentTotal + fixedTotal;
            },

            hideDropdown(event) {
                if (
                    this.$refs.searchDropdown &&
                    !this.$refs.searchDropdown.contains(event.target) &&
                    !this.$refs.searchInput.contains(event.target)
                ) {
                    this.dropdownVisible = false;
                }
            },

            toggleDropdown() {
                this.dropdownVisible = !this.dropdownVisible;
            },

            fetchProducts: debounce(function () {
                this.loadingMore = true;
                axios.post(
                        `/api/search/products?query=${this.searchQuery}`,
                        {},
                        {
                            headers: {
                                Authorization: 'GenericCommerceV1-SBW7583837NUDD82'
                            }
                        }
                    )
                    .then(response => {
                        try {
                            this.searchResults = response.data.data.data;
                        } catch (e) {
                            this.searchResults = [];
                        }
                        this.allProductsLoaded = true;
                    })
                    .catch(error => {
                        console.log("Error fetching products:", error);
                    })
                    .finally(() => {
                        this.loadingMore = false;
                    });
            }, 500),

            getRooms() {
                if (!this.selectedWarehouse) {
                    this.rooms = [];
                    this.cartoonsByRoom = {};
                    this.selectedRoom = '';
                    this.selectedCartoon = '';
                    return Promise.resolve();
                }

                return axios.get(`/api/get-rooms/${this.selectedWarehouse}`)
                    .then(response => {
                        this.rooms = response.data || [];
                        this.cartoonsByRoom = {};
                        this.selectedRoom = '';
                        this.selectedCartoon = '';
                    })
                    .catch(error => {
                        console.error("Error fetching rooms:", error);
                    });
            },

            loadCartoonsForRoom(roomId, silent = false) {
                if (!roomId || !this.selectedWarehouse) {
                    return Promise.resolve([]);
                }

                if (this.cartoonsByRoom[roomId]) {
                    return Promise.resolve(this.cartoonsByRoom[roomId]);
                }

                return axios.get(`/api/get-cartoons/${this.selectedWarehouse}/${roomId}`)
                    .then(response => {
                        const cartoons = response.data || [];
                        this.$set(this.cartoonsByRoom, roomId, cartoons);
                        return cartoons;
                    })
                    .catch(error => {
                        if (!silent) {
                            console.error("Error fetching cartoons:", error);
                        }
                        return [];
                    });
            },

            addRow(product) {
                if (!product || !product.id) {
                    return;
                }

                this.purchaseItems = this.purchaseItems.filter(item => item.product_id !== product.id);

                if (product.has_variant && Array.isArray(product.variants) && product.variants.length > 0) {
                    product.variants.forEach(variant => {
                        const row = this.createPurchaseRow(product, variant);
                        this.purchaseItems.push(row);
                        if (row.warehouse_room_id) {
                            this.loadCartoonsForRoom(row.warehouse_room_id, true).then(cartoons => {
                                row.cartoonOptions = cartoons;
                            });
                        }
                    });
                } else {
                    const row = this.createPurchaseRow(product, null);
                    this.purchaseItems.push(row);
                }

                this.searchQuery = '';
                this.dropdownVisible = false;
            },

            createPurchaseRow(product, variant = null) {
                const isVariant = !!variant;
                const price = isVariant
                    ? Number(variant.price ?? product.price ?? 0)
                    : Number(product.price ?? 0);

                const displayName = isVariant
                    ? `${product.name} (${variant.name || variant.combination_key || 'Variant'})`
                    : product.name;

                return {
                    rowKey: `${product.id}-${variant ? 'variant-' + variant.id : 'product'}`,
                    isVisible: true,
                    product_id: product.id,
                    id: product.id,
                    variant_combination_id: variant ? variant.id : null,
                    has_variant: product.has_variant,
                    name: product.name,
                    display_name: displayName,
                    price: price,
                    quantity: 0,
                    discount: 0,
                    tax: 0,
                    previous_stock: isVariant ? Number(variant.previous_stock ?? variant.stock ?? 0) : Number(product.previous_stock ?? product.stock ?? 0),
                    warehouse_room_id: '',
                    warehouse_cartoon_id: '',
                    cartoonOptions: [],
                };
            },

            removeRow(index) {
                this.purchaseItems.splice(index, 1);
            },

            getItemTotalPrice(item) {
                const quantity = Number(item.quantity || 0);
                const price = Number(item.price || 0);
                let total = quantity * price;

                if (item.discount > 0) {
                    let discountAmount = (Number(item.discount) / 100) * total;
                    total -= discountAmount;
                }

                if (item.tax > 0) {
                    let taxAmount = (Number(item.tax) / 100) * total;
                    total += taxAmount;
                }

                return total;
            },

            onWarehouseChange() {
                this.getRooms();
                this.purchaseItems.forEach(item => {
                    item.warehouse_room_id = '';
                    item.warehouse_cartoon_id = '';
                    item.cartoonOptions = [];
                });
            },

            onRoomChange() {
                this.selectedCartoon = '';
                if (this.selectedRoom) {
                    this.loadCartoonsForRoom(this.selectedRoom, true);
                }
            },

            onItemRoomChange(item) {
                if (!item.warehouse_room_id) {
                    item.warehouse_cartoon_id = '';
                    item.cartoonOptions = [];
                    return;
                }

                this.loadCartoonsForRoom(item.warehouse_room_id).then(cartoons => {
                    item.cartoonOptions = cartoons;
                    if (!cartoons.find(cartoon => Number(cartoon.id) === Number(item.warehouse_cartoon_id))) {
                        item.warehouse_cartoon_id = '';
                    }
                });
            },
        },
        watch: {
            searchQuery(newQuery) {
                if (newQuery.trim() === "") {
                    this.searchResults = [];
                    this.allProductsLoaded = false;
                    this.dropdownVisible = false;
                } else {
                    this.getData();
                }
            },

            selectedWarehouse(newWarehouse, oldWarehouse) {
                if (this.initializing) {
                    return;
                }
                this.onWarehouseChange();
            },

            selectedRoom(newRoom, oldRoom) {
                if (this.initializing) {
                    return;
                }
                this.onRoomChange();
            },

            discount_on_all_type() {
                this.updateDiscountTotals();
            }
        },
        mounted() {
            window.addEventListener("click", this.hideDropdown);
        },
        beforeDestroy() {
            window.removeEventListener("click", this.hideDropdown);
        }
    });
};






