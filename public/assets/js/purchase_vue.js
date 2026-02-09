
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

                warehouses: [],
                rooms: [],
                cartoonsByRoom: {},
                selectedWarehouse: "",
                selectedRoom: "",
                selectedCartoon: "",
                purchaseItems: [ ],
                subtotalAmt: 0,
                finalTotal: 0,
                dropdownVisible: false,

                other_charges_input_amount: '',
                other_charges_type: '',
                discount_on_all: '0',
                discount_on_all_type: 'in_percentage',

                other_charges_amt: 0,
                discount_to_all_amt: 0,

                total_round_off_amt: 0,
                round_off_input: '0',


            };
        },
        computed: {
            totalProducts() {
                return this.purchaseItems.length;
            },
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
                const adjustedTotal = this.pre_grand_total_amt - deduction;
                return adjustedTotal > 0 ? adjustedTotal : 0;
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

            calc_other_charges() {
                // console.log(this.subtotal);
                
                const subtotal = this.subtotal; 
                let percentTotal = 0;
                let fixedTotal = 0;

                var other_charges_amount = document.querySelectorAll('.other_charges_amount');
                other_charges_amount = [...other_charges_amount];
                other_charges_amount.forEach((element, index) => {
                    var type = document.querySelector('.other_charges_type' + index);
                    var rawValue = element.value;

                    if (rawValue === '' || rawValue === null || rawValue === undefined) {
                        return;
                    }

                    var value = Number(rawValue);
                    if (Number.isNaN(value)) {
                        this.showNumericError('Other Charge Amount');
                        element.value = 0;
                        value = 0;
                    }

                    if (type && type.value === "percent") {
                        percentTotal += (subtotal * value) / 100;
                    } else {
                        fixedTotal += value;
                    }
                });
                this.other_charges_amt = percentTotal + fixedTotal;

            },

            hideDropdown(event) {
                // Ensure the dropdown is hidden when clicking anywhere outside
                if (
                    this.$refs.searchDropdown && 
                    !this.$refs.searchDropdown.contains(event.target) && 
                    !this.$refs.searchInput.contains(event.target)
                ) {
                    this.dropdownVisible = false;
                }
            },

            toggleDropdown() {
                // Manually toggle the dropdown visibility
                this.dropdownVisible = !this.dropdownVisible;
            },

            fetchProducts: debounce(function() {
                this.loadingMore = true;
                axios.post(
                      `/api/search/products?query=${this.searchQuery}`,
                      {}, //inputs body
                      {
                        headers: {
                          Authorization: 'GenericCommerceV1-SBW7583837NUDD82'
                        }
                      }
                    )
                    .then(response => {
                        console.log(response.data.data)
                        try{
                            this.searchResults = response.data.data.data;
                        }catch(e){
                            
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
                if (this.selectedWarehouse) {
                    axios.get(`/api/get-rooms/${this.selectedWarehouse}`).then((response) => {
                        this.rooms = response.data;
                        this.cartoonsByRoom = {};
                        this.selectedRoom = '';
                        this.selectedCartoon = '';
                    }).catch(error => {
                        console.error("Error fetching rooms:", error);
                    });
                } else {
                    this.rooms = [];
                    this.cartoonsByRoom = {};
                    this.selectedRoom = '';
                    this.selectedCartoon = '';
                }
            },

            getCartoons() {
                if (this.selectedRoom) {
                    this.selectedCartoon = '';
                    this.loadCartoonsForRoom(this.selectedRoom);
                } else {
                    this.selectedCartoon = '';
                }
            },

            addRow(product) {
                if (!product || !product.id) {
                    return;
                }

                // Remove existing entries for the same product to avoid duplicates
                this.purchaseItems = this.purchaseItems.filter(item => item.product_id !== product.id);

                if (product.has_variant && Array.isArray(product.variants) && product.variants.length > 0) {
                    product.variants.forEach(variant => {
                        const row = this.createPurchaseRow(product, variant);
                        this.purchaseItems.push(row);
                        if (row.warehouse_room_id) {
                            this.loadCartoonsForRoom(row.warehouse_room_id, true).then(() => {
                                if (!row.warehouse_cartoon_id) {
                                    return;
                                }
                                const cartoons = this.cartoonsByRoom[row.warehouse_room_id] || [];
                                this.$set(row, 'cartoonOptions', cartoons);
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

            removeRow(index) {
                this.purchaseItems.splice(index, 1);
            },

            getItemTotalPrice(item) {
                const quantity = Number(item.quantity || 0);
                const price = Number(item.price || 0);
                let total = quantity * price;

                if(item.discount > 0) {
                    let discountAmount = (Number(item.discount) / 100) * total;
                    total -=  discountAmount;
                }

                if(item.tax > 0) {
                    let taxAmount = (Number(item.tax) / 100) * total;                    
                    total += taxAmount;                            
                }
                
                return total;
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
                    stock_codes: [],
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
                    warehouse_room_id: variant ? variant.warehouse_room_id || '' : '',
                    warehouse_cartoon_id: variant ? variant.warehouse_cartoon_id || '' : '',
                    cartoonOptions: [],
                };
            },

            loadCartoonsForRoom(roomId, silent = false) {
                if (!roomId || !this.selectedWarehouse) {
                    return Promise.resolve([]);
                }

                if (this.cartoonsByRoom[roomId]) {
                    return Promise.resolve(this.cartoonsByRoom[roomId]);
                }

                return axios.get(`/api/get-cartoons/${this.selectedWarehouse}/${roomId}`).then((response) => {
                    const cartoons = response.data || [];
                    this.$set(this.cartoonsByRoom, roomId, cartoons);
                    return cartoons;
                }).catch(error => {
                    if (!silent) {
                        console.error("Error fetching cartoons:", error);
                    }
                    return [];
                });
            },

            onRowRoomChange(item) {
                if (!item.warehouse_room_id) {
                    item.warehouse_cartoon_id = '';
                    item.cartoonOptions = [];
                    return;
                }

                this.loadCartoonsForRoom(item.warehouse_room_id).then((cartoons) => {
                    item.cartoonOptions = cartoons;
                    if (!cartoons.find(cartoon => Number(cartoon.id) === Number(item.warehouse_cartoon_id))) {
                        item.warehouse_cartoon_id = '';
                    }
                });
            }



        },
        watch: {
            searchQuery: function (newQuery) {
                if (newQuery.trim() === "") {
                    this.searchResults = [];
                    this.allProductsLoaded = false;
                    this.dropdownVisible = false;
                } else {
                    this.getData();
                }
            },

            selectedWarehouse(newWarehouse) {
                this.selectedRoom = null;
                this.getRooms();
                this.cartoonsByRoom = {};
                this.selectedCartoon = '';
                this.purchaseItems.forEach(item => {
                    item.warehouse_room_id = '';
                    item.warehouse_cartoon_id = '';
                    item.cartoonOptions = [];
                });
            },
            selectedRoom(newRoom) {
                this.getCartoons();
            },

            discount_on_all_type() {
                this.updateDiscountTotals();
            }
           
        },

        mounted() {
            window.addEventListener("click", this.hideDropdown); // Listen for clicks on the window
            this.handleDiscountOnAllInput();
            this.handleRoundOffInput();
        },
        beforeDestroy() {
            window.removeEventListener("click", this.hideDropdown); // Cleanup event listener
        }
    });
};






